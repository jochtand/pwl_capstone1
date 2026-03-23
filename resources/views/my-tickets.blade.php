<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🎟️ Tiket Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @forelse($transactions as $transaction)
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl flex flex-col md:flex-row border border-gray-200 hover:shadow-xl transition-shadow duration-300">

                        <div class="p-6 flex-1">
                            <span class="text-xs font-bold uppercase tracking-wider bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full">{{ $transaction->ticketCategory->name }}</span>

                            <h3 class="text-2xl font-black text-gray-900 mt-4 leading-tight">{{ $transaction->ticketCategory->event->title }}</h3>

                            <div class="mt-4 text-sm text-gray-600 space-y-2 font-medium">
                                <p>📅 {{ date('d F Y, H:i', strtotime($transaction->ticketCategory->event->start_date)) }} WIB</p>
                                <p>📍 {{ $transaction->ticketCategory->event->location }}</p>
                            </div>

                            <div class="mt-6 pt-4 border-t border-dashed border-gray-300">
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">ID Pesanan</p>
                                <p class="text-sm text-gray-800 font-mono mt-1">#TRX-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 p-6 flex flex-col items-center justify-center border-l border-dashed border-gray-300 min-w-[250px]">

                            @if($transaction->payment_status === 'pending')
                                <div class="text-center w-full">
                                    <span class="inline-block bg-amber-100 text-amber-800 text-xs font-black px-3 py-1 rounded-full uppercase tracking-widest mb-2">Belum Dibayar</span>
                                    <p class="text-xs text-gray-500 mb-2">Scan QRIS di bawah ini untuk membayar</p>

                                    <div class="p-2 bg-white border border-gray-200 rounded-lg shadow-sm w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=QRIS-DUMMY-PAYMENT" alt="QRIS" class="w-20 h-20 opacity-80">
                                    </div>

                                    <form action="{{ route('tickets.pay', $transaction->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm hover:bg-indigo-700 transition text-sm">
                                            Kirim Konfirmasi Bayar
                                        </button>
                                    </form>
                                </div>

                            @elseif($transaction->payment_status === 'verifying')
                                <div class="text-center w-full py-8">
                                    <div class="text-4xl mb-3">⏳</div>
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-black px-3 py-1 rounded-full uppercase tracking-widest mb-2">Menunggu Verifikasi</span>
                                    <p class="text-sm text-gray-600 font-medium leading-relaxed">Panitia sedang mengecek pembayaran Anda. Mohon tunggu sebentar.</p>
                                </div>

                                <!-- JIKA DITOLAK (FAILED) -->
                            @elseif($transaction->payment_status === 'failed')
                                <div class="text-center w-full py-8">
                                    <div class="text-4xl mb-3">❌</div>
                                    <span class="inline-block bg-red-100 text-red-800 text-xs font-black px-3 py-1 rounded-full uppercase tracking-widest mb-2">Pembayaran Ditolak</span>
                                    <p class="text-sm text-gray-600 font-medium leading-relaxed">Bukti pembayaran Anda ditolak oleh panitia atau waktu pembayaran habis.</p>
                                </div>

                                <!-- JIKA LUNAS (PAID) -->
                            @elseif($transaction->payment_status === 'paid')
                                <span class="inline-block bg-emerald-100 text-emerald-800 text-xs font-black px-3 py-1 rounded-full uppercase tracking-widest mb-3">Lunas - Tiket Valid</span>
                                <div class="p-3 bg-white border border-gray-200 rounded-xl shadow-sm">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=TIKETAPP-TRX-{{ $transaction->id }}-USER-{{ Auth::id() }}" alt="QR Code" class="w-32 h-32">
                                </div>
                                <p class="text-[11px] text-center mt-4 mb-3 text-gray-500 font-medium leading-relaxed">Tunjukkan QR Code ini<br>di pintu masuk venue.</p>

                                <!-- TOMBOL DOWNLOAD PDF -->
                                <a href="{{ route('tickets.download', $transaction->id) }}" class="mt-2 flex items-center justify-center w-full bg-gray-900 text-white font-bold py-2 px-4 rounded-lg shadow-sm hover:bg-indigo-600 transition text-xs uppercase tracking-widest">
                                    📥 Download PDF
                                </a>
                            @endif

                        </div>
                    </div>
                @empty
                    <div class="col-span-2 bg-white p-16 text-center shadow-sm sm:rounded-2xl border border-dashed border-gray-300">
                        <span class="text-4xl">🎫</span>
                        <h3 class="text-xl font-bold text-gray-800 mt-4">Kamu belum memiliki tiket.</h3>
                        <p class="text-gray-500 mt-2">Yuk, cari event seru untuk akhir pekanmu!</p>
                        <a href="{{ url('/') }}" class="inline-block mt-6 bg-gray-900 text-white px-8 py-3 rounded-lg hover:bg-gray-800 font-bold shadow-md transition">Lihat Katalog Event</a>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
