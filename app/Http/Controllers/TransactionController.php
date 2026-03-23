<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TicketCategory;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $transaction = Transaction::with('ticketCategory.event')->where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($transaction->payment_status !== 'paid') {
            abort(403);
        }

        // Download qr dan diubah ke base64
        $url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=TIKETAPP-TRX-" . $transaction->id . "-USER-" . Auth::id();

        try {
            $qrImageData = base64_encode(file_get_contents($url));
            $qrImage = 'data:image/png;base64,' . $qrImageData;
        } catch (\Exception $e) {
            $qrImage = null; // Backup jika gagal download
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.ticket', compact('transaction', 'qrImage'))
            ->setPaper('a4', 'portrait')
            ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf->download('E-Ticket_TRX-' . $transaction->id . '.pdf');
    }
}
