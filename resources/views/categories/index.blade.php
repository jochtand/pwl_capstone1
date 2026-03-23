<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📂 Kelola Kategori Event
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-xl shadow-sm flex items-center">
                    <span class="text-emerald-500 text-2xl mr-3">✅</span>
                    <p class="text-emerald-800 font-semibold tracking-wide">{{ session('success') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <div class="md:col-span-1">
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
                            ✨ Tambah Baru
                        </h3>
                        <form action="{{ route('categories.store') }}" method="POST">
                            @csrf
                            <div class="mb-5">
                                <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wider">Nama Kategori</label>
                                <input type="text" name="name" required placeholder="Contoh: Workshop"
                                       class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl shadow-sm">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <button type="submit" class="w-full bg-gray-900 text-white font-black py-3 rounded-xl hover:bg-indigo-600 transition shadow-lg transform hover:-translate-y-0.5">
                                SIMPAN KATEGORI
                            </button>
                        </form>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="text-xl font-black text-gray-900 mb-6">
                            📋 Daftar Kategori Tersedia
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left">
                                <thead>
                                <tr class="text-gray-400 text-xs font-bold uppercase tracking-widest border-b border-gray-100">
                                    <th class="pb-4 px-2">Nama Kategori</th>
                                    <th class="pb-4 px-2 text-center">Aksi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($categories as $cat)
                                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50 transition">
                                        <td class="py-4 px-2 font-bold text-gray-800">
                                            <span class="bg-indigo-50 text-indigo-700 px-3 py-1 rounded-lg text-sm">{{ $cat->name }}</span>
                                        </td>
                                        <td class="py-4 px-2 text-center">
                                            <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 px-3 py-1.5 rounded-lg transition">
                                                    🗑️ HAPUS
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-10 text-center text-gray-400 font-medium">Belum ada kategori.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
