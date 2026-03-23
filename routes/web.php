<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\IsOrganizer;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;

// ==========================================
// HALAMAN UTAMA (Katalog Event)
// ==========================================
Route::get('/', function (Request $request) {
    $query = Event::query();

    if ($request->has('search') && $request->search != '') {
        $query->where('title', 'like', '%' . $request->search . '%')
            ->orWhere('location', 'like', '%' . $request->search . '%');
    }

    $events = $query->latest()->get();
    return view('welcome', compact('events'));
});

Route::get('/event/{id}', [EventController::class, 'show'])->name('events.show');

// Semua user yang sudah login bisa akses
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute Checkout Tiket
    Route::get('/checkout/{ticket}', [TransactionController::class, 'index'])->name('checkout');
    Route::post('/checkout/{ticket}', [TransactionController::class, 'store'])->name('checkout.process');

    // Rute Halaman Tiket Saya & Pembayaran
    Route::get('/my-tickets', [TransactionController::class, 'myTickets'])->name('my-tickets');
    Route::post('/my-tickets/{id}/pay', [TransactionController::class, 'pay'])->name('tickets.pay');
    Route::get('/tickets/{id}/download', [TransactionController::class, 'downloadPDF'])->name('tickets.download');

    // Rute Panitia Validasi Pembayaran
    Route::post('/transactions/{id}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
    Route::post('/transactions/{id}/reject', [TransactionController::class, 'reject'])->name('transactions.reject');
});

// ==========================================
// DASHBOARD (Pintu Masuk Utama Setelah Login)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        // 1. Jika Super Admin, langsung masuk
        if ($user->role === 'admin') {
            return redirect()->route('admin.users');
        }

        // 2. Jika Panitia, tampilkan analitik
        if ($user->role === 'organizer') {
            $events = Event::where('organizer_id', $user->id)->latest()->get();
            $totalEvents = $events->count();

            // Mengambil semua transaksi untuk event milik panitia ini
            $transactions = Transaction::whereHas('ticketCategory.event', function ($query) use ($user) {
                $query->where('organizer_id', $user->id);
            })->get();

            // Hanya hitung tiket dan pendapatan yang statusnya SUDAH LUNAS ('paid')
            $totalTickets = $transactions->where('payment_status', 'paid')->count();
            $totalRevenue = $transactions->where('payment_status', 'paid')->sum('total_price');

            // Yang butuh validasi adalah yang statusnya 'verifying'
            $pendingTransactions = $transactions->where('payment_status', 'verifying');

            return view('dashboard', compact('events', 'totalEvents', 'totalTickets', 'totalRevenue', 'pendingTransactions'));
        }

        // 3. Jika User Biasa, tampilkan dashboard biasa
        return view('dashboard');
    })->name('dashboard');
});

// Bagian Organizer
Route::middleware(['auth', 'verified', IsOrganizer::class])->group(function () {
    // Rute Kelola Event
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // Rute Kelola Kategori Tiket
    Route::get('/events/{event}/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::post('/events/{event}/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');

    // Rute Scan Tiket
    Route::get('/scan', [TransactionController::class, 'scan'])->name('scan.index');
    Route::post('/scan', [TransactionController::class, 'processScan'])->name('scan.process');

    // Rute Kelola Kategori
    Route::get('/categories', [EventController::class, 'categoriesIndex'])->name('categories.index');
    Route::post('/categories', [EventController::class, 'categoriesStore'])->name('categories.store');
    Route::delete('/categories/{id}', [EventController::class, 'categoriesDestroy'])->name('categories.destroy');
});

// Super Admin
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users');
    Route::put('/admin/users/{id}/role', [AdminController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
});

require __DIR__.'/auth.php';
