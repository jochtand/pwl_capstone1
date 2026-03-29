<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🛒 Checkout Tiket
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-8">

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
                <h3 class="text-2xl font-bold mb-6 border-b pb-4">Ringkasan Pesanan</h3>

                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-600 font-medium">Nama Event:</span>
                        <span class="font-bold text-lg">{{ $ticket->event->title }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-gray-600 font-medium">Kategori Tiket:</span>
                        <span class="font-bold text-lg text-indigo-600">{{ $ticket->name }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-300 pt-4 mt-2">
                        <span class="text-gray-800 font-bold text-xl">Total Pembayaran:</span>
                        <span class="font-black text-2xl text-green-600">Rp {{ number_format($ticket->price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <form action="{{ route('checkout.process', $ticket->id) }}" method="POST" class="mt-6">
                    @csrf
                    <button type="submit" class="w-full bg-gray-800 text-white py-4 rounded-lg font-extrabold text-lg hover:bg-gray-700 transition shadow-lg">
                        Bayar Sekarang
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('events.show', $ticket->event->id) }}" class="text-gray-500 hover:text-gray-800 font-medium text-sm">
                        &larr; Batal dan kembali
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
