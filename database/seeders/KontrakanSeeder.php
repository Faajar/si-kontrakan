<?php

namespace Database\Seeders;

use App\Enums\JenisPerbaikan;
use App\Enums\KategoriPengeluaran;
use App\Enums\MetodeBayar;
use App\Enums\PeriodeSewa;
use App\Enums\StatusKontrak;
use App\Enums\StatusPembayaran;
use App\Enums\StatusPerbaikan;
use App\Enums\StatusUnit;
use App\Models\KontrakSewa;
use App\Models\Lokasi;
use App\Models\Penyewa;
use App\Models\Perbaikan;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Data contoh: 9 lokasi, 11 unit, beberapa penyewa aktif beserta riwayat bayar
 * dan perbaikan. Nama/alamat hanya contoh, ganti dengan data asli pemilik.
 */
class KontrakanSeeder extends Seeder
{
    public function run(): void
    {
        $lokasiData = [
            ['Kontrakan Telukjambe A', 'Jl. Contoh Telukjambe No. 1, Karawang', -6.3227, 107.2873, 'Dekat kawasan industri KIIC (±10 menit), minimarket 200 m'],
            ['Kontrakan Telukjambe B', 'Jl. Contoh Telukjambe No. 7, Karawang', -6.3241, 107.2902, 'Dekat SD & SMP negeri, pasar ±1 km'],
            ['Kontrakan Puseurjaya', 'Jl. Contoh Puseurjaya No. 3, Karawang', -6.3105, 107.3001, 'Dekat kampus UNSIKA, banyak warung makan'],
            ['Kontrakan Sukaluyu', 'Jl. Contoh Sukaluyu No. 12, Karawang', -6.3010, 107.3122, 'Akses angkot 24 jam, dekat puskesmas'],
            ['Kontrakan Karangpawitan', 'Jl. Contoh Karangpawitan No. 5, Karawang', -6.2954, 107.3205, 'Dekat Pasar Johar, masjid 100 m'],
            ['Kontrakan Adiarsa', 'Jl. Contoh Adiarsa No. 9, Karawang', -6.3062, 107.2950, 'Dekat stasiun Karawang (±2 km)'],
            ['Kontrakan Tanjungpura', 'Jl. Contoh Tanjungpura No. 21, Karawang', -6.2899, 107.2987, 'Pinggir jalan raya, dekat SPBU'],
            ['Kontrakan Klari', 'Jl. Contoh Klari No. 4, Karawang', -6.3555, 107.3460, 'Dekat kawasan industri Surya Cipta'],
            ['Kontrakan Cikampek', 'Jl. Contoh Cikampek No. 15, Karawang', -6.4083, 107.4600, 'Dekat pabrik & gerbang tol Cikampek'],
        ];

        $lokasi = collect($lokasiData)->map(fn ($l) => Lokasi::create([
            'nama' => $l[0],
            'alamat' => $l[1],
            'latitude' => $l[2],
            'longitude' => $l[3],
            'lingkungan_sekitar' => $l[4],
        ]));

        // 11 unit: dua lokasi pertama masing-masing punya 2 pintu.
        $lokasiPerUnit = [0, 0, 1, 1, 2, 3, 4, 5, 6, 7, 8];
        $units = collect($lokasiPerUnit)->map(function (int $i, int $n) use ($lokasi) {
            $tarif = [850_000, 850_000, 1_000_000, 1_000_000, 750_000, 900_000, 1_200_000, 800_000, 950_000, 1_100_000, 1_000_000][$n];

            return Unit::create([
                'lokasi_id' => $lokasi[$i]->id,
                'nomor_unit' => sprintf('U-%02d', $n + 1),
                'tarif_bulanan' => $tarif,
                'tarif_tahunan' => $tarif * 11,
                'jumlah_kamar' => $n % 3 === 0 ? 2 : 1,
                'luas_bangunan' => $n % 3 === 0 ? 45 : 30,
                'fasilitas' => 'Kamar mandi dalam, dapur, listrik token, air sumur',
                'status' => StatusUnit::Kosong,
                'ketentuan_penghuni' => $n % 2 === 0 ? 'Pasutri' : 'Sesama jenis',
                'izin_hewan' => $n % 4 !== 0,
            ]);
        });

        // Penyewa demo yang punya akun login.
        $userPenyewa = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'penyewa@kontrakan.test',
            'no_whatsapp' => '081200000001',
        ]);

        $penyewaData = [
            ['Budi Santoso', '081200000001', $userPenyewa->id],
            ['Siti Rahmawati', '081200000002', null],
            ['Andi Pratama', '081200000003', null],
            ['Dewi Lestari', '081200000004', null],
            ['Rizky Hidayat', '081200000005', null],
            ['Nur Aisyah', '081200000006', null],
            ['Joko Susilo', '081200000007', null],
        ];

        $penyewa = collect($penyewaData)->map(fn ($p, $i) => Penyewa::create([
            'user_id' => $p[2],
            'nama' => $p[0],
            'nik' => '32150'.str_pad((string) ($i + 1), 11, '0', STR_PAD_LEFT),
            'no_whatsapp' => $p[1],
            'alamat_asal' => 'Karawang',
        ]));

        $bulanIni = Carbon::now()->startOfMonth();

        // [indeks unit, indeks penyewa, periode, bulan mulai (mundur), jumlah bulan nunggak di akhir]
        $kontrakData = [
            [0, 0, PeriodeSewa::Bulanan, 5, 1],
            [2, 1, PeriodeSewa::Bulanan, 8, 0],
            [3, 2, PeriodeSewa::Bulanan, 3, 2],
            [5, 3, PeriodeSewa::Tahunan, 4, 0],
            [6, 4, PeriodeSewa::Bulanan, 6, 0],
            [8, 5, PeriodeSewa::Bulanan, 2, 1],
            [9, 6, PeriodeSewa::Bulanan, 10, 0],
        ];

        foreach ($kontrakData as [$u, $p, $periode, $mundur, $nunggak]) {
            $unit = $units[$u];
            // Mundur 10 hari dari hari ini supaya tagihan terbaru selalu sudah lewat,
            // jadi tanggal bayar/verifikasi tidak pernah berada di masa depan.
            $mulai = Carbon::today()->subMonths($mundur)->subDays(10);
            $tahunan = $periode === PeriodeSewa::Tahunan;

            $kontrak = KontrakSewa::create([
                'unit_id' => $unit->id,
                'penyewa_id' => $penyewa[$p]->id,
                'periode' => $periode,
                'tanggal_mulai' => $mulai,
                'tanggal_selesai' => $mulai->copy()->addYear()->subDay(),
                'tarif' => $tahunan ? $unit->tarif_tahunan : $unit->tarif_bulanan,
                'status' => StatusKontrak::Aktif,
            ]);
            $unit->update(['status' => StatusUnit::Terisi]);

            $jumlahTagihan = $tahunan ? 1 : $mundur + 1;
            for ($k = 0; $k < $jumlahTagihan; $k++) {
                $jatuhTempo = $mulai->copy()->addMonths($k);
                $lunas = $k < $jumlahTagihan - $nunggak;

                $kontrak->pembayaran()->create([
                    'jatuh_tempo' => $jatuhTempo,
                    'nominal' => $kontrak->tarif,
                    'status' => $lunas ? StatusPembayaran::Lunas : StatusPembayaran::Nunggak,
                    'tanggal_bayar' => $lunas ? $jatuhTempo->copy()->addDays(rand(0, 3)) : null,
                    'metode' => $lunas ? ($k % 2 ? MetodeBayar::Tunai : MetodeBayar::Transfer) : null,
                    'diverifikasi_pada' => $lunas ? $jatuhTempo->copy()->addDays(4) : null,
                ]);
            }
        }

        // Kontrak yang masih diajukan (menunggu persetujuan pemilik).
        $pemohon = Penyewa::create([
            'nama' => 'Fitri Handayani',
            'no_whatsapp' => '081200000008',
        ]);
        KontrakSewa::create([
            'unit_id' => $units[1]->id,
            'penyewa_id' => $pemohon->id,
            'periode' => PeriodeSewa::Bulanan,
            'tanggal_mulai' => $bulanIni->copy()->addMonth(),
            'tanggal_selesai' => $bulanIni->copy()->addMonth()->addYear()->subDay(),
            'tarif' => $units[1]->tarif_bulanan,
            'status' => StatusKontrak::Diajukan,
        ]);

        // Kerusakan dilaporkan penyewa + biaya perbaikan.
        $bocor = Perbaikan::create([
            'unit_id' => $units[0]->id,
            'penyewa_id' => $penyewa[0]->id,
            'jenis' => JenisPerbaikan::Kerusakan,
            'judul' => 'Atap kamar mandi bocor',
            'deskripsi' => 'Air menetes saat hujan deras.',
            'tanggal_lapor' => $bulanIni->copy()->subMonth()->addDays(10),
            'tanggal_selesai' => $bulanIni->copy()->subMonth()->addDays(13),
            'status' => StatusPerbaikan::Selesai,
        ]);
        $this->biaya($bocor, [
            [KategoriPengeluaran::Material, 'Genteng & semen', 275_000],
            [KategoriPengeluaran::UpahTukang, 'Upah tukang 1 hari', 150_000],
        ]);

        Perbaikan::create([
            'unit_id' => $units[3]->id,
            'penyewa_id' => $penyewa[2]->id,
            'jenis' => JenisPerbaikan::Kerusakan,
            'judul' => 'Keran dapur rusak',
            'tanggal_lapor' => Carbon::today()->subDays(3),
            'status' => StatusPerbaikan::Dilaporkan,
        ]);

        // Unit sedang direnovasi (upgrade) -> status perbaikan.
        $units[4]->update(['status' => StatusUnit::Perbaikan]);
        $cat = Perbaikan::create([
            'unit_id' => $units[4]->id,
            'jenis' => JenisPerbaikan::Upgrade,
            'judul' => 'Pengecatan ulang seluruh unit',
            'tanggal_lapor' => Carbon::today()->subDays(5),
            'status' => StatusPerbaikan::Diproses,
        ]);
        $this->biaya($cat, [
            [KategoriPengeluaran::Material, 'Cat tembok 2 pail', 640_000],
            [KategoriPengeluaran::UpahTukang, 'Upah tukang cat 3 hari', 450_000],
        ]);
    }

    /** @param  array<int, array{0: KategoriPengeluaran, 1: string, 2: int}>  $rincian */
    private function biaya(Perbaikan $perbaikan, array $rincian): void
    {
        foreach ($rincian as [$kategori, $ket, $nominal]) {
            $perbaikan->biaya()->create([
                'unit_id' => $perbaikan->unit_id,
                'kategori' => $kategori,
                'tanggal' => $perbaikan->tanggal_selesai ?? $perbaikan->tanggal_lapor,
                'keterangan' => $ket,
                'nominal' => $nominal,
            ]);
        }
    }
}
