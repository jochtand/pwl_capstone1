<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📷 Simulasi Scanner Tiket
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-emerald-100 border-l-8 border-emerald-500 text-emerald-800 p-6 rounded-lg shadow-md">
                    <p class="font-black text-2xl mb-1">Berhasil!</p>
                    <p class="text-lg font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border-l-8 border-red-500 text-red-800 p-6 rounded-lg shadow-md">
                    <p class="font-black text-2xl mb-1">Ditolak!</p>
                    <p class="text-lg font-medium">{{ session('error') }}</p>
                </div>
            @endif

            @if(session('warning'))
                <div class="mb-6 bg-amber-100 border-l-8 border-amber-500 text-amber-800 p-6 rounded-lg shadow-md">
                    <p class="font-black text-2xl mb-1">Peringatan!</p>
                    <p class="text-lg font-medium">{{ session('warning') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
                <div class="p-10 text-center">
                    <div class="text-6xl mb-6">🔍</div>
                    <h3 class="text-3xl font-black text-gray-900 mb-2">Pintu Masuk Venue</h3>
                    <p class="text-gray-500 mb-8 font-medium">Masukkan ID Pesanan (Contoh: #TRX-00004 atau ketik angka 4) untuk memvalidasi tiket pengunjung.</p>

                    <form action="{{ route('scan.process') }}" method="POST" class="max-w-md mx-auto">
                        @csrf
                        <div class="mb-6">
                            <input type="text" name="ticket_id" required autofocus autocomplete="off" placeholder="Ketik ID Pesanan di sini..." class="w-full text-center text-2xl font-mono font-bold py-4 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-inner bg-gray-50">
                        </div>

                        <button type="submit" class="w-full bg-gray-900 text-white font-black text-xl py-4 rounded-xl shadow-lg hover:bg-gray-800 transition transform hover:scale-105 active:scale-95 duration-200">
                            CEK TIKET SEKARANG
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-900 font-bold transition">⬅️ Kembali ke Dashboard</a>
            </div>

        </div>
    </div>
</x-app-layout>
