<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LokasiRequest;
use App\Models\Lokasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LokasiController extends Controller
{
    public function index(): View
    {
        $lokasi = Lokasi::withCount('units')->orderBy('nama')->paginate(15);

        return view('admin.lokasi.index', compact('lokasi'));
    }

    public function create(): View
    {
        return view('admin.lokasi.form', ['lokasi' => new Lokasi]);
    }

    public function store(LokasiRequest $request): RedirectResponse
    {
        Lokasi::create($request->validated());

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function show(Lokasi $lokasi): View
    {
        $lokasi->load(['units' => fn ($q) => $q->orderBy('nomor_unit')]);

        return view('admin.lokasi.show', compact('lokasi'));
    }

    public function edit(Lokasi $lokasi): View
    {
        return view('admin.lokasi.form', compact('lokasi'));
    }

    public function update(LokasiRequest $request, Lokasi $lokasi): RedirectResponse
    {
        $lokasi->update($request->validated());

        return redirect()->route('admin.lokasi.show', $lokasi)->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Lokasi $lokasi): RedirectResponse
    {
        if ($lokasi->units()->exists()) {
            return back()->with('error', 'Lokasi masih punya unit. Pindahkan atau hapus unitnya dulu.');
        }

        $lokasi->delete();

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil dihapus.');
    }
}
