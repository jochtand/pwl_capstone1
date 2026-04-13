<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketCategory;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\TicketMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Models\WaitingList;

class TransactionController extends Controller
{
    // Menampilkan halaman ringkasan pesanan (Checkout)
    public function index($ticketId)
    {
        $ticket = TicketCategory::with('event')->findOrFail($ticketId);
        return view('checkout.index', compact('ticket'));
    }

    // Memproses pembelian tiket (Booking) & Logika Waiting List
    public function store(Request $request, $ticketId)
    {
        $ticket = TicketCategory::findOrFail($ticketId);

        // Jika tiket habis, masukkan ke Waiting List
        if ($ticket->quota < 1) {
            // Cek apakah user sudah ada di antrean biar gak double
            $alreadyWaiting = WaitingList::where('user_id', Auth::id())
                ->where('ticket_category_id', $ticket->id)
                ->exists();

            if ($alreadyWaiting) {
                return back()->with('error', 'Maaf, Anda sudah berada di dalam daftar antrean tiket ini.');
            }

            // Masukkan ke database antrean
            WaitingList::create([
                'user_id' => Auth::id(),
                'ticket_category_id' => $ticket->id,
                'event_id' => $ticket->event_id,
            ]);

            return back()->with('warning', 'Kuota habis! Anda telah dimasukkan ke dalam Waiting List. Kami akan mengalihkan tiket otomatis jika ada pembeli yang batal.');
        }

        // Jika kuota masih ada, proses normal
        Transaction::create([
            'user_id' => Auth::id(),
            'ticket_category_id' => $ticket->id,
            'total_price' => $ticket->price,
        ]);

        // Kurangi kuota (Booking)
        $ticket->quota -= 1;
        $ticket->save();

        return redirect()->route('my-tickets')->with('success', 'Tiket berhasil dipesan! Silakan selesaikan pembayaran.');
    }

    // Menampilkan halaman riwayat tiket milik User
    public function myTickets()
    {
        $transactions = Transaction::with(['ticketCategory.event'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('my-tickets', compact('transactions'));
    }

    // 1. User upload bukti dan klik "Kirim Bukti Pembayaran"
    public function pay(Request $request, $id)
    {
        // Validasi file yang diupload (wajib gambar, max 2MB)
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $transaction = Transaction::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // Proses penyimpanan gambar (JALAN PINTAS ANTI 403 FORBIDDEN)
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Simpan LANGSUNG ke folder public/payment_proofs (Tidak perlu storage:link)
            $file->move(public_path('payment_proofs'), $filename);

            // Update database
            $transaction->update([
                'payment_status' => 'verifying',
                'payment_proof' => $filename
            ]);
        }

        return back()->with('success', 'Bukti pembayaran berhasil terkirim! Menunggu validasi dari panitia.');
    }

    // 2. Panitia klik "Terima Pembayaran"
    public function approve($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update(['payment_status' => 'paid']);
        return back()->with('success', 'Pembayaran berhasil divalidasi! Tiket user sudah aktif.');
    }

    // 3. Panitia klik "Tolak Pembayaran" & Oper tiket ke antrean
    public function reject($id)
    {
        $transaction = Transaction::findOrFail($id);
        $ticket = $transaction->ticketCategory;

        // Ubah status transaksi awal jadi gagal
        $transaction->update(['payment_status' => 'failed']);

        // CEK WAITING LIST: Cari 1 orang yang paling lama ngantre (oldest)
        $nextInLine = WaitingList::where('ticket_category_id', $ticket->id)->oldest()->first();

        if ($nextInLine) {
            // ADA ANTREAN! Otomatis buatkan transaksi untuk orang ini
            Transaction::create([
                'user_id' => $nextInLine->user_id,
                'ticket_category_id' => $ticket->id,
                'total_price' => $ticket->price,
            ]);

            // Hapus orang tersebut dari daftar antrean karena sudah dapat tiket
            $nextInLine->delete();

            return back()->with('success', 'Pembayaran ditolak! Tiket langsung dialihkan secara otomatis ke antrean pertama di Waiting List.');
        } else {
            // TIDAK ADA ANTREAN. Kembalikan kuota tiket ke publik seperti biasa
            $ticket->quota += 1;
            $ticket->save();

            return back()->with('success', 'Pembayaran ditolak! Kuota tiket telah dikembalikan ke sistem.');
        }
    }

    // Menampilkan halaman Scanner
    public function scan()
    {
        if (Auth::user()->role !== 'organizer') {
            abort(403, 'Akses Ditolak! Hanya Panitia yang bisa melakukan scan tiket.');
        }
        return view('scan');
    }

    // Memproses ID Pesanan yang dimasukkan Panitia
    public function processScan(Request $request)
    {
        $request->validate(['ticket_id' => 'required']);

        $rawId = preg_replace('/[^0-9]/', '', $request->ticket_id);
        $id = (int) $rawId;

        $transaction = Transaction::with('ticketCategory.event')->find($id);

        if (!$transaction) {
            return back()->with('error', '❌ TIKET TIDAK DITEMUKAN! Pastikan ID Pesanan benar.');
        }

        if ($transaction->ticketCategory->event->organizer_id !== Auth::id()) {
            return back()->with('error', '❌ TIKET INVALID! Tiket ini bukan untuk event Anda.');
        }

        if ($transaction->payment_status !== 'paid') {
            return back()->with('warning', '⚠️ TIKET DITOLAK! Pembayaran belum lunas.');
        }

        if ($transaction->ticket_status === 'used') {
            return back()->with('error', '❌ TIKET HANGUS! Tiket ini sudah di-scan dan digunakan sebelumnya.');
        }

        $transaction->update(['ticket_status' => 'used']);

        return back()->with('success', '✅ AKSES DIBERIKAN! Tiket Valid untuk event: ' . $transaction->ticketCategory->event->title);
    }

    // Mencetak E-Ticket ke PDF
    public function downloadPDF($id)
    {

        set_time_limit(120);

        $transaction = Transaction::with('ticketCategory.event')->where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($transaction->payment_status !== 'paid') {
            abort(403);
        }

        $url = "https://quickchart.io/qr?text=TIKETAPP-TRX-" . $transaction->id . "-USER-" . Auth::id() . "&size=150";

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ])->get($url);

