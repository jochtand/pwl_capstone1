<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Event Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-sm">
                    <p class="font-bold mb-2">Gagal menyimpan! Tolong periksa hal berikut:</p>
                    <ul class="list-disc list-inside text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg border-t-4 border-indigo-500">
                <div class="p-8 text-gray-900">

                    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <label for="title" class="block font-medium text-sm text-gray-700">Nama Event</label>
                            <input id="title" type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Konser Dewa 19" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">
                        </div>

                        <div>
                            <label for="description" class="block font-medium text-sm text-gray-700">Deskripsi Event</label>
                            <textarea id="description" name="description" required rows="4" placeholder="Ceritakan keseruan event ini..." class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">{{ old('description') }}</textarea>
                        </div>

                        <div class="bg-indigo-50 p-5 rounded-lg border border-indigo-100">
                            <label class="block font-bold text-sm text-indigo-900 mb-2">Poster/Banner Event (Opsional)</label>
                            <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 transition">
                            <p class="text-xs text-indigo-600 mt-2 font-medium">Format yang didukung: JPG, JPEG, PNG. Ukuran maksimal 2MB.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="category" class="block font-medium text-sm text-gray-700">Kategori</label>
                                <select id="category" name="category" required class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->name }}" {{ old('category') == $cat->name ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1 italic">*Jika kategori tidak ada, buat dulu di menu Kelola Kategori.</p>
                            </div>

                            <div>
                                <label for="location" class="block font-medium text-sm text-gray-700">Lokasi (Venue)</label>
                                <input id="location" type="text" name="location" value="{{ old('location') }}" required placeholder="Contoh: Alun-Alun Kota Bandung" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_date" class="block font-medium text-sm text-gray-700">Tanggal & Waktu Mulai</label>
                                <input id="start_date" type="datetime-local" name="start_date" value="{{ old('start_date') }}" required class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">
                            </div>
                            <div>
                                <label for="end_date" class="block font-medium text-sm text-gray-700">Tanggal & Waktu Selesai</label>
                                <input id="end_date" type="datetime-local" name="end_date" value="{{ old('end_date') }}" required class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">
                            </div>
                        </div>

                        <div class="pt-6 mt-8">
                            <button type="submit" class="w-full bg-gray-900 text-white px-4 py-4 rounded-md font-bold text-lg hover:bg-gray-800 transition shadow-md">
                                + Simpan Event Baru
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
