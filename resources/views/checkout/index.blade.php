<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🛒 Checkout Tiket
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-2xl p-8 border border-gray-100">

                @if(session('warning'))
                    <div class="mb-6 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg shadow-sm flex items-center">
                        <span class="text-amber-500 text-2xl mr-3">⚠️</span>
                        <p class="text-amber-800 font-bold tracking-wide">{{ session('warning') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm flex items-center">
                        <span class="text-red-500 text-2xl mr-3">🚨</span>
                        <p class="text-red-800 font-bold tracking-wide">{{ session('error') }}</p>
                    </div>
                @endif

                @if(strtoupper($ticket->name) === 'VIP')
                    <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-5 rounded-r-xl shadow-sm animate-pulse">
                        <div class="flex items-center">
                            <span class="text-red-500 text-3xl mr-4">⚠️</span>
                            <div>
                                <h3 class="text-red-800 font-black text-sm uppercase tracking-widest mb-1">Peringatan Kuota Terbatas!</h3>
                                <p class="text-red-600 text-sm font-medium leading-relaxed">Karena tingginya permintaan antrean (Waiting List), batas waktu penyelesaian pembayaran untuk tiket VIP adalah <span class="font-black underline text-red-700">1 Menit</span> setelah pesanan dibuat. Harap segera siapkan bukti transfer Anda!</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="mb-8 bg-yellow-50 border-l-4 border-yellow-500 p-5 rounded-r-xl shadow-sm">
                        <div class="flex items-center">
                            <span class="text-yellow-600 text-3xl mr-4">⏳</span>
                            <div>
                                <h3 class="text-yellow-800 font-black text-sm uppercase tracking-widest mb-1">Batas Waktu Pembayaran</h3>
                                <p class="text-yellow-700 text-sm font-medium leading-relaxed">Selesaikan pembayaran Anda dalam kurun waktu <span class="font-black text-yellow-900">24 Jam</span> setelah pesanan dibuat sebelum transaksi otomatis dibatalkan oleh sistem.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <h3 class="text-2xl font-black text-gray-900 mb-6 border-b border-gray-100 pb-4">Ringkasan Pesanan</h3>

                <div class="bg-gray-50/50 rounded-2xl p-6 border border-gray-200 mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-500 font-medium">Nama Event:</span>
                        <span class="font-bold text-lg text-gray-900">{{ $ticket->event->title }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-500 font-medium">Kategori Tiket:</span>
                        <span class="font-black text-lg text-indigo-600 px-3 py-1 bg-indigo-50 rounded-lg">{{ $ticket->name }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-dashed border-gray-300 pt-5 mt-3">
                        <span class="text-gray-800 font-black text-xl">Total Pembayaran:</span>
                        <span class="font-black text-3xl text-emerald-600">Rp {{ number_format($ticket->price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <form action="{{ route('checkout.process', $ticket->id) }}" method="POST" class="mt-6">
                    @csrf
                    <button type="submit" class="w-full bg-gray-900 text-white py-4 rounded-xl font-black text-lg hover:bg-indigo-600 transition-all duration-300 shadow-lg hover:shadow-indigo-200 hover:-translate-y-1">
                        Buat Pesanan & Bayar Sekarang
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('events.show', $ticket->event->id) }}" class="text-gray-400 hover:text-gray-800 font-bold text-sm transition">
                        &larr; Batal dan kembali ke halaman event
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
