<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // Cek keamanan: Memastikan yang masuk benar-benar Admin
    private function checkAdmin()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak! Anda bukan Super Admin.');
        }
    }

    // Menampilkan daftar semua User
    public function index()
    {
        $this->checkAdmin();
        // Ambil semua user kecuali dirinya sendiri (Admin)
        $users = User::where('id', '!=', Auth::id())->latest()->get();
        return view('admin.users', compact('users'));
    }

    // Mengupdate Role / Peran User
    public function updateRole(Request $request, $id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);

        $request->validate([
            'role' => 'required|in:user,organizer,admin'
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', 'Role pengguna berhasil diubah!');
    }

    // Menghapus User
    public function destroy($id)
    {
        $this->checkAdmin();
        $user = User::findOrFail($id);
        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus permanen!');
    }
}
