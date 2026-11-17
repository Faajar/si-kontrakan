<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['izin_hewan' => $this->boolean('izin_hewan')]);
    }

    public function rules(): array
    {
        $unitId = $this->route('unit')?->id;

        return [
            'lokasi_id' => ['required', 'integer', 'exists:lokasi,id'],
            'nomor_unit' => ['required', 'string', 'max:30', Rule::unique('unit', 'nomor_unit')->ignore($unitId)],
            'tarif_bulanan' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'tarif_tahunan' => ['nullable', 'integer', 'min:0', 'max:10000000000'],
            'jumlah_kamar' => ['required', 'integer', 'min:1', 'max:20'],
            'luas_bangunan' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'fasilitas' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::enum(StatusUnit::class)],
            'ketentuan_penghuni' => ['nullable', 'string', 'max:255'],
            'izin_hewan' => ['boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'lokasi_id' => 'lokasi',
            'nomor_unit' => 'nomor unit',
            'tarif_bulanan' => 'tarif bulanan',
            'tarif_tahunan' => 'tarif tahunan',
            'jumlah_kamar' => 'jumlah kamar',
            'luas_bangunan' => 'luas bangunan',
            'ketentuan_penghuni' => 'ketentuan penghuni',
        ];
    }
}
