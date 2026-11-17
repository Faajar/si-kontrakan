<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Lokasi Kontrakan</h2>
            <a href="{{ route('admin.lokasi.create') }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">Tambah Lokasi</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3 font-medium">Nama</th>
                            <th class="px-4 py-3 font-medium">Alamat</th>
                            <th class="px-4 py-3 font-medium">Unit</th>
                            <th class="px-4 py-3 font-medium">Peta</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($lokasi as $l)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900"><a href="{{ route('admin.lokasi.show', $l) }}" class="hover:underline">{{ $l->nama }}</a></td>
                                <td class="px-4 py-3 text-gray-600">{{ $l->alamat }}</td>
                                <td class="px-4 py-3">{{ $l->units_count }}</td>
                                <td class="px-4 py-3">
                                    @if ($l->mapsUrl())
                                        <a href="{{ $l->mapsUrl() }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">Google Maps</a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.lokasi.edit', $l) }}" class="text-indigo-600 hover:underline">Ubah</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada lokasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $lokasi->links() }}</div>
        </div>
    </div>
</x-app-layout>
