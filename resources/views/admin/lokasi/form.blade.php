<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $lokasi->exists ? 'Ubah Lokasi' : 'Tambah Lokasi' }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ $lokasi->exists ? route('admin.lokasi.update', $lokasi) : route('admin.lokasi.store') }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @csrf
                @if ($lokasi->exists) @method('PUT') @endif

                <div>
                    <x-input-label for="nama" value="Nama lokasi" />
                    <x-text-input id="nama" name="nama" class="mt-1 block w-full" :value="old('nama', $lokasi->nama)" required />
                    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="alamat" value="Alamat lengkap" />
                    <textarea id="alamat" name="alamat" rows="2" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('alamat', $lokasi->alamat) }}</textarea>
                    <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="latitude" value="Latitude" />
                        <x-text-input id="latitude" name="latitude" type="number" step="any" class="mt-1 block w-full" :value="old('latitude', $lokasi->latitude)" placeholder="-6.3227" />
                        <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="longitude" value="Longitude" />
                        <x-text-input id="longitude" name="longitude" type="number" step="any" class="mt-1 block w-full" :value="old('longitude', $lokasi->longitude)" placeholder="107.2873" />
                        <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="link_maps" value="Link Google Maps (opsional)" />
                    <x-text-input id="link_maps" name="link_maps" type="url" class="mt-1 block w-full" :value="old('link_maps', $lokasi->link_maps)" placeholder="https://maps.app.goo.gl/..." />
                    <p class="mt-1 text-xs text-gray-500">Kalau kosong, link dibuat otomatis dari koordinat.</p>
                    <x-input-error :messages="$errors->get('link_maps')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="lingkungan_sekitar" value="Lingkungan sekitar" />
                    <textarea id="lingkungan_sekitar" name="lingkungan_sekitar" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Jarak ke pabrik, sekolah, pasar, dll.">{{ old('lingkungan_sekitar', $lokasi->lingkungan_sekitar) }}</textarea>
                    <x-input-error :messages="$errors->get('lingkungan_sekitar')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ $lokasi->exists ? route('admin.lokasi.show', $lokasi) : route('admin.lokasi.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
                    <x-primary-button>Simpan</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
