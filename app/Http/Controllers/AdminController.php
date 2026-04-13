<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Cek keamanan: Memastikan yang masuk benar-benar Admin
    private function checkAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Anda bukan Super Admin.');
        }
    }

    // [READ] Menampilkan daftar semua User
    public function index()
    {
        $this->checkAdmin();
        // Ambil semua user kecuali dirinya sendiri (Admin)
        $users = User::where('id', '!=', Auth::id())->latest()->get();
        return view('admin.users', compact('users'));
    }

    // [CREATE] Menambahkan User Baru dari panel Admin
    public function store(Request $request)
    {
        $this->checkAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,organizer,admin'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
            'role' => $request->role,
        ]);

        return back()->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    // [UPDATE] Mengupdate Nama, Email, dan Role User
    public function update(Request $request, $id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            // Pastikan email unik, tapi abaikan jika itu email milik user yang sedang diedit
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:user,organizer,admin'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return back()->with('success', 'Data pengguna berhasil diperbarui!');
    }

    // [DELETE] Menghapus User
    public function destroy($id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus permanen!');
    }

    // ==========================================
    // SUPER ADMIN - KELOLA EVENT
    // ==========================================
    public function events()
    {
        $this->checkAdmin();
        // Ambil semua event berserta data panitianya
        $events = Event::with('organizer')->latest()->get();
        // Ambil semua user yang role-nya panitia untuk ditaruh di dropdown
        $organizers = User::where('role', 'organizer')->get();

        return view('admin.events', compact('events', 'organizers'));
    }

    public function storeEvent(Request $request)
    {
        $this->checkAdmin();
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'start_date' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'organizer_id' => 'required|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('event_images'), $filename); // Langsung ke public biar aman!
            $data['image'] = $filename;
        }

        Event::create($data);
        return back()->with('success', 'Event berhasil dibuat dan ditugaskan ke Panitia!');
    }

    public function updateEvent(Request $request, $id)
    {
        $this->checkAdmin();
        $event = Event::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'start_date' => 'required|date',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'organizer_id' => 'required|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Hapus gambar lama dari folder public jika ada
            if ($event->image && file_exists(public_path('event_images/' . $event->image))) {
                unlink(public_path('event_images/' . $event->image));
            }

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('event_images'), $filename);
            $data['image'] = $filename;
        }

        $event->update($data);
        return back()->with('success', 'Data Event berhasil diperbarui!');
    }

    public function destroyEvent($id)
    {
        $this->checkAdmin();
        $event = Event::findOrFail($id);

        // Cek apakah ada transaksi sebelum dihapus (Sama dengan logika Panitia)
        $ticketCategoryIds = $event->ticketCategories()->pluck('id');
        $hasTransactions = \App\Models\Transaction::whereIn('ticket_category_id', $ticketCategoryIds)->exists();

        if ($hasTransactions) {
            return back()->with('error', '❌ DITOLAK! Event ini tidak bisa dihapus karena sudah ada tiket yang terjual.');
        }

        // Hapus file gambar dari server sebelum data dihapus dari database
        if ($event->image && file_exists(public_path('event_images/' . $event->image))) {
            unlink(public_path('event_images/' . $event->image));
        }

        $event->delete();
        return back()->with('success', 'Event beserta posternya berhasil dihapus!');
    }
}
