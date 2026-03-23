<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} - TiketApp</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<nav class="bg-white shadow p-4 flex justify-between items-center">
    <a href="{{ url('/') }}" class="text-2xl font-black text-indigo-600">🎫 TiketApp</a>
    <div>
        @guest
            <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 font-semibold mr-4">Log in</a>
            <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 font-semibold">Register</a>
        @endguest
        @auth
            <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800 font-bold mr-4">Dashboard</a>
        @endauth
    </div>
</nav>

<div class="max-w-6xl mx-auto p-6 mt-4 grid grid-cols-1 md:grid-cols-3 gap-8">

    <div class="md:col-span-2 space-y-6">
        <div class="bg-white rounded-xl shadow-md p-8">
            <span class="text-xs font-bold uppercase tracking-wider bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full">{{ $event->category }}</span>

            <h1 class="text-4xl font-extrabold text-gray-900 mt-4">{{ $event->title }}</h1>

            <div class="mt-6 flex items-center text-gray-600 text-lg">
                <span class="mr-3">📅</span>
                <span>{{ date('d F Y, H:i', strtotime($event->start_date)) }} WIB</span>
            </div>

            <div class="mt-3 flex items-center text-gray-600 text-lg">
                <span class="mr-3">📍</span>
                <span>{{ $event->location }}</span>
            </div>

            <div class="mt-8 border-t pt-6">
                <h3 class="text-2xl font-bold mb-4">Deskripsi Event</h3>
                <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $event->description }}</p>
            </div>
        </div>
    </div>

    <div class="md:col-span-1">
        <div class="bg-white rounded-xl shadow-md p-6 sticky top-6 border-t-4 border-indigo-500">
            <h3 class="text-xl font-bold mb-4 border-b pb-2">Pilih Tiket Anda</h3>

            @forelse($event->ticketCategories as $ticket)
                <div class="border border-gray-200 rounded-lg p-4 mb-4 hover:border-indigo-500 hover:shadow-md transition duration-200">
                    <h4 class="font-bold text-lg text-gray-800">{{ $ticket->name }}</h4>
                    <p class="text-indigo-600 font-extrabold text-2xl mt-1">Rp {{ number_format($ticket->price, 0, ',', '.') }}</p>

                    <div class="flex justify-between items-center mt-3">
                        <span class="text-sm text-gray-500 font-medium">Sisa Kuota: {{ $ticket->quota }}</span>
                    </div>

                    <a href="{{ route('checkout', $ticket->id) }}" class="block text-center w-full mt-4 bg-gray-900 text-white py-2.5 rounded-lg font-bold hover:bg-gray-800 transition shadow-sm">
                        Beli Tiket
                    </a>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    <p class="font-medium">Tiket belum tersedia.</p>
                    <p class="text-sm mt-1">Nantikan info selanjutnya!</p>
                </div>
            @endforelse

        </div>
    </div>

</div>

</body>
</html>
