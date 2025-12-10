<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Services\PenilaianService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    protected $penilaianService;

    public function __construct(PenilaianService $penilaianService)
    {
        $this->penilaianService = $penilaianService;
    }

    public function index()
    {
        $dosen = Auth::user()->dosen;
        $kelasAjar = $dosen->kelas()->with(['mataKuliah', 'krsDetail'])->get();
        return view('dosen.penilaian.index', compact('kelasAjar'));
    }

    public function show($kelasId)
    {
        $dosen = Auth::user()->dosen;
        $kelas = $dosen->kelas()->with(['mataKuliah', 'krsDetail.krs.mahasiswa.user', 'nilai'])->findOrFail($kelasId);
        
        return view('dosen.penilaian.show', compact('kelas'));
    }

    public function store(Request $request, $kelasId)
    {
        $request->validate([
            'nilai' => 'required|array',
            'nilai.*' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $this->penilaianService->bulkInputNilai($kelasId, $request->nilai);
            return redirect()->back()->with('success', 'Nilai berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
