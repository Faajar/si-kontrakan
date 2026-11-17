<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $unit->exists ? 'Ubah Unit '.$unit->nomor_unit : 'Tambah Unit' }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ $unit->exists ? route('admin.unit.update', $unit) : route('admin.unit.store') }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @csrf
                @if ($unit->exists) @method('PUT') @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="lokasi_id" value="Lokasi" />
                        <select id="lokasi_id" name="lokasi_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Pilih lokasi</option>
                            @foreach ($lokasiList as $id => $nama)
                                <option value="{{ $id }}" @selected(old('lokasi_id', $unit->lokasi_id) == $id)>{{ $nama }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('lokasi_id')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="nomor_unit" value="Nomor unit" />
                        <x-text-input id="nomor_unit" name="nomor_unit" class="mt-1 block w-full" :value="old('nomor_unit', $unit->nomor_unit)" required placeholder="U-01" />
                        <x-input-error :messages="$errors->get('nomor_unit')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="tarif_bulanan" value="Tarif bulanan (Rp)" />
                        <x-text-input id="tarif_bulanan" name="tarif_bulanan" type="number" min="0" step="1000" class="mt-1 block w-full" :value="old('tarif_bulanan', $unit->tarif_bulanan)" required />
                        <x-input-error :messages="$errors->get('tarif_bulanan')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="tarif_tahunan" value="Tarif tahunan (Rp, opsional)" />
                        <x-text-input id="tarif_tahunan" name="tarif_tahunan" type="number" min="0" step="1000" class="mt-1 block w-full" :value="old('tarif_tahunan', $unit->tarif_tahunan)" />
                        <x-input-error :messages="$errors->get('tarif_tahunan')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="jumlah_kamar" value="Jumlah kamar" />
                        <x-text-input id="jumlah_kamar" name="jumlah_kamar" type="number" min="1" class="mt-1 block w-full" :value="old('jumlah_kamar', $unit->jumlah_kamar)" required />
                        <x-input-error :messages="$errors->get('jumlah_kamar')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="luas_bangunan" value="Luas bangunan (m²)" />
                        <x-text-input id="luas_bangunan" name="luas_bangunan" type="number" min="0" step="0.01" class="mt-1 block w-full" :value="old('luas_bangunan', $unit->luas_bangunan)" />
                        <x-input-error :messages="$errors->get('luas_bangunan')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach (\App\Enums\StatusUnit::options() as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $unit->status?->value ?? 'kosong') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('status')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="ketentuan_penghuni" value="Ketentuan penghuni" />
                        <x-text-input id="ketentuan_penghuni" name="ketentuan_penghuni" class="mt-1 block w-full" :value="old('ketentuan_penghuni', $unit->ketentuan_penghuni)" placeholder="Pasutri / sesama jenis" />
                        <x-input-error :messages="$errors->get('ketentuan_penghuni')" class="mt-2" />
                    </div>
                </div>

                <div>
                    <x-input-label for="fasilitas" value="Fasilitas" />
                    <textarea id="fasilitas" name="fasilitas" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('fasilitas', $unit->fasilitas) }}</textarea>
                    <x-input-error :messages="$errors->get('fasilitas')" class="mt-2" />
                </div>

                <input type="hidden" name="izin_hewan" value="0">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="izin_hewan" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('izin_hewan', $unit->izin_hewan))>
                    <span class="text-sm text-gray-700">Hewan peliharaan diizinkan (kecuali hewan liar)</span>
                </label>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ $unit->exists ? route('admin.unit.show', $unit) : route('admin.unit.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
                    <x-primary-button>Simpan</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
