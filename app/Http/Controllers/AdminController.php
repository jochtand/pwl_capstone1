<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Tambahan untuk enkripsi password saat tambah user

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
}
