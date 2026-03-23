<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            👑 Kelola Pengguna (Super Admin)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Notifikasi Sukses/Error -->
            @if(session('success'))
                <div class="mb-8 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex items-center">
                    <span class="text-emerald-500 text-2xl mr-3">✅</span>
                    <p class="text-emerald-800 font-semibold tracking-wide">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-8">
                    <h3 class="text-2xl font-black text-gray-900 mb-6 flex items-center gap-2">
                        👥 Daftar Pengguna Sistem
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                            <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                                <th class="border-b border-gray-200 py-4 px-4 font-bold">Nama Lengkap</th>
                                <th class="border-b border-gray-200 py-4 px-4 font-bold">Email</th>
                                <th class="border-b border-gray-200 py-4 px-4 font-bold">Role Aktif</th>
                                <th class="border-b border-gray-200 py-4 px-4 font-bold text-center">Aksi (Ubah Role & Hapus)</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 border-b border-gray-100 last:border-0 transition">
                                    <td class="py-4 px-4 font-medium text-gray-900">{{ $user->name }}</td>
                                    <td class="py-4 px-4 text-gray-600">{{ $user->email }}</td>
                                    <td class="py-4 px-4">
                                        @if($user->role === 'admin')
                                            <span class="bg-purple-100 text-purple-800 text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wider">Admin</span>
                                        @elseif($user->role === 'organizer')
                                            <span class="bg-indigo-100 text-indigo-800 text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wider">Panitia</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded-full font-bold uppercase tracking-wider">User</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">

                                            <!-- Form Ubah Role -->
                                            <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST" class="flex items-center gap-2">
                                                @csrf
                                                @method('PUT')
                                                <select name="role" class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5 pl-3 pr-8">
                                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                                    <option value="organizer" {{ $user->role === 'organizer' ? 'selected' : '' }}>Panitia</option>
                                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                </select>
                                                <button type="submit" class="bg-gray-900 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-gray-800 transition shadow-sm">
                                                    Simpan
                                                </button>
                                            </form>

                                            <span class="text-gray-300 hidden sm:inline">|</span>

                                            <!-- Form Hapus User -->
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('YAKIN HAPUS USER INI? Semua data miliknya (termasuk tiket) akan ikut terhapus permanen!');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                                    🗑️ Hapus
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-500">
                                        Belum ada pengguna lain di sistem.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
