<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KerjaPraktek;
use App\Models\Dosen;
use Illuminate\Http\Request;

class KpController extends Controller
{
    public function index(Request $request)
    {
        $query = KerjaPraktek::with(['mahasiswa.user', 'pembimbing.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_perusahaan', 'like', "%{$search}%")
                    ->orWhereHas('mahasiswa.user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('mahasiswa', fn($q) => $q->where('nim', 'like', "%{$search}%"));
            });
        }

        $kpList = $query->orderBy('created_at', 'desc')->paginate(20);
        $dosenList = Dosen::with('user')->get();
        $statusList = KerjaPraktek::getStatusList();

        $stats = [
            'total' => KerjaPraktek::count(),
            'aktif' => KerjaPraktek::active()->count(),
            'perlu_pembimbing' => KerjaPraktek::whereNull('pembimbing_id')->count(),
            'selesai' => KerjaPraktek::where('status', KerjaPraktek::STATUS_SELESAI)->count(),
        ];

        return view('admin.kp.index', compact('kpList', 'dosenList', 'statusList', 'stats'));
    }

    public function show(KerjaPraktek $kp)
    {
        $kp->load(['mahasiswa.user', 'pembimbing.user', 'logbook']);
        $dosenList = Dosen::with('user')->get();

        return view('admin.kp.show', compact('kp', 'dosenList'));
    }

    public function assignPembimbing(Request $request, KerjaPraktek $kp)
    {
        $validated = $request->validate([
            'pembimbing_id' => 'required|exists:dosen,id',
        ]);

        $kp->update([
            'pembimbing_id' => $validated['pembimbing_id'],
            'status' => KerjaPraktek::STATUS_DISETUJUI,
        ]);

        return redirect()->back()->with('success', 'Pembimbing berhasil ditentukan');
    }

    public function updateStatus(Request $request, KerjaPraktek $kp)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(KerjaPraktek::getStatusList())),
            'catatan' => 'nullable|string',
        ]);

        $kp->update([
            'status' => $validated['status'],
            'catatan' => $validated['catatan'] ?? $kp->catatan,
        ]);

        return redirect()->back()->with('success', 'Status berhasil diupdate');
    }

    public function updateNilai(Request $request, KerjaPraktek $kp)
    {
        $validated = $request->validate([
            'nilai_perusahaan' => 'nullable|numeric|min:0|max:100',
            'nilai_pembimbing' => 'nullable|numeric|min:0|max:100',
            'nilai_seminar' => 'nullable|numeric|min:0|max:100',
            'nilai_akhir' => 'required|numeric|min:0|max:100',
            'nilai_huruf' => 'required|in:A,B+,B,C+,C,D,E',
        ]);

        $kp->update([
            ...$validated,
            'status' => KerjaPraktek::STATUS_SELESAI,
        ]);

        return redirect()->back()->with('success', 'Nilai berhasil disimpan');
    }
}
