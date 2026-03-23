<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Event') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-blue-500">
                <div class="p-8 text-gray-900">

                    <!-- Tambahkan enctype agar bisa upload gambar saat edit -->
                    <form method="POST" action="{{ route('events.update', $event->id) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="title" class="block font-medium text-sm text-gray-700">Nama Event</label>
                            <input id="title" type="text" name="title" value="{{ old('title', $event->title) }}" required class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">
                        </div>

                        <div>
                            <label for="description" class="block font-medium text-sm text-gray-700">Deskripsi Event</label>
                            <textarea id="description" name="description" required rows="4" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">{{ old('description', $event->description) }}</textarea>
                        </div>

                        <!-- Bagian Edit Gambar/Poster -->
                        <div class="bg-blue-50 p-5 rounded-lg border border-blue-100">
                            <label class="block font-bold text-sm text-blue-900 mb-2">Poster Event (Biarkan kosong jika tidak ingin ganti)</label>
                            @if($event->image)
                                <div class="mb-3">
                                    <p class="text-xs text-blue-600 mb-1 font-bold italic underline">Poster Saat Ini:</p>
                                    <img src="{{ asset('storage/' . $event->image) }}" alt="Poster" class="h-32 rounded shadow-sm border-2 border-white">
                                </div>
                            @endif
                            <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 transition">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="category" class="block font-medium text-sm text-gray-700">Kategori</label>
                                <select id="category" name="category" required class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->name }}" {{ old('category', $event->category) == $cat->name ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="location" class="block font-medium text-sm text-gray-700">Lokasi (Venue)</label>
                                <input id="location" type="text" name="location" value="{{ old('location', $event->location) }}" required class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_date" class="block font-medium text-sm text-gray-700">Tanggal & Waktu Mulai</label>
                                <input id="start_date" type="datetime-local" name="start_date" value="{{ old('start_date', date('Y-m-d\TH:i', strtotime($event->start_date))) }}" required class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">
                            </div>
                            <div>
                                <label for="end_date" class="block font-medium text-sm text-gray-700">Tanggal & Waktu Selesai</label>
                                <input id="end_date" type="datetime-local" name="end_date" value="{{ old('end_date', date('Y-m-d\TH:i', strtotime($event->end_date))) }}" required class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">
                            </div>
                        </div>

                        <div class="pt-6 flex items-center space-x-4 border-t border-gray-100">
                            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition shadow-md shadow-blue-100 uppercase tracking-widest text-sm">
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-gray-500 hover:text-gray-800 transition">
                                ❌ Batal & Kembali
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
