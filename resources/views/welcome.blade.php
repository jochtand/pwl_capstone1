<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TiketApp - Cari Event Seru di Sekitarmu</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900 selection:bg-indigo-500 selection:text-white">

<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-indigo-50/50 relative overflow-hidden">

    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-indigo-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>

    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm shadow-[0_2px_15px_rgb(0,0,0,0.02)] border-b border-gray-100">
        <nav class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5 transition hover:scale-105">
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-2 rounded-xl shadow-inner">
                    <span class="text-white text-2xl leading-none block">🎫</span>
                </div>
                <span class="font-black text-2xl tracking-tighter text-gray-900">
                            TiketApp
                        </span>
            </a>

            @if (Route::has('login'))
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition">Masuk</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2.5 bg-gray-900 border border-transparent rounded-full font-bold text-xs text-white uppercase tracking-widest hover:bg-gray-800 focus:bg-gray-800 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition shadow-sm">
                                Daftar
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </nav>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-16 relative z-10">

        <div class="text-center mb-20 bg-white p-12 rounded-3xl shadow-[0_10px_40px_rgb(0,0,0,0.03)] border border-gray-100">
            <h1 class="text-5xl md:text-6xl font-black text-gray-900 tracking-tighter leading-tight mb-6">
                Jelajahi <span class="text-indigo-600">Event</span> Pilihan<br>Tanpa Ribet.
            </h1>
            <p class="max-w-2xl mx-auto text-lg text-gray-600 font-medium leading-relaxed">
                Sistem manajemen dan penjualan tiket terintegrasi untuk berbagai event mulai dari Konser Musik, E-Sports, hingga Seminar.
            </p>
        </div>

        <div class="mb-12 flex items-center justify-between">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Jadwal Event Mendatang</h2>
            <form action="/" method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama event..." class="border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-full text-sm px-4 py-2 w-64 shadow-inner bg-gray-50">

                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-full text-sm font-bold hover:bg-indigo-700 transition shadow-sm">
                    Cari
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($events as $event)
                <div class="bg-white rounded-2xl shadow-[0_3px_15px_rgb(0,0,0,0.02)] border border-gray-100 overflow-hidden hover:shadow-[0_20px_40px_rgb(0,0,0,0.05)] hover:-translate-y-1 transition-all duration-300 flex flex-col group">

                    @if($event->image)
                        <img src="{{ asset('storage/' . $event->image) }}" alt="Poster {{ $event->title }}" class="w-full h-52 object-cover transition-transform duration-500 group-hover:scale-105">
                    @else
                        <div class="w-full h-52 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <span class="text-white/60 font-black text-2xl opacity-50">TiketApp</span>
                        </div>
                    @endif

                    <div class="p-7 flex-1 flex flex-col relative">
                        <div class="mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-full">{{ $event->category }}</span>
                        </div>

                        <h3 class="text-2xl font-black text-gray-900 mb-5 leading-tight group-hover:text-indigo-600 transition">{{ $event->title }}</h3>

                        <div class="text-sm text-gray-600 space-y-2.5 mb-8 font-medium border-t border-gray-100 pt-5">
                            <p class="flex items-center"><span class="mr-3 text-lg">📅</span> {{ date('d F Y, H:i', strtotime($event->start_date)) }} WIB</p>
                            <p class="flex items-center"><span class="mr-3 text-lg">📍</span> {{ $event->location }}</p>
                        </div>

                        <div class="mt-auto">
                            <a href="{{ route('events.show', $event->id) }}" class="block text-center w-full bg-gray-900 text-white py-3 rounded-xl font-bold hover:bg-indigo-600 transition shadow-sm transform group-hover:shadow-indigo-100 group-hover:shadow-lg">
                                Beli Tiket Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 text-gray-500 bg-white rounded-2xl border-2 border-dashed border-gray-200 shadow-inner">
                    <span class="text-5xl block mb-4">🎫</span>
                    <p class="text-lg font-medium">Belum ada event yang dijadwalkan.</p>
                    <p class="text-sm mt-1">Coba cek kembali nanti atau hubungi administrator.</p>
                </div>
            @endforelse
        </div>
    </main>

    <footer class="mt-24 border-t border-gray-100 bg-white">
        <div class="max-w-7xl mx-auto px-6 py-10 text-center text-gray-500 text-sm font-medium">
            &copy; {{ date('Y') }} TiketApp Capstone Project. All rights reserved.
        </div>
    </footer>

</div>
</body>
</html>
