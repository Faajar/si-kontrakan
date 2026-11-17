<?php

namespace App\Models;

use App\Enums\MetodeBayar;
use App\Enums\StatusPembayaran;
use App\Support\Phone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'kontrak_sewa_id', 'jatuh_tempo', 'nominal', 'tanggal_bayar', 'metode',
        'bukti_bayar', 'status', 'diverifikasi_pada', 'catatan',
    ];

    protected function casts(): array
    {
        return [
            'jatuh_tempo' => 'date',
            'tanggal_bayar' => 'date',
            'diverifikasi_pada' => 'datetime',
            'nominal' => 'integer',
            'metode' => MetodeBayar::class,
            'status' => StatusPembayaran::class,
        ];
    }

    public function kontrak(): BelongsTo
    {
        return $this->belongsTo(KontrakSewa::class, 'kontrak_sewa_id');
    }

    /** Penyewa sudah unggah bukti tapi pemilik belum menetapkan lunas. */
    public function menungguVerifikasi(): bool
    {
        return $this->status === StatusPembayaran::Nunggak && $this->bukti_bayar !== null;
    }

    /** Tautan wa.me berisi draf pesan penagihan (nama, unit, nominal, jatuh tempo). */
    public function linkPengingatWa(): string
    {
        $k = $this->kontrak;
        $pesan = "Assalamualaikum {$k->penyewa->nama}, kami mengingatkan tagihan sewa unit {$k->unit->nomor_unit} "
            .'sebesar '.rupiah($this->nominal).' yang jatuh tempo '.$this->jatuh_tempo->translatedFormat('d F Y')
            .'. Mohon segera melakukan pembayaran. Terima kasih.';

        return Phone::waLink($k->penyewa->no_whatsapp, $pesan);
    }
}
