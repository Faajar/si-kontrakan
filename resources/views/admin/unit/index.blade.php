<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Unit Kontrakan</h2>
            <a href="{{ route('admin.unit.create') }}" class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">Tambah Unit</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            <form method="GET" class="flex flex-wrap items-end gap-3">
                <div>
                    <x-input-label for="lokasi_id" value="Lokasi" />
                    <select id="lokasi_id" name="lokasi_id" class="mt-1 rounded-md border-gray-300 shadow-sm text-sm">
                        <option value="">Semua lokasi</option>
                        @foreach ($lokasiList as $id => $nama)
                            <option value="{{ $id }}" @selected(($filter['lokasi_id'] ?? null) == $id)>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="status" value="Status" />
                    <select id="status" name="status" class="mt-1 rounded-md border-gray-300 shadow-sm text-sm">
                        <option value="">Semua status</option>
                        @foreach (\App\Enums\StatusUnit::options() as $value => $label)
                            <option value="{{ $value }}" @selected(($filter['status'] ?? null) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <x-secondary-button type="submit">Filter</x-secondary-button>
            </form>

            <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3 font-medium">Unit</th>
                            <th class="px-4 py-3 font-medium">Lokasi</th>
                            <th class="px-4 py-3 font-medium">Tarif / bulan</th>
                            <th class="px-4 py-3 font-medium">Kamar</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Penyewa</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($units as $u)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900"><a href="{{ route('admin.unit.show', $u) }}" class="hover:underline">{{ $u->nomor_unit }}</a></td>
                                <td class="px-4 py-3 text-gray-600">{{ $u->lokasi->nama }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ rupiah($u->tarif_bulanan) }}</td>
                                <td class="px-4 py-3">{{ $u->jumlah_kamar }}</td>
                                <td class="px-4 py-3"><x-status-unit :status="$u->status" /></td>
                                <td class="px-4 py-3 text-gray-600">{{ $u->kontrakAktif?->penyewa->nama ?? '-' }}</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.unit.edit', $u) }}" class="text-indigo-600 hover:underline">Ubah</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Tidak ada unit.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>{{ $units->links() }}</div>
        </div>
    </div>
</x-app-layout>
