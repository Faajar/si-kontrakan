<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusUnit;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UnitRequest;
use App\Models\Lokasi;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function index(Request $request): View
    {
        $filter = $request->validate([
            'lokasi_id' => ['nullable', 'integer'],
            'status' => ['nullable', Rule::enum(StatusUnit::class)],
        ]);

        $units = Unit::with(['lokasi', 'kontrakAktif.penyewa'])
            ->when($filter['lokasi_id'] ?? null, fn ($q, $id) => $q->where('lokasi_id', $id))
            ->when($filter['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->orderBy('nomor_unit')
            ->paginate(15)
            ->withQueryString();

        return view('admin.unit.index', [
            'units' => $units,
            'lokasiList' => Lokasi::orderBy('nama')->pluck('nama', 'id'),
            'filter' => $filter,
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.unit.form', [
            'unit' => new Unit(['lokasi_id' => $request->integer('lokasi_id') ?: null, 'jumlah_kamar' => 1]),
            'lokasiList' => Lokasi::orderBy('nama')->pluck('nama', 'id'),
        ]);
    }

    public function store(UnitRequest $request): RedirectResponse
    {
        $unit = Unit::create($request->validated());

        return redirect()->route('admin.unit.show', $unit)->with('success', 'Unit berhasil ditambahkan.');
    }

    public function show(Unit $unit): View
    {
        $unit->load([
            'lokasi',
            'kontrakAktif.penyewa',
            'perbaikan' => fn ($q) => $q->withSum('biaya', 'nominal')->latest('tanggal_lapor')->limit(10),
        ]);

        return view('admin.unit.show', [
            'unit' => $unit,
            'totalPengeluaran' => $unit->pengeluaran()->sum('nominal'),
        ]);
    }

    public function edit(Unit $unit): View
    {
        return view('admin.unit.form', [
            'unit' => $unit,
            'lokasiList' => Lokasi::orderBy('nama')->pluck('nama', 'id'),
        ]);
    }

    public function update(UnitRequest $request, Unit $unit): RedirectResponse
    {
        $unit->update($request->validated());

        return redirect()->route('admin.unit.show', $unit)->with('success', 'Unit berhasil diperbarui.');
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        if ($unit->kontrak()->exists() || $unit->perbaikan()->exists()) {
            return back()->with('error', 'Unit punya riwayat kontrak/perbaikan, tidak bisa dihapus. Ubah statusnya saja.');
        }

        $unit->delete();

        return redirect()->route('admin.unit.index')->with('success', 'Unit berhasil dihapus.');
    }
}
