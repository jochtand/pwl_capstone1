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

    // ==========================================
    // FINAL BOSS: RUTE SAPU BERSIH TIKET KADALUARSA
    // ==========================================
    Route::post('/transactions/clear-expired', [TransactionController::class, 'clearExpiredTickets'])->name('transactions.clearExpired');
});

// ==========================================
// DASHBOARD (Pintu Masuk Setelah Login)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        $user = \Illuminate\Support\Facades\Auth::user();

        // JIKA ADMIN: Lempar ke Kelola Pengguna
        if ($user->role === 'admin') {
            return redirect()->route('admin.users');
        }

        // JIKA PANITIA: Tampilkan Dashboard Analitik
        if ($user->role === 'organizer') {
            $events = \App\Models\Event::where('organizer_id', $user->id)->get();
            $totalEvents = $events->count();

            $transactions = \App\Models\Transaction::with('ticketCategory.event')
                ->whereHas('ticketCategory.event', function ($query) use ($user) {
                    $query->where('organizer_id', $user->id);
                })->get();

            $paidTransactions = $transactions->where('payment_status', 'paid');
            $pendingTransactions = $transactions->where('payment_status', 'verifying');

            $totalTickets = $paidTransactions->count();
            $totalRevenue = $paidTransactions->sum('total_price');

            $eventAnalysis = $events->map(function ($event) use ($paidTransactions) {
                $sales = $paidTransactions->filter(function ($trx) use ($event) {
                    return $trx->ticketCategory->event_id == $event->id;
                });
                $event->tickets_sold = $sales->count();
                $event->revenue = $sales->sum('total_price');
                return $event;
            })->sortByDesc('tickets_sold');

            $monthlyRevenue = array_fill(1, 12, 0);
            foreach ($paidTransactions as $trx) {
                $month = (int)$trx->created_at->format('m');
                $year = $trx->created_at->format('Y');
                if ($year == date('Y')) {
                    $monthlyRevenue[$month] += $trx->total_price;
                }
            }
            $chartMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            $chartRevenueData = array_values($monthlyRevenue);

            return view('dashboard', compact(
                'events', 'totalEvents', 'totalTickets', 'totalRevenue',
                'pendingTransactions', 'eventAnalysis', 'chartMonths', 'chartRevenueData'
            ));
        }

        // JIKA USER BIASA: Langsung Tampilkan Katalog Event
        $events = \App\Models\Event::latest()->get();
        return view('dashboard', compact('events'));

    })->name('dashboard');
});
// ==========================================
// BAGIAN ORGANIZER (Panitia)
// ==========================================
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
    Route::put('/categories/{id}', [EventController::class, 'categoriesUpdate'])->name('categories.update');
    Route::delete('/categories/{id}', [EventController::class, 'categoriesDestroy'])->name('categories.destroy');

    // Rute Export Laporan Penjualan
    Route::get('/export-report', [TransactionController::class, 'exportReport'])->name('report.export');
});

// ==========================================
// SUPER ADMIN (CRUD USER & EVENT)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {
    // Kelola User
    Route::get('/admin/users', [AdminController::class, 'index'])->name('admin.users');
    Route::post('/admin/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{id}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroy'])->name('admin.users.destroy');

    // Kelola Event
    Route::get('/admin/events', [AdminController::class, 'events'])->name('admin.events');
    Route::post('/admin/events', [AdminController::class, 'storeEvent'])->name('admin.events.store');
    Route::put('/admin/events/{id}', [AdminController::class, 'updateEvent'])->name('admin.events.update');
    Route::delete('/admin/events/{id}', [AdminController::class, 'destroyEvent'])->name('admin.events.destroy');
});

require __DIR__.'/auth.php';
