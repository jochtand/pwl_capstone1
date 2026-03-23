<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\TicketCategory;

class TicketController extends Controller
{
    public function index($eventId)
    {
        $event = Event::findOrFail($eventId);
        $tickets = $event->ticketCategories;
        return view('tickets.index', compact('event', 'tickets'));
    }

    public function store(Request $request, $eventId)
    {
        // Ganti validasi stock menjadi quota
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
        ]);

        $event = Event::findOrFail($eventId);

        // Ganti stock menjadi quota
        $event->ticketCategories()->create([
            'name' => $request->name,
            'price' => $request->price,
            'quota' => $request->quota,
        ]);

        return back()->with('success', 'Kategori tiket berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $ticket = TicketCategory::findOrFail($id);
        $ticket->delete();

        return back()->with('success', 'Kategori tiket berhasil dihapus!');
    }
}
