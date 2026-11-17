<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Unit {{ $unit->nomor_unit }} <x-status-unit :status="$unit->status" class="ms-2 align-middle" /></h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.unit.edit', $unit) }}" class="text-sm text-indigo-600 hover:underline">Ubah</a>
                <form method="POST" action="{{ route('admin.unit.destroy', $unit) }}" onsubmit="return confirm('Hapus unit ini?')">
                    @csrf @method('DELETE')
                    <button class="text-sm text-red-600 hover:underline">Hapus</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div class="col-span-2">
                        <dt class="text-gray-500">Lokasi</dt>
                        <dd class="text-gray-900"><a href="{{ route('admin.lokasi.show', $unit->lokasi) }}" class="hover:underline">{{ $unit->lokasi->nama }}</a></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Tarif bulanan</dt>
                        <dd class="text-gray-900">{{ rupiah($unit->tarif_bulanan) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Tarif tahunan</dt>
                        <dd class="text-gray-900">{{ $unit->tarif_tahunan ? rupiah($unit->tarif_tahunan) : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Jumlah kamar</dt>
                        <dd class="text-gray-900">{{ $unit->jumlah_kamar }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Luas bangunan</dt>
                        <dd class="text-gray-900">{{ $unit->luas_bangunan ? $unit->luas_bangunan.' m²' : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Ketentuan penghuni</dt>
                        <dd class="text-gray-900">{{ $unit->ketentuan_penghuni ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Hewan peliharaan</dt>
                        <dd class="text-gray-900">{{ $unit->izin_hewan ? 'Diizinkan' : 'Tidak diizinkan' }}</dd>
                    </div>
                    <div class="col-span-2 sm:col-span-4">
                        <dt class="text-gray-500">Fasilitas</dt>
                        <dd class="text-gray-900 whitespace-pre-line">{{ $unit->fasilitas ?: '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800">Penyewa saat ini</h3>
                    @if ($unit->kontrakAktif)
                        <p class="mt-2 text-gray-900">{{ $unit->kontrakAktif->penyewa->nama }}</p>
                        <p class="text-sm text-gray-600">
                            {{ $unit->kontrakAktif->periode->label() }} ·
                            {{ $unit->kontrakAktif->tanggal_mulai->translatedFormat('d M Y') }} – {{ $unit->kontrakAktif->tanggal_selesai->translatedFormat('d M Y') }}
                        </p>
                    @else
                        <p class="mt-2 text-sm text-gray-500">Unit sedang tidak disewa.</p>
                    @endif
                </div>

                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800">Perbaikan & upgrade</h3>
                        <span class="text-sm text-gray-600">Total biaya: <strong>{{ rupiah($totalPengeluaran) }}</strong></span>
                    </div>
                    <ul class="mt-3 divide-y divide-gray-100 text-sm">
                        @forelse ($unit->perbaikan as $p)
                            <li class="py-2 flex justify-between gap-3">
                                <span>{{ $p->judul }} <span class="text-gray-500">({{ $p->jenis->label() }}, {{ $p->status->label() }})</span></span>
                                <span class="whitespace-nowrap">{{ rupiah($p->biaya_sum_nominal) }}</span>
                            </li>
                        @empty
                            <li class="py-2 text-gray-500">Belum ada catatan.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
