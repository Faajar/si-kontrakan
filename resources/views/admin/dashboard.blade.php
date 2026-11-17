<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Pemilik</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if ($pengajuanBaru || $menungguVerifikasi)
                <div class="rounded-md bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-900">
                    @if ($pengajuanBaru) {{ $pengajuanBaru }} pengajuan kontrak menunggu persetujuan. @endif
                    @if ($menungguVerifikasi) {{ $menungguVerifikasi }} bukti bayar menunggu verifikasi. @endif
                </div>
            @endif

            {{-- Ringkasan unit --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Total unit</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $jumlahUnit }}</p>
                    <p class="text-xs text-gray-500">di {{ $jumlahLokasi }} lokasi</p>
                </div>
                @foreach ($statusUnit as $label => $jumlah)
                    <a href="{{ route('admin.unit.index', ['status' => \App\Enums\StatusUnit::cases()[$loop->index]->value]) }}" class="bg-white shadow-sm rounded-lg p-5 hover:ring-2 hover:ring-indigo-200">
                        <p class="text-sm text-gray-500">{{ $label }}</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $jumlah }}</p>
                    </a>
                @endforeach
            </div>

            {{-- Indikator keuangan --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Total pemasukan</p>
                    <p class="mt-1 text-xl font-semibold text-green-700">{{ rupiah($totalPemasukan) }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Total pengeluaran perbaikan/upgrade</p>
                    <p class="mt-1 text-xl font-semibold text-red-700">{{ rupiah($totalPengeluaran) }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Total tunggakan aktif</p>
                    <p class="mt-1 text-xl font-semibold text-amber-700">{{ rupiah($totalTunggakan) }}</p>
                </div>
            </div>

            {{-- Grafik arus kas (batang CSS, tanpa library tambahan) --}}
            @php($maks = max(1, collect($arusKas)->flatMap(fn ($b) => [$b['masuk'], $b['keluar']])->max()))
            <div class="bg-white shadow-sm rounded-lg p-5">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Arus kas 6 bulan terakhir</h3>
                    <div class="flex gap-4 text-xs text-gray-600">
                        <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded-sm bg-green-500"></span>Masuk</span>
                        <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded-sm bg-red-400"></span>Keluar</span>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-6 gap-2 sm:gap-4 items-end h-48">
                    @foreach ($arusKas as $b)
                        <div class="flex flex-col items-center h-full justify-end">
                            <div class="flex items-end gap-1 h-full w-full justify-center">
                                <div class="w-1/3 bg-green-500 rounded-t" style="height: {{ round($b['masuk'] / $maks * 100) }}%" title="Masuk {{ rupiah($b['masuk']) }}"></div>
                                <div class="w-1/3 bg-red-400 rounded-t" style="height: {{ round($b['keluar'] / $maks * 100) }}%" title="Keluar {{ rupiah($b['keluar']) }}"></div>
                            </div>
                            <span class="mt-2 text-[11px] sm:text-xs text-gray-600 whitespace-nowrap">{{ $b['bulan'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Penyewa menunggak --}}
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <h3 class="font-semibold text-gray-800">Penyewa menunggak</h3>
                    <ul class="mt-3 divide-y divide-gray-100">
                        @forelse ($tunggakan as $p)
                            <li class="py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $p->kontrak->penyewa->nama }} · {{ $p->kontrak->unit->nomor_unit }}</p>
                                    <p class="text-sm text-gray-500">{{ rupiah($p->nominal) }} · jatuh tempo {{ $p->jatuh_tempo->translatedFormat('d M Y') }}</p>
                                </div>
                                <a href="{{ $p->linkPengingatWa() }}" target="_blank" rel="noopener" class="shrink-0 inline-flex items-center rounded-md bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700">Ingatkan via WA</a>
                            </li>
                        @empty
                            <li class="py-3 text-sm text-gray-500">Tidak ada tunggakan.</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Mendekati jatuh tempo --}}
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <h3 class="font-semibold text-gray-800">Jatuh tempo 7 hari ke depan</h3>
                    <ul class="mt-3 divide-y divide-gray-100">
                        @forelse ($jatuhTempo as $p)
                            <li class="py-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-900 truncate">{{ $p->kontrak->penyewa->nama }} · {{ $p->kontrak->unit->nomor_unit }}</p>
                                    <p class="text-sm text-gray-500">{{ rupiah($p->nominal) }} · {{ $p->jatuh_tempo->translatedFormat('d M Y') }}</p>
                                </div>
                                <a href="{{ $p->linkPengingatWa() }}" target="_blank" rel="noopener" class="shrink-0 inline-flex items-center rounded-md border border-green-600 px-3 py-2 text-xs font-semibold text-green-700 hover:bg-green-50">Ingatkan via WA</a>
                            </li>
                        @empty
                            <li class="py-3 text-sm text-gray-500">Tidak ada tagihan yang jatuh tempo minggu ini.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
