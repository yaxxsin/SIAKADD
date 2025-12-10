<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index()
    {
        $ruanganList = Ruangan::orderBy('kode_ruangan')->get();
        
        // Group by gedung for stats
        $perGedung = $ruanganList->groupBy('gedung');
        
        return view('admin.ruangan.index', compact('ruanganList', 'perGedung'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_ruangan' => 'required|string|max:20|unique:ruangan,kode_ruangan',
            'nama_ruangan' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:1',
            'gedung' => 'nullable|string|max:50',
            'lantai' => 'nullable|integer|min:1',
            'fasilitas' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Ruangan::create($validated);

        return redirect()->back()->with('success', 'Ruangan berhasil ditambahkan');
    }

    public function update(Request $request, Ruangan $ruangan)
    {
        $validated = $request->validate([
            'kode_ruangan' => 'required|string|max:20|unique:ruangan,kode_ruangan,' . $ruangan->id,
            'nama_ruangan' => 'required|string|max:100',
            'kapasitas' => 'required|integer|min:1',
            'gedung' => 'nullable|string|max:50',
            'lantai' => 'nullable|integer|min:1',
            'fasilitas' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $ruangan->update($validated);

        return redirect()->back()->with('success', 'Ruangan berhasil diupdate');
    }

    public function destroy(Ruangan $ruangan)
    {
        $ruangan->delete();
        return redirect()->back()->with('success', 'Ruangan berhasil dihapus');
    }
}
