<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $lokasi->nama }}</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.lokasi.edit', $lokasi) }}" class="text-sm text-indigo-600 hover:underline">Ubah</a>
                <form method="POST" action="{{ route('admin.lokasi.destroy', $lokasi) }}" onsubmit="return confirm('Hapus lokasi ini?')">
                    @csrf @method('DELETE')
                    <button class="text-sm text-red-600 hover:underline">Hapus</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm rounded-lg p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Alamat</p>
                    <p class="text-gray-900">{{ $lokasi->alamat }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Koordinat</p>
                    <p class="text-gray-900">
                        @if ($lokasi->latitude !== null) {{ $lokasi->latitude }}, {{ $lokasi->longitude }} @else - @endif
                        @if ($lokasi->mapsUrl())
                            · <a href="{{ $lokasi->mapsUrl() }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">Buka di Google Maps</a>
                        @endif
                    </p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-gray-500">Lingkungan sekitar</p>
                    <p class="text-gray-900 whitespace-pre-line">{{ $lokasi->lingkungan_sekitar ?: '-' }}</p>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Unit di lokasi ini</h3>
                    <a href="{{ route('admin.unit.create', ['lokasi_id' => $lokasi->id]) }}" class="text-sm text-indigo-600 hover:underline">+ Tambah unit</a>
                </div>
                <ul class="mt-3 divide-y divide-gray-100">
                    @forelse ($lokasi->units as $u)
                        <li class="py-3 flex items-center justify-between">
                            <a href="{{ route('admin.unit.show', $u) }}" class="font-medium text-gray-900 hover:underline">{{ $u->nomor_unit }}</a>
                            <span class="text-sm text-gray-600">{{ rupiah($u->tarif_bulanan) }}/bulan · <x-status-unit :status="$u->status" /></span>
                        </li>
                    @empty
                        <li class="py-3 text-sm text-gray-500">Belum ada unit.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
