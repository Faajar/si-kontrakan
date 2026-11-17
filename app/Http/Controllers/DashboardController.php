<?php

namespace App\Http\Controllers;

use App\Enums\StatusKontrak;
use App\Enums\StatusPembayaran;
use App\Enums\StatusUnit;
use App\Models\KontrakSewa;
use App\Models\Lokasi;
use App\Models\Pembayaran;
use App\Models\Pengeluaran;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        return $request->user()->isAdmin()
            ? $this->admin()
            : $this->penyewa($request);
    }

    private function admin(): View
    {
        $hariIni = Carbon::today();

        $statusUnit = Unit::query()
            ->selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $tunggakan = Pembayaran::with('kontrak.penyewa', 'kontrak.unit.lokasi')
            ->where('status', StatusPembayaran::Nunggak)
            ->whereDate('jatuh_tempo', '<', $hariIni)
            ->orderBy('jatuh_tempo')
            ->get();

        $jatuhTempo = Pembayaran::with('kontrak.penyewa', 'kontrak.unit')
            ->where('status', StatusPembayaran::Nunggak)
            ->whereBetween('jatuh_tempo', [$hariIni, $hariIni->copy()->addDays(7)])
            ->orderBy('jatuh_tempo')
            ->get();

        return view('admin.dashboard', [
            'jumlahLokasi' => Lokasi::count(),
            'jumlahUnit' => $statusUnit->sum(),
            'statusUnit' => collect(StatusUnit::cases())->mapWithKeys(
                fn (StatusUnit $s) => [$s->label() => (int) ($statusUnit[$s->value] ?? 0)]
            ),
            'totalPemasukan' => Pembayaran::where('status', StatusPembayaran::Lunas)->sum('nominal'),
            'totalPengeluaran' => Pengeluaran::sum('nominal'),
            'totalTunggakan' => $tunggakan->sum('nominal'),
            'tunggakan' => $tunggakan,
            'jatuhTempo' => $jatuhTempo,
            'pengajuanBaru' => KontrakSewa::where('status', StatusKontrak::Diajukan)->count(),
            'menungguVerifikasi' => Pembayaran::where('status', StatusPembayaran::Nunggak)->whereNotNull('bukti_bayar')->count(),
            'arusKas' => $this->arusKas(6),
        ]);
    }

    /**
     * Pemasukan (pembayaran lunas, menurut tanggal bayar) vs pengeluaran per bulan.
     *
     * @return array<int, array{bulan: string, masuk: int, keluar: int}>
     */
    private function arusKas(int $bulan): array
    {
        $mulai = Carbon::now()->startOfMonth()->subMonths($bulan - 1);

        $masuk = Pembayaran::where('status', StatusPembayaran::Lunas)
            ->whereDate('tanggal_bayar', '>=', $mulai)
            ->get(['tanggal_bayar', 'nominal'])
            ->groupBy(fn ($p) => $p->tanggal_bayar->format('Y-m'))
            ->map->sum('nominal');

        $keluar = Pengeluaran::whereDate('tanggal', '>=', $mulai)
            ->get(['tanggal', 'nominal'])
            ->groupBy(fn ($p) => $p->tanggal->format('Y-m'))
            ->map->sum('nominal');

        return collect(range(0, $bulan - 1))->map(function (int $i) use ($mulai, $masuk, $keluar) {
            $b = $mulai->copy()->addMonths($i);

            return [
                'bulan' => $b->translatedFormat('M Y'),
                'masuk' => (int) ($masuk[$b->format('Y-m')] ?? 0),
                'keluar' => (int) ($keluar[$b->format('Y-m')] ?? 0),
            ];
        })->all();
    }

    private function penyewa(Request $request): View
    {
        $penyewa = $request->user()->penyewa;

        $kontrak = $penyewa?->kontrak()
            ->with('unit.lokasi')
            ->whereIn('status', [StatusKontrak::Aktif, StatusKontrak::Diajukan])
            ->orderByRaw('status = ? desc', [StatusKontrak::Aktif->value])
            ->latest('tanggal_mulai')
            ->first();

        $tagihanBerikut = $kontrak?->pembayaran()
            ->where('status', StatusPembayaran::Nunggak)
            ->orderBy('jatuh_tempo')
            ->first();

        $pembayaranTerakhir = $kontrak?->pembayaran()
            ->where(fn ($q) => $q->where('status', StatusPembayaran::Lunas)->orWhereNotNull('bukti_bayar'))
            ->latest('jatuh_tempo')
            ->first();

        return view('penyewa.dashboard', [
            'penyewa' => $penyewa,
            'kontrak' => $kontrak,
            'tagihanBerikut' => $tagihanBerikut,
            'pembayaranTerakhir' => $pembayaranTerakhir,
            'sisaHari' => $kontrak && $kontrak->status === StatusKontrak::Aktif
                ? max(0, (int) Carbon::today()->diffInDays($kontrak->tanggal_selesai, false))
                : null,
        ]);
    }
}