            if ($response->successful()) {
                $qrImageData = base64_encode($response->body());
                $qrImage = 'data:image/png;base64,' . $qrImageData;
            } else {
                $qrImage = null;
            }
        } catch (\Exception $e) {
            // Jika masuk ke sini, artinya request API gagal
            $qrImage = null;
        }

        // Mengirim email langsung ke alamat email pengguna yang login
        Mail::to(Auth::user()->email)->send(new TicketMail($transaction, $qrImage));

        // Merender tampilan PDF
        $pdf = Pdf::loadView('pdf.ticket', compact('transaction', 'qrImage'))
            ->setPaper('a4', 'portrait')
            ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf->download('E-Ticket_TiketApp_TRX-' . $transaction->id . '.pdf');
    }
    // Export Laporan Penjualan untuk Panitia
    public function exportReport()
    {
        $user = Auth::user();

        // Ambil semua transaksi lunas untuk event milik panitia ini
        $transactions = Transaction::with(['ticketCategory.event', 'user'])
            ->where('payment_status', 'paid')
            ->whereHas('ticketCategory.event', function ($query) use ($user) {
                $query->where('organizer_id', $user->id);
            })
            ->latest()
            ->get();

        $totalRevenue = $transactions->sum('total_price');

        $pdf = Pdf::loadView('pdf.report', compact('transactions', 'totalRevenue'))
            ->setPaper('a4', 'landscape'); // Pakai landscape biar tabelnya lega

        return $pdf->download('Laporan_Penjualan_TiketApp_' . date('Ymd') . '.pdf');
    }
    // ==========================================
    // Tiket Kadaluarsa
    // ==========================================
    public function clearExpiredTickets()
    {
        $user = Auth::user();

        // 1. Ambil semua transaksi yang statusnya 'pending' (Belum Dibayar) untuk event milik panitia ini
        $transactions = Transaction::with('ticketCategory')
            ->whereHas('ticketCategory.event', function ($query) use ($user) {
                $query->where('organizer_id', $user->id);
            })
            ->where('payment_status', 'pending')
            ->get();

        $expiredCount = 0;
        $shiftedCount = 0;

        foreach ($transactions as $transaction) {
            $isExpired = false;

            // Aturan 1: TIKET VIP/MAHAL (Harga >= 300.000) -> Kedaluwarsa dalam 1 Jam
            if ($transaction->total_price >= 300000) {
                // TIPS DEMO: Ubah diffInHours jadi diffInMinutes saat presentasi besok agar cepat batal
                if ($transaction->created_at->diffInMinutes(now()) >= 1) {
                    $isExpired = true;
                }
            }
            // Aturan 2: TIKET STANDARD (Harga < 300.000) -> Kedaluwarsa dalam 24 Jam
            else {
                if ($transaction->created_at->diffInHours(now()) >= 24) {
                    $isExpired = true;
                }
            }

            // Jika terbukti kedaluwarsa, lakukan Eksekusi!
            if ($isExpired) {
                // 1. Batalkan pesanan si pengantre palsu
                $transaction->update(['payment_status' => 'failed']);
                $expiredCount++;

                $ticket = $transaction->ticketCategory;

                // 2. Cek apakah ada pahlawan kesiangan di Waiting List
                $nextInLine = WaitingList::where('ticket_category_id', $ticket->id)->oldest()->first();

                if ($nextInLine) {
                    // ADA ANTREAN! Buatkan transaksi baru untuk orang pertama di antrean
                    Transaction::create([
                        'user_id' => $nextInLine->user_id,
                        'ticket_category_id' => $ticket->id,
                        'total_price' => $ticket->price,
                        'payment_status' => 'pending',
                    ]);
                    // Hapus orang tersebut dari daftar antrean
                    $nextInLine->delete();
                    $shiftedCount++;
                } else {
                    // TIDAK ADA ANTREAN. Kembalikan stok kuota tiket ke publik
                    $ticket->increment('stock');
                }
            }
        }

        if ($expiredCount > 0) {
            return back()->with('success', "🧹 Sapu Bersih Selesai! $expiredCount tiket kadaluarsa dibatalkan. $shiftedCount tiket otomatis dialihkan ke antrean Waiting List.");
        } else {
            return back()->with('success', "✅ Sistem aman. Tidak ada pesanan tiket yang melewati batas waktu saat ini.");
        }
    }
}
