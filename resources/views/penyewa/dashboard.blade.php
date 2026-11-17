<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Penyewa</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (! $kontrak)
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <p class="text-gray-800 font-medium">Halo, {{ Auth::user()->name }}.</p>
                    <p class="mt-1 text-sm text-gray-600">Anda belum punya kontrak sewa aktif. Fitur pengajuan sewa akan tersedia di sini.</p>
                </div>
            @else
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <p class="text-sm text-gray-500">Unit yang Anda sewa</p>
                    <p class="mt-1 text-xl font-semibold text-gray-900">{{ $kontrak->unit->nomor_unit }} · {{ $kontrak->unit->lokasi->nama }}</p>
                    <p class="text-sm text-gray-600">{{ $kontrak->unit->lokasi->alamat }}</p>

                    <dl class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Status kontrak</dt>
                            <dd class="font-medium text-gray-900">{{ $kontrak->status->label() }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Masa sewa</dt>
                            <dd class="font-medium text-gray-900">{{ $kontrak->tanggal_mulai->translatedFormat('d M Y') }} – {{ $kontrak->tanggal_selesai->translatedFormat('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Sisa masa kontrak</dt>
                            <dd class="font-medium text-gray-900">{{ $sisaHari !== null ? $sisaHari.' hari' : '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <p class="text-sm text-gray-500">Tagihan berikutnya</p>
                        @if ($tagihanBerikut)
                            <p class="mt-1 text-xl font-semibold text-gray-900">{{ rupiah($tagihanBerikut->nominal) }}</p>
                            <p class="text-sm {{ $tagihanBerikut->jatuh_tempo->isPast() ? 'text-red-700' : 'text-gray-600' }}">
                                Jatuh tempo {{ $tagihanBerikut->jatuh_tempo->translatedFormat('d F Y') }}
                                @if ($tagihanBerikut->jatuh_tempo->isPast()) (terlambat) @endif
                            </p>
                        @else
                            <p class="mt-1 text-gray-700">Tidak ada tagihan yang belum dibayar.</p>
                        @endif
                    </div>

                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <p class="text-sm text-gray-500">Status pembayaran terakhir</p>
                        @if ($pembayaranTerakhir)
                            <p class="mt-1 text-xl font-semibold {{ $pembayaranTerakhir->menungguVerifikasi() ? 'text-amber-700' : 'text-green-700' }}">
                                {{ $pembayaranTerakhir->menungguVerifikasi() ? 'Menunggu verifikasi' : $pembayaranTerakhir->status->label() }}
                            </p>
                            <p class="text-sm text-gray-600">Tagihan {{ $pembayaranTerakhir->jatuh_tempo->translatedFormat('F Y') }} · {{ rupiah($pembayaranTerakhir->nominal) }}</p>
                        @else
                            <p class="mt-1 text-gray-700">Belum ada pembayaran.</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
