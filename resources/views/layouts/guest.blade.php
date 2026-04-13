<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TiketApp </title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased selection:bg-indigo-500 selection:text-white">
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-50 via-white to-purple-50 relative overflow-hidden">

    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
    <div class="absolute top-[20%] right-[-10%] w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>

    <div class="relative z-10 mb-8">
        <a href="/" class="flex flex-col items-center group">
            <div class="bg-indigo-600 text-white p-4 rounded-2xl group-hover:bg-indigo-700 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl shadow-lg shadow-indigo-200 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </div>
            <span class="font-black text-3xl tracking-tight text-gray-900">
                    Tiket<span class="text-indigo-600">App</span>
                </span>
        </a>
    </div>

    <div class="relative z-10 w-full sm:max-w-md px-10 py-12 bg-white/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white sm:rounded-3xl overflow-hidden">
        {{ $slot }}
    </div>

    <p class="relative z-10 mt-8 text-xs text-gray-400 font-medium tracking-wide">
        &copy; {{ date('Y') }} TiketApp Capstone Project.
    </p>
</div>
</body>
</html>
