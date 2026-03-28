<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            👑 Kelola Pengguna (Super Admin)
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showAddModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm">
                    <div class="flex items-center mb-2">
                        <span class="text-red-500 text-2xl mr-3">⚠️</span>
                        <h3 class="text-red-800 font-bold">Ada kesalahan input:</h3>
                    </div>
                    <ul class="list-disc list-inside text-red-700 text-sm ml-8">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-8 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex items-center">
                    <span class="text-emerald-500 text-2xl mr-3">✅</span>
                    <p class="text-emerald-800 font-semibold tracking-wide">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="p-8">

                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-2xl font-black text-gray-900 flex items-center gap-2">
                            👥 Daftar Pengguna Sistem
                        </h3>
                        <button @click="showAddModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-lg shadow-md transition flex items-center gap-2">
                            <span>+</span> Tambah Pengguna Baru
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                            <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                                <th class="border-b border-gray-200 py-4 px-4 font-bold">Nama Lengkap</th>
                                <th class="border-b border-gray-200 py-4 px-4 font-bold">Email</th>
                                <th class="border-b border-gray-200 py-4 px-4 font-bold">Role Aktif</th>
                                <th class="border-b border-gray-200 py-4 px-4 font-bold text-center">Aksi</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($users as $user)
                                <tr class="hover:bg-gray-50 border-b border-gray-100 last:border-0 transition" x-data="{ showEditModal: false }">
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

                                            <button @click="showEditModal = true" type="button" class="bg-amber-100 text-amber-700 hover:bg-amber-200 px-4 py-1.5 rounded-lg text-xs font-bold transition">
                                                ✏️ Edit
                                            </button>

                                            <span class="text-gray-300 hidden sm:inline">|</span>

                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('YAKIN HAPUS USER INI? Semua data miliknya (termasuk tiket) akan ikut terhapus permanen!');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                                    🗑️ Hapus
                                                </button>
                                            </form>

                                        </div>

                                        <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                                                <div x-show="showEditModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditModal = false" aria-hidden="true"></div>

                                                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                                                <div x-show="showEditModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                                                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                            <h3 class="text-xl leading-6 font-black text-gray-900 mb-5" id="modal-title">✏️ Edit Data Pengguna</h3>

                                                            <div class="mb-4">
                                                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                                                                <input type="text" name="name" value="{{ $user->name }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                            </div>

                                                            <div class="mb-4">
                                                                <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                                                                <input type="email" name="email" value="{{ $user->email }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                            </div>

                                                            <div class="mb-4">
                                                                <label class="block text-sm font-bold text-gray-700 mb-1">Role Sistem</label>
                                                                <select name="role" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                                    <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User</option>
                                                                    <option value="organizer" {{ $user->role === 'organizer' ? 'selected' : '' }}>Panitia</option>
                                                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                                                            <button type="submit" class="w-full inline-flex justify-center rounded-lg shadow-sm px-4 py-2 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm transition">
                                                                Simpan Perubahan
                                                            </button>
                                                            <button type="button" @click="showEditModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 border border-gray-300 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                                                                Batal
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-500 font-medium">
                                        Belum ada pengguna lain di sistem.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <div x-show="showAddModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                    <div x-show="showAddModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAddModal = false" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showAddModal" x-transition class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                        <form action="{{ route('admin.users.store') }}" method="POST">
                            @csrf
                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <h3 class="text-xl leading-6 font-black text-gray-900 mb-5" id="modal-title">✨ Tambah Pengguna Baru</h3>

                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                                    <input type="text" name="name" required placeholder="Masukkan nama..." class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" required placeholder="email@contoh.com" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Password</label>
                                    <input type="password" name="password" required minlength="8" placeholder="Minimal 8 karakter" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Role Sistem</label>
                                    <select name="role" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="user">User Biasa</option>
                                        <option value="organizer">Panitia (Organizer)</option>
                                        <option value="admin">Super Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                                <button type="submit" class="w-full inline-flex justify-center rounded-lg shadow-sm px-4 py-2 bg-indigo-600 text-base font-bold text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm transition">
                                    Simpan Pengguna
                                </button>
                                <button type="button" @click="showAddModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg shadow-sm px-4 py-2 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 border border-gray-300 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
