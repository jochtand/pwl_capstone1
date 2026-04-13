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
            <a href="/" class="flex items-center gap-2.5 transition hover:scale-105 group">
                <div class="bg-indigo-600 text-white p-2 rounded-xl shadow-md group-hover:bg-indigo-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
                <span class="font-black text-2xl tracking-tighter text-gray-900">
                    Tiket<span class="text-indigo-600">App</span>
                </span>
            </a>

            @if (Route::has('login'))
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-bold text-gray-700 hover:text-indigo-600 transition">Masuk</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 rounded-full font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-md shadow-indigo-100">
                                Daftar
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </nav>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-16 relative z-10">

        <div class="text-center mb-20 bg-white p-12 rounded-3xl shadow-[0_10px_40px_rgb(0,0,0,0.03)] border border-gray-100 relative overflow-hidden">
            <h1 class="text-5xl md:text-6xl font-black text-gray-900 tracking-tighter leading-tight mb-6">
                Jelajahi <span class="text-indigo-600">Event</span> Pilihan<br>Tanpa Ribet.
            </h1>
            <p class="max-w-2xl mx-auto text-lg text-gray-600 font-medium leading-relaxed">
                Sistem manajemen dan penjualan tiket terintegrasi untuk berbagai event mulai dari Konser Musik, E-Sports, hingga Seminar.
            </p>
        </div>

        <div class="mb-12 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Jadwal Event Mendatang</h2>
            <form action="/" method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama event..." class="border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-full text-sm px-6 py-3 w-64 shadow-inner bg-gray-50/50">

                <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-full text-sm font-bold hover:bg-indigo-700 transition shadow-md shadow-indigo-100">
                    Cari
                </button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($events as $event)
                <div class="bg-white rounded-3xl shadow-[0_3px_15px_rgb(0,0,0,0.02)] border border-gray-100 overflow-hidden hover:shadow-[0_20px_40px_rgb(0,0,0,0.05)] hover:-translate-y-2 transition-all duration-500 flex flex-col group">

                    <div class="relative h-56 overflow-hidden bg-gray-100">
                        @if($event->image)
                            <img src="{{ asset('event_images/' . $event->image) }}" alt="Poster {{ $event->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                <span class="text-white/60 font-black text-2xl opacity-50 italic">TiketApp</span>
                            </div>
                        @endif

                        <div class="absolute top-4 left-4">
                            <span class="bg-white/95 backdrop-blur-sm text-gray-900 text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-lg shadow-sm">
                                {{ $event->category }}
                            </span>
                        </div>
                    </div>

                    <div class="p-8 flex-1 flex flex-col">
                        <h3 class="text-2xl font-black text-gray-900 mb-4 leading-tight group-hover:text-indigo-600 transition">{{ $event->title }}</h3>

                        <div class="text-sm text-gray-600 space-y-3 mb-8 font-medium border-t border-dashed border-gray-100 pt-6">
                            <p class="flex items-center"><span class="mr-3 text-lg">📅</span> {{ date('d F Y, H:i', strtotime($event->start_date)) }} WIB</p>
                            <p class="flex items-center"><span class="mr-3 text-lg">📍</span> <span class="line-clamp-1">{{ $event->location }}</span></p>
                        </div>

                        <div class="mt-auto">
                            <a href="{{ route('events.show', $event->id) }}" class="block text-center w-full bg-gray-900 text-white py-3.5 rounded-2xl font-bold hover:bg-indigo-600 transition shadow-lg transform active:scale-95">
                                Beli Tiket Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 text-gray-500 bg-white rounded-3xl border-2 border-dashed border-gray-100 shadow-inner">
                    <span class="text-6xl block mb-4">🎫</span>
                    <p class="text-xl font-bold text-gray-800">Belum ada event yang dijadwalkan.</p>
                    <p class="text-sm mt-1">Coba cek kembali nanti atau cari event lainnya.</p>
                </div>
            @endforelse
        </div>
    </main>

    <footer class="mt-24 border-t border-gray-100 bg-white">
        <div class="max-w-7xl mx-auto px-6 py-12 text-center text-gray-400 text-xs font-bold uppercase tracking-widest">
            &copy; {{ date('Y') }} TiketApp Capstone Project. Handcrafted for Performance.
        </div>
    </footer>

</div>
</body>
</html>
