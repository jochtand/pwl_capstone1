<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if(Auth::user()->role === 'organizer')
                📊 Dashboard Analitik Panitia
            @else
                Selamat Datang, {{ Auth::user()->name }}!
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-8">

                @if(session('success'))
                    <div class="mb-8 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex items-center">
                        <span class="text-emerald-500 text-2xl mr-3">✅</span>
                        <p class="text-emerald-800 font-semibold tracking-wide">{{ session('success') }}</p>
                    </div>
                @endif
                    @if(session('error'))
                        <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm flex items-center">
                            <span class="text-red-500 text-2xl mr-3"></span>
                            <p class="text-red-800 font-semibold tracking-wide">{{ session('error') }}</p>
                        </div>
                    @endif

                @if(Auth::user()->role === 'organizer')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-indigo-500">
                            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Total Event Aktif</h3>
                            <p class="text-3xl font-black text-gray-900">{{ $totalEvents }} <span class="text-sm font-medium text-gray-400 normal-case">Event</span></p>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-emerald-500">
                            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Total Tiket Terjual (Lunas)</h3>
                            <p class="text-3xl font-black text-gray-900">{{ $totalTickets }} <span class="text-sm font-medium text-gray-400 normal-case">Tiket</span></p>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 border-l-4 border-l-amber-500">
                            <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-1">Total Keuntungan (Revenue)</h3>
                            <p class="text-3xl font-black text-emerald-600"><span class="text-lg text-emerald-500 mr-1">Rp</span>{{ number_format($totalRevenue, 0, ',', '.') }}</p>
                        </div>
                    </div>

                        <div class="mb-8 flex justify-end gap-3">

                            <form action="{{ route('transactions.clearExpired') }}" method="POST" onsubmit="return confirm('Yakin ingin menyapu bersih semua pesanan tiket yang melewati batas waktu pembayaran (VIP >1 Jam, Reguler >24 Jam)?');">
                                @csrf
                                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 px-5 rounded-lg text-sm shadow-md transition flex items-center gap-2">
                                    🧹 Bersihkan Tiket Kadaluarsa
                                </button>
                            </form>

                            <a href="{{ route('report.export') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 px-5 rounded-lg text-sm shadow-md transition flex items-center gap-2">
                                📄 Download Rekap Keuntungan (PDF)
                            </a>
                        </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">

                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <h3 class="text-lg font-black text-gray-800 mb-2 flex items-center">
                                <span class="mr-2">📈</span> Tren Pendapatan Tahunan
                            </h3>
                            <p class="text-xs text-gray-500 mb-6 font-medium">Akumulasi keuntungan tiket lunas per bulan di tahun {{ date('Y') }}</p>
                            <div class="relative w-full" style="height: 250px;">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                            <h3 class="text-lg font-black text-gray-800 mb-2 flex items-center">
                                <span class="mr-2">🔥</span> Analisis Event Terpopuler
                            </h3>
                            <p class="text-xs text-gray-500 mb-6 font-medium">Berdasarkan jumlah tiket terjual terbanyak</p>

                            <div class="space-y-4">
                                @forelse($eventAnalysis->take(4) as $index => $event)
                                    <div class="flex items-center justify-between p-3 rounded-lg {{ $index == 0 ? 'bg-amber-50 border border-amber-200' : 'bg-gray-50 border border-gray-100' }}">
                                        <div class="flex items-center gap-4">
                                            <div class="font-black text-xl {{ $index == 0 ? 'text-amber-500' : 'text-gray-400' }}">#{{ $index + 1 }}</div>
                                            <div>
                                                <h4 class="font-bold text-gray-800 text-sm">{{ $event->title }}</h4>
                                                <p class="text-xs text-gray-500">{{ $event->category }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-black text-indigo-600 text-sm">{{ $event->tickets_sold }} <span class="text-xs font-normal text-gray-500">Tiket</span></p>
                                            <p class="text-xs font-bold text-emerald-600">Rp {{ number_format($event->revenue, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-gray-500 text-sm py-4">Belum ada data penjualan tiket.</p>
                                @endforelse
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
                                        <th class="p-3 font-semibold text-center">Bukti Bayar</th>
                                        <th class="p-3 font-semibold text-center">Aksi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($pendingTransactions as $trx)
                                        <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                                            <td class="p-3 font-mono text-sm text-gray-600">#TRX-{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}</td>
                                            <td class="p-3 font-medium text-gray-800">{{ $trx->ticketCategory->event->title }}</td>
                                            <td class="p-3 font-bold text-emerald-600">Rp {{ number_format($trx->total_price, 0, ',', '.') }}</td>
                                            <td class="p-3 text-center">
                                                @if($trx->payment_proof)
                                                    <a href="{{ asset('payment_proofs/' . $trx->payment_proof) }}" target="_blank" class="inline-block bg-indigo-50 text-indigo-600 border border-indigo-200 px-3 py-1 rounded-md text-xs font-bold hover:bg-indigo-600 hover:text-white transition">👁️ Lihat Bukti</a>
                                                @else
                                                    <span class="text-xs text-gray-400 italic">Tidak ada</span>
                                                @endif
                                            </td>
                                            <td class="p-3 text-center">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <form action="{{ route('transactions.approve', $trx->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="bg-emerald-500 text-white px-3 py-1.5 rounded-md text-sm font-bold hover:bg-emerald-600 transition shadow-sm">✅ Terima</button>
                                                    </form>
                                                    <form action="{{ route('transactions.reject', $trx->id) }}" method="POST" onsubmit="return confirm('Tolak pembayaran dan batalkan tiket ini?');">
                                                        @csrf
                                                        <button type="submit" class="bg-red-500 text-white px-3 py-1.5 rounded-md text-sm font-bold hover:bg-red-600 transition shadow-sm">❌ Tolak</button>
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
                                <th class="border-b py-4 px-4 font-bold">Tiket Terjual</th>
                                <th class="border-b py-4 px-4 font-bold text-center">Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($events as $event)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="border-b py-4 px-4 font-medium">{{ $event->title }}</td>
                                    <td class="border-b py-4 px-4"><span class="bg-indigo-50 text-indigo-700 text-xs px-3 py-1 rounded-full font-bold">{{ $event->category }}</span></td>

                                    @php
                                        $sold = $eventAnalysis->firstWhere('id', $event->id)->tickets_sold ?? 0;
                                    @endphp
                                    <td class="border-b py-4 px-4 font-bold text-indigo-600">{{ $sold }}</td>

                                    <td class="border-b py-4 px-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <a href="{{ route('tickets.index', $event->id) }}" class="text-emerald-700 hover:text-white hover:bg-emerald-600 font-bold text-xs bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-md transition">🎟️ Tiket</a>
                                            <a href="{{ route('events.edit', $event->id) }}" class="text-blue-700 hover:text-white hover:bg-blue-600 font-bold text-xs bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-md transition">Edit</a>
                                            <form action="{{ route('events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-700 hover:text-white hover:bg-red-600 font-bold text-xs bg-red-50 border border-red-200 px-3 py-1.5 rounded-md transition">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="border-b py-10 text-center text-gray-500">Belum ada event.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    @elseif(Auth::user()->role === 'user')

                        <div class="mb-8">
                            <h3 class="text-2xl font-black text-gray-900">Event Sedang Berlangsung</h3>
                            <p class="text-gray-500 mt-1 font-medium">Temukan dan pesan tiket event favoritmu sekarang sebelum kehabisan!</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pb-6">
                            @forelse($events as $event)
                                <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 flex flex-col group">
                                    <div class="relative h-48 overflow-hidden bg-gray-100">
                                        @if($event->image)
                                            <img src="{{ asset('event_images/' . $event->image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        @else
                                            <div class="w-full h-full bg-indigo-50 flex items-center justify-center">
                                                <span class="text-indigo-200 text-5xl">🎟️</span>
                                            </div>
                                        @endif
                                        <div class="absolute top-4 left-4">
                                        <span class="bg-white/90 backdrop-blur-sm text-gray-900 text-xs font-black uppercase tracking-wider px-3 py-1.5 rounded-md shadow-sm">
                                            {{ $event->category }}
                                        </span>
                                        </div>
                                    </div>

                                    <div class="p-5 flex-1 flex flex-col">
                                        <h4 class="text-lg font-black text-gray-900 mb-2 leading-tight group-hover:text-indigo-600 transition">{{ $event->title }}</h4>

                                        <div class="text-sm text-gray-600 mb-5 space-y-1.5 flex-1 font-medium">
                                            <p class="flex items-center"><span class="mr-2">📅</span> {{ date('d M Y, H:i', strtotime($event->start_date)) }}</p>
                                            <p class="flex items-start"><span class="mr-2">📍</span> <span class="line-clamp-2">{{ $event->location }}</span></p>
                                        </div>

                                        <a href="{{ route('events.show', $event->id) }}" class="block w-full text-center bg-gray-900 text-white px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-indigo-600 transition shadow-md">
                                            Lihat Detail & Tiket
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-full text-center py-16 bg-gray-50 rounded-3xl border border-dashed border-gray-300">
                                    <span class="text-5xl block mb-4">📭</span>
                                    <h4 class="text-xl font-bold text-gray-800 mb-1">Belum ada event</h4>
                                    <p class="text-gray-500 font-medium">Saat ini belum ada event yang tersedia. Coba lagi nanti!</p>
                                </div>
                            @endforelse
                        </div>

                    @endif

            </div>
        </div>
    </div>

    @if(Auth::user()->role === 'organizer')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const canvasRev = document.getElementById('revenueChart');
                if (canvasRev) {
                    new Chart(canvasRev.getContext('2d'), {
                        type: 'line', // Grafik garis untuk tren waktu
                        data: {
                            labels: {!! json_encode($chartMonths ?? []) !!},
                            datasets: [{
                                label: 'Keuntungan (Rp)',
                                data: {!! json_encode($chartRevenueData ?? []) !!},
                                borderColor: '#10b981', // Emerald 500
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                borderWidth: 3,
                                pointBackgroundColor: '#059669',
                                pointRadius: 4,
                                fill: true,
                                tension: 0.3 // Melengkung halus
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let value = context.raw || 0;
                                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            if (value >= 1000000) return 'Rp ' + (value / 1000000) + ' Jt';
                                            if (value >= 1000) return 'Rp ' + (value / 1000) + ' Rb';
                                            return value;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endif
</x-app-layout>
