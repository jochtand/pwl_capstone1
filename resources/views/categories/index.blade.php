<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🏷️ Kelola Kategori Event
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm flex items-center">
                    <span class="text-emerald-500 text-2xl mr-3">✅</span>
                    <p class="text-emerald-800 font-bold tracking-wide">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-2xl p-8 border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Daftar Kategori</h3>

                    <form action="{{ route('categories.store') }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="name" placeholder="Nama Kategori Baru" required class="rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-bold shadow-sm hover:bg-indigo-700 transition">+ Tambah</button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                        <tr class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wider">
                            <th class="p-4 font-bold border-b">ID</th>
                            <th class="p-4 font-bold border-b w-full">Nama Kategori</th>
                            <th class="p-4 font-bold border-b text-center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($categories as $category)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="p-4 text-gray-500 font-mono text-sm">{{ $category->id }}</td>
                                <td class="p-4 font-bold text-gray-900">{{ $category->name }}</td>

                                <td class="p-4 text-center" x-data="{ showEdit: false }">
                                    <div class="flex justify-center space-x-2">
                                        <button @click="showEdit = true" class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-md font-bold text-xs hover:bg-blue-600 hover:text-white transition">Edit</button>
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-md font-bold text-xs hover:bg-red-600 hover:text-white transition">Hapus</button>
                                        </form>
                                    </div>

                                    <div x-show="showEdit" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
                                        <div @click.away="showEdit = false" class="bg-white rounded-2xl p-6 max-w-md w-full shadow-2xl text-left">
                                            <h3 class="text-xl font-bold mb-4 border-b pb-4">Edit Kategori</h3>
                                            <form action="{{ route('categories.update', $category->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="mb-6">
                                                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Kategori</label>
                                                    <input type="text" name="name" value="{{ $category->name }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="showEdit = false" class="px-5 py-2 bg-gray-200 text-gray-800 font-bold rounded-lg hover:bg-gray-300 text-sm transition">Batal</button>
                                                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 text-sm transition shadow-sm">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="p-8 text-center text-gray-500">
                                    <span class="text-3xl block mb-2">🏷️</span>
                                    Belum ada kategori.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
