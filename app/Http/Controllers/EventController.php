<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    // 1. Menampilkan halaman form Buat Event
    public function create()
    {
        // Mengambil semua kategori dari database untuk ditampilkan di dropdown
        $categories = Category::all();
        return view('events.create', compact('categories'));
    }

    // 2. Menangkap data dari form dan menyimpannya (Insert)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'category' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posters', 'public');
        }

        Event::create([
            'organizer_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'location' => $request->location,
            'image' => $imagePath,
        ]);

        return redirect()->route('dashboard')->with('success', 'Event beserta poster berhasil dibuat!');
    }

    // 3. Menampilkan halaman detail event untuk publik
    public function show($id)
    {
        $event = Event::with('ticketCategories')->findOrFail($id);
        return view('events.show', compact('event'));
    }

    // 4. Menampilkan form edit yang sudah terisi data lama
    public function edit($id)
    {
        $event = Event::findOrFail($id);

        // Memastikan hanya pemilik yang bisa edit
        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Akses Ditolak!');
        }

        // Mengambil daftar kategori juga untuk pilihan dropdown di form edit
        $categories = Category::all();

        return view('events.edit', compact('event', 'categories'));
    }

    // 5. Menyimpan perubahan data (Update)
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'category' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $event = Event::findOrFail($id);
        $imagePath = $event->image;

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $imagePath = $request->file('image')->store('posters', 'public');
        }

        $event->update([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'location' => $request->location,
            'image' => $imagePath,
        ]);

        return redirect()->route('dashboard')->with('success', 'Event berhasil diperbarui!');
    }

    // 6. Menghapus data (Delete)
    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        if ($event->organizer_id !== Auth::id()) {
            abort(403, 'Akses Ditolak!');
        }

        // Cek apakah ada transaksi
        $ticketCategoryIds = $event->ticketCategories()->pluck('id');
        $hasTransactions = \App\Models\Transaction::whereIn('ticket_category_id', $ticketCategoryIds)->exists();

        if ($hasTransactions) {
            return back()->with('error', '❌ DITOLAK! Event ini tidak bisa dihapus karena sudah ada transaksi.');
        }

        // Menghapus gambar dari storage sebelum hapus database
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return back()->with('success', 'Event berhasil dihapus.');
    }

    // Mengelola Kategori
    public function categoriesIndex() {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function categoriesStore(Request $request) {
        $request->validate(['name' => 'required|unique:categories,name']);
        Category::create($request->all());
        return back()->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function categoriesDestroy($id) {
        $category = Category::findOrFail($id);
        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus!');
    }
}
