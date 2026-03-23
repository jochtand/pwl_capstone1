<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-8 text-gray-900">

                    <!-- ========================================== -->
                    <!-- Notifikasi error sama sukses
                    <!-- ========================================== -->
                    @if(session('success'))
                        <div class="mb-8 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex items-center">
                            <span class="text-emerald-500 text-2xl mr-3">✅</span>
                            <p class="text-emerald-800 font-semibold tracking-wide">{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm flex items-center">
                            <span class="text-red-500 text-2xl mr-3">🚨</span>
                            <p class="text-red-800 font-semibold tracking-wide">{{ session('error') }}</p>
                        </div>
                    @endif
                    <!-- ========================================== -->

                    <!-- Hanya panitia yang bisa liat-->
                    @if(Auth::user()->role === 'organizer')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-indigo-500 hover:shadow-md transition">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Total Event Aktif</h3>
                                        <p class="text-3xl font-black text-gray-900">{{ $totalEvents }}<span class="text-sm font-medium text-gray-400 normal-case">Event</span></p>
                                    </div>
                                    <div class="bg-indigo-50 p-3 rounded-lg text-indigo-500 text-2xl">📅</div>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-emerald-500 hover:shadow-md transition">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Tiket Terjual</h3>
                                        <p class="text-3xl font-black text-gray-900">{{ $totalTickets }} <span class="text-sm font-medium text-gray-400 normal-case">Tiket</span></p>
                                    </div>
                                    <div class="bg-emerald-50 p-3 rounded-lg text-emerald-500 text-2xl">🎟️</div>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-amber-500 hover:shadow-md transition">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Estimasi Pendapatan</h3>
                                        <p class="text-3xl font-black text-gray-900"><span class="text-lg text-gray-500 mr-1">Rp</span>{{ number_format($totalRevenue, 0, ',', '.') }}</p>
                                    </div>
                                    <div class="bg-amber-50 p-3 rounded-lg text-amber-500 text-2xl">💰</div>
                                </div>
                            </div>

                        </div>

                        <div class="border-t border-gray-100 my-8"></div>

                        @if($pendingTransactions->count() > 0)
                            <div class="mb-10 bg-amber-50 rounded-xl border border-amber-200 p-6 shadow-sm">
                                <h3 class="text-lg font-bold text-amber-900 mb-4 flex items-center">
                                    <span class="mr-2">🔔</span> Butuh Validasi Pembayaran ({{ $pendingTransactions->count() }})
                                </h3>
                                <div class="bg-white rounded-lg shadow-sm border border-amber-100 overflow-hidden">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                        <tr class="bg-amber-100 text-amber-800 text-sm">
                                            <th class="p-3 font-semibold">ID Transaksi</th>
                                            <th class="p-3 font-semibold">Event</th>
                                            <th class="p-3 font-semibold">Total Transfer</th>
                                            <th class="p-3 font-semibold text-center">Aksi</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($pendingTransactions as $trx)
                                            <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                                                <td class="p-3 font-mono text-sm text-gray-600">#TRX-{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}</td>
                                                <td class="p-3 font-medium text-gray-800">{{ $trx->ticketCategory->event->title }}</td>
                                                <td class="p-3 font-bold text-emerald-600">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>

                                                <!-- PERUBAHAN DI SINI: DUA TOMBOL AKSI -->
                                                <td class="p-3 text-center">
                                                    <div class="flex items-center justify-center space-x-2">
                                                        <!-- Tombol Terima -->
                                                        <form action="{{ route('transactions.approve', $trx->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="bg-emerald-500 text-white px-3 py-1.5 rounded-md text-sm font-bold hover:bg-emerald-600 transition shadow-sm">
                                                                ✅ Terima
                                                            </button>
                                                        </form>

                                                        <!-- Tombol Tolak -->
                                                        <form action="{{ route('transactions.reject', $trx->id) }}" method="POST" onsubmit="return confirm('Tolak pembayaran dan kembalikan kuota tiket ini ke sistem?');">
                                                            @csrf
                                                            <button type="submit" class="bg-red-500 text-white px-3 py-1.5 rounded-md text-sm font-bold hover:bg-red-600 transition shadow-sm">
                                                                ❌ Tolak
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>

                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center mb-6 mt-4">
                            <h3 class="text-lg font-bold text-gray-800">Daftar Event Saya</h3>
                            <a href="{{ route('events.create') }}" class="bg-gray-900 text-white px-5 py-2.5 rounded-lg text-sm font-bold hover:bg-indigo-600 transition shadow-sm">
                                + Buat Event Baru
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse border border-gray-100 rounded-lg overflow-hidden">
                                <thead>
                                <tr class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wider">
                                    <th class="border-b py-4 px-4 font-bold">Nama Event</th>
                                    <th class="border-b py-4 px-4 font-bold">Kategori</th>
                                    <th class="border-b py-4 px-4 font-bold">Tanggal Mulai</th>
                                    <th class="border-b py-4 px-4 font-bold">Lokasi</th>
                                    <th class="border-b py-4 px-4 font-bold text-center">Aksi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($events as $event)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="border-b py-4 px-4 font-medium">{{ $event->title }}</td>
                                        <td class="border-b py-4 px-4">
                                            <span class="bg-indigo-50 text-indigo-700 text-xs px-3 py-1 rounded-full font-bold">{{ $event->category }}</span>
                                        </td>
                                        <td class="border-b py-4 px-4 text-gray-600 text-sm">{{ date('d M Y, H:i', strtotime($event->start_date)) }}</td>
                                        <td class="border-b py-4 px-4 text-gray-600 text-sm">{{ $event->location }}</td>
                                        <td class="border-b py-4 px-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a href="{{ route('tickets.index', $event->id) }}" class="text-emerald-700 hover:text-white hover:bg-emerald-600 font-bold text-xs bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-md transition">
                                                    🎟️ Tiket
                                                </a>
                                                <a href="{{ route('events.edit', $event->id) }}" class="text-blue-700 hover:text-white hover:bg-blue-600 font-bold text-xs bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-md transition">
                                                    Edit
                                                </a>
                                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-700 hover:text-white hover:bg-red-600 font-bold text-xs bg-red-50 border border-red-200 px-3 py-1.5 rounded-md transition">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="border-b py-10 text-center text-gray-500">
                                            <span class="text-4xl block mb-3">📅</span>
                                            Belum ada event yang dibuat.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Jika yang login user biasa -->
                    @elseif(Auth::user()->role === 'user')
                        <div class="text-center py-16">
                            <span class="text-6xl block mb-6">👋</span>
                            <h3 class="text-3xl font-black text-gray-900 mb-3 tracking-tight">Selamat Datang, {{ Auth::user()->name }}!</h3>
                            <p class="text-lg text-gray-600 mb-8 font-medium">Mulai eksplorasi event menarik dan pesan tiketmu sekarang.</p>
                            <a href="/" class="bg-indigo-600 text-white px-8 py-3.5 rounded-full font-bold hover:bg-indigo-700 transition shadow-lg hover:shadow-indigo-200">
                                Cari Event Sekarang
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
