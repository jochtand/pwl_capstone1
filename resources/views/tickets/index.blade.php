<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🎫 Kelola Tiket: {{ $event->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white shadow-sm sm:rounded-lg p-6 border-t-4 border-indigo-500">
                <h3 class="text-lg font-bold mb-4">Tambah Kategori Tiket Baru</h3>

                <form method="POST" action="{{ route('tickets.store', $event->id) }}">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Nama Tiket</label>
                            <input type="text" name="name" required placeholder="Misal: VIP" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700">Harga (Rp)</label>
                            <input type="number" name="price" required placeholder="Misal: 500000" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">
                        </div>

                        <div>
                            <label class="block font-medium text-sm text-gray-700">Stok (Kuota)</label>
                            <input type="number" name="quota" required placeholder="Misal: 100" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full mt-1">
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full bg-gray-800 text-white px-4 py-3 rounded-md hover:bg-gray-700 font-bold shadow-md transition duration-150 ease-in-out">
                                + Simpan Tiket
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4">Daftar Tiket</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                        <tr class="bg-gray-100">
                            <th class="border-b py-3 px-4">Nama</th>
                            <th class="border-b py-3 px-4">Harga</th>
                            <th class="border-b py-3 px-4">Kuota</th>
                            <th class="border-b py-3 px-4 text-center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($tickets as $ticket)
                            <tr class="hover:bg-gray-50">
                                <td class="border-b py-3 px-4 font-semibold">{{ $ticket->name }}</td>
                                <td class="border-b py-3 px-4 text-green-600 font-bold">Rp {{ number_format($ticket->price, 0, ',', '.') }}</td>
                                <td class="border-b py-3 px-4">{{ $ticket->quota }}</td> <td class="border-b py-3 px-4 text-center">
                                    <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tiket ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline font-semibold text-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="border-b py-8 text-center text-gray-500 border-dashed">Belum ada tiket.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
