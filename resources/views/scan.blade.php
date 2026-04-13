<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📷 Scan Tiket
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
                    <div class="text-6xl mb-6">📷</div>
                    <h3 class="text-3xl font-black text-gray-900 mb-2">Scan QR Code E-Ticket</h3>
                    <p class="text-gray-500 mb-8 font-medium">Arahkan kamera ke QR Code pada E-Ticket pengunjung.</p>

                    <div class="flex justify-center mb-8">
                        <div id="qr-reader" class="w-full max-w-md rounded-2xl overflow-hidden border-4 border-indigo-100 shadow-inner"></div>
                    </div>

                    <form id="scan-form" action="{{ route('scan.process') }}" method="POST" class="max-w-md mx-auto border-t border-dashed border-gray-300 pt-8">
                        @csrf
                        <p class="text-xs text-gray-400 font-bold mb-3 uppercase tracking-widest">Atau ketik ID manual</p>
                        <div class="flex gap-2">
                            <input type="text" id="ticket_id" name="ticket_id" required autocomplete="off" placeholder="Contoh: 4 atau #TRX-00004" class="w-full text-center text-lg font-mono font-bold py-3 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm bg-gray-50">
                            <button type="submit" class="bg-gray-900 text-white font-black px-6 rounded-xl shadow-md hover:bg-gray-800 transition">
                                CEK
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-900 font-bold transition">⬅️ Kembali ke Dashboard</a>
            </div>

        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Scanner
            const html5QrCode = new Html5Qrcode("qr-reader");

            // Fungsi yang dijalankan ketika QR Code sukses terbaca
            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                // 1. Bunyikan suara 'BEEP' kecil (opsional tapi bikin keren)
                let audio = new Audio('https://www.soundjay.com/buttons/sounds/beep-07a.mp3');
                audio.play();

                // 2. Matikan kamera sementara biar ga nge-scan berkali-kali
                html5QrCode.stop().then((ignore) => {

                    // 3. Ekstrak ID dari Teks QR (Format asli: TIKETAPP-TRX-1-USER-1)
                    let ticketId = decodedText;
                    const match = decodedText.match(/TRX-(\d+)/);
                    if(match && match[1]) {
                        ticketId = match[1]; // Mengambil angka aslinya saja
                    }

                    // 4. Masukkan ke input teks dan Submit Form Otomatis!
                    document.getElementById('ticket_id').value = ticketId;
                    document.getElementById('scan-form').submit();

                }).catch((err) => {
                    console.log("Gagal menghentikan scanner: ", err);
                });
            };

            // Konfigurasi Kamera (Kotak scan 250x250)
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            // Nyalakan Kamera Belakang (environment)
            html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
                .catch((err) => {
                    console.log("Error starting camera:", err);
                    document.getElementById('qr-reader').innerHTML = '<div class="bg-red-50 p-6 text-red-500 font-medium">Kamera tidak dapat diakses.<br>Pastikan Anda memberikan izin akses kamera (Allow Camera) di browser Anda.</div>';
                });
        });
    </script>
</x-app-layout>
