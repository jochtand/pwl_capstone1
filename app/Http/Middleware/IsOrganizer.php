<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsOrganizer
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login dan rolenya adalah 'organizer'
        if (Auth::check() && Auth::user()->role === 'organizer') {
            // Kalau dia organizer, silakan lewat!
            return $next($request);
        }

        // Kalau user biasa (pembeli), bawa kembali ke halaman utama web
        return redirect('/');
    }
}
