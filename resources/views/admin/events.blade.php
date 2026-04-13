<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            👑 Super Admin - Kelola Event
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-sm flex items-center">
                    <span class="text-emerald-500 text-2xl mr-3">✅</span>
                    <p class="text-emerald-800 font-bold tracking-wide">{{ session('success') }}</p>
                </div>
            @endif

            <div x-data="{ showCreate: false }" class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 p-8">

                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Daftar Semua Event di Sistem</h3>
                    <button @click="showCreate = true" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-indigo-700 transition shadow-sm">
                        + Tambah Event Baru
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                        <tr class="bg-gray-50 text-gray-700 text-sm uppercase tracking-wider">
                            <th class="p-4 font-bold border-b">Nama Event</th>
                            <th class="p-4 font-bold border-b">Kategori</th>
                            <th class="p-4 font-bold border-b text-indigo-600">Panitia Bertugas</th>
                            <th class="p-4 font-bold border-b">Waktu & Lokasi</th>
                            <th class="p-4 font-bold border-b text-center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($events as $event)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="p-4 font-medium text-gray-900">{{ $event->title }}</td>
                                <td class="p-4"><span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-bold">{{ $event->category }}</span></td>
                                <td class="p-4 font-bold text-indigo-600">{{ $event->organizer ? $event->organizer->name : 'Tidak Ada' }}</td>
                                <td class="p-4 text-sm text-gray-600">
                                    📅 {{ date('d M Y, H:i', strtotime($event->start_date)) }}<br>
                                    📍 {{ $event->location }}
                                </td>

                                <td class="p-4 text-center" x-data="{ showEdit: false }">
                                    <div class="flex justify-center space-x-2">
                                        <button @click="showEdit = true" class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-md font-bold text-xs hover:bg-blue-600 hover:text-white transition">Edit</button>
                                        <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus event ini? Semua data terkait akan hilang!');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-red-50 text-red-600 px-3 py-1.5 rounded-md font-bold text-xs hover:bg-red-600 hover:text-white transition">Hapus</button>
                                        </form>
                                    </div>

                                    <div x-show="showEdit" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
                                        <div @click.away="showEdit = false" class="bg-white rounded-2xl p-8 max-w-2xl w-full shadow-2xl text-left">
                                            <h3 class="text-2xl font-bold mb-6 border-b pb-4">Edit Event: {{ $event->title }}</h3>
                                            <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf @method('PUT')
                                                <div class="grid grid-cols-2 gap-4 mb-4">
                                                    <div>
                                                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Event</label>
                                                        <input type="text" name="title" value="{{ $event->title }}" required class="w-full rounded-md border-gray-300 shadow-sm">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-bold text-gray-700 mb-1">Kategori Event</label>
                                                        <input type="text" name="category" value="{{ $event->category }}" required class="w-full rounded-md border-gray-300 shadow-sm">
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-2 gap-4 mb-4">
                                                    <div>
                                                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Mulai</label>
                                                        <input type="datetime-local" name="start_date" value="{{ date('Y-m-d\TH:i', strtotime($event->start_date)) }}" required class="w-full rounded-md border-gray-300 shadow-sm">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-bold text-gray-700 mb-1">Lokasi</label>
                                                        <input type="text" name="location" value="{{ $event->location }}" required class="w-full rounded-md border-gray-300 shadow-sm">
                                                    </div>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="block text-sm font-black text-indigo-600 mb-1">Pilih Panitia Bertugas</label>
                                                    <select name="organizer_id" required class="w-full rounded-md border-indigo-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-indigo-50">
                                                        @foreach($organizers as $org)
                                                            <option value="{{ $org->id }}" {{ $event->organizer_id == $org->id ? 'selected' : '' }}>{{ $org->name }} ({{ $org->email }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-4">
                                                    <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Event</label>
                                                    <textarea name="description" rows="3" required class="w-full rounded-md border-gray-300 shadow-sm">{{ $event->description }}</textarea>
                                                </div>
                                                <div class="mb-6">
                                                    <label class="block text-sm font-bold text-gray-700 mb-1">Ganti Banner (Opsional)</label>
                                                    <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-gray-100 file:text-gray-700">
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button" @click="showEdit = false" class="px-5 py-2.5 bg-gray-200 text-gray-800 font-bold rounded-lg hover:bg-gray-300">Batal</button>
                                                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div x-show="showCreate" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
                    <div @click.away="showCreate = false" class="bg-white rounded-2xl p-8 max-w-2xl w-full shadow-2xl">
                        <h3 class="text-2xl font-bold mb-6 border-b pb-4">Tambah Event Baru</h3>
                        <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Event</label>
                                    <input type="text" name="title" required class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Kategori Event</label>
                                    <input type="text" name="category" required class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Mulai</label>
                                    <input type="datetime-local" name="start_date" required class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Lokasi</label>
                                    <input type="text" name="location" required class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-black text-indigo-600 mb-1">Pilih Panitia Bertugas</label>
                                <select name="organizer_id" required class="w-full rounded-md border-indigo-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-indigo-50">
                                    <option value="" disabled selected>-- Pilih Panitia --</option>
                                    @foreach($organizers as $org)
                                        <option value="{{ $org->id }}">{{ $org->name }} ({{ $org->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi Event</label>
                                <textarea name="description" rows="3" required class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
                            </div>
                            <div class="mb-6">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Upload Banner</label>
                                <input type="file" name="image" required accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-bold file:bg-gray-100 file:text-gray-700">
                            </div>
                            <div class="flex justify-end gap-3">
                                <button type="button" @click="showCreate = false" class="px-5 py-2.5 bg-gray-200 text-gray-800 font-bold rounded-lg hover:bg-gray-300">Batal</button>
                                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700">Simpan Event</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
