<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketCategory;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\TicketMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http; // Tambahan untuk mengatasi error 403 QR Code

class TransactionController extends Controller
{
    // Menampilkan halaman ringkasan pesanan (Checkout)
    public function index($ticketId)
    {
        $ticket = TicketCategory::with('event')->findOrFail($ticketId);
        return view('checkout.index', compact('ticket'));
    }

    // Memproses pembelian tiket (Booking)
    public function store(Request $request, $ticketId)
    {
        $ticket = TicketCategory::findOrFail($ticketId);

        if ($ticket->quota < 1) {
            return back()->with('error', 'Maaf, kuota tiket ini sudah habis!');
        }

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

    // 1. User klik "Sudah Bayar"
    public function pay($id)
    {
        $transaction = Transaction::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $transaction->update(['payment_status' => 'verifying']);
        return back()->with('success', 'Konfirmasi terkirim! Menunggu validasi dari panitia.');
    }

    // 2. Panitia klik "Terima Pembayaran"
    public function approve($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update(['payment_status' => 'paid']);
        return back()->with('success', 'Pembayaran berhasil divalidasi! Tiket user sudah aktif.');
    }

    // 3. Panitia klik "Tolak Pembayaran"
    public function reject($id)
    {
        $transaction = Transaction::findOrFail($id);

        // Ubah status jadi gagal
        $transaction->update(['payment_status' => 'failed']);

        // KEMBALIKAN KUOTA TIKET KARENA BATAL!
        $ticket = $transaction->ticketCategory;
        $ticket->quota += 1;
        $ticket->save();

        return back()->with('success', 'Pembayaran ditolak! Kuota tiket telah dikembalikan ke sistem.');
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
}
