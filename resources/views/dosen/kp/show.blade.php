<x-app-layout>
    <x-slot name="header">Detail KP</x-slot>
    <div class="mb-6"><a href="{{ route('dosen.kp.index') }}" class="text-siakad-secondary hover:text-siakad-primary text-sm">← Kembali</a></div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card-saas p-6">
                <div class="flex items-start justify-between mb-4">
                    <div><span class="px-3 py-1 text-xs font-medium rounded-full bg-{{ $kp->status_color }}-100 text-{{ $kp->status_color }}-700">{{ $kp->status_label }}</span><h2 class="text-lg font-bold text-siakad-dark mt-3">{{ $kp->nama_perusahaan }}</h2></div>
                    <span class="text-2xl font-bold text-siakad-primary">{{ $kp->progress_percent }}%</span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="p-3 rounded-lg bg-siakad-light/30"><p class="text-xs text-siakad-secondary">Mahasiswa</p><p class="font-medium text-siakad-dark">{{ $kp->mahasiswa->user->name }}</p><p class="text-xs text-siakad-secondary">{{ $kp->mahasiswa->nim }}</p></div>
                    <div class="p-3 rounded-lg bg-siakad-light/30"><p class="text-xs text-siakad-secondary">Periode</p><p class="font-medium text-siakad-dark">{{ $kp->tanggal_mulai->format('d M') }} - {{ $kp->tanggal_selesai->format('d M Y') }}</p></div>
                </div>
                <form action="{{ route('dosen.kp.update-status', $kp) }}" method="POST" class="mt-4 flex items-end gap-3">@csrf @method('PUT')
                    <div class="flex-1"><label class="block text-xs font-medium text-siakad-dark mb-1">Update Status</label><select name="status" class="input-saas w-full text-sm">@foreach(\App\Models\KerjaPraktek::getStatusList() as $k => $v)<option value="{{ $k }}" {{ $kp->status === $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach</select></div>
                    <button type="submit" class="btn-primary-saas px-4 py-2.5 rounded-lg text-sm font-medium">Update</button>
                </form>
            </div>
            <div class="card-saas overflow-hidden">
                <div class="px-6 py-4 border-b border-siakad-light"><h3 class="font-semibold text-siakad-dark">Logbook ({{ $kp->logbook->count() }})</h3></div>
                @forelse($kp->logbook as $log)
                <div class="p-4 border-b border-siakad-light/50">
                    <div class="flex items-start gap-4">
                        <div class="text-center"><p class="text-lg font-bold text-siakad-primary">{{ $log->tanggal->format('d') }}</p><p class="text-xs text-siakad-secondary">{{ $log->tanggal->format('M') }}</p></div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1"><span class="text-xs text-siakad-secondary">{{ $log->jam_masuk ?? '-' }} - {{ $log->jam_keluar ?? '-' }}</span><span class="px-2 py-0.5 text-xs rounded-full bg-{{ $log->status_color }}-100 text-{{ $log->status_color }}-700">{{ $log->status_label }}</span></div>
                            <p class="text-sm text-siakad-dark">{{ $log->kegiatan }}</p>
                            @if($log->catatan_pembimbing)<p class="text-xs text-emerald-600 mt-2 p-2 bg-emerald-50 rounded">{{ $log->catatan_pembimbing }}</p>
                            @elseif($log->status === 'pending')
                            <form action="{{ route('dosen.kp.logbook.review', $log) }}" method="POST" class="mt-2 flex items-center gap-2">@csrf
                                <input type="text" name="catatan_pembimbing" class="input-saas flex-1 text-xs py-1" placeholder="Catatan...">
                                <button type="submit" name="status" value="disetujui" class="px-2 py-1 text-xs bg-emerald-600 text-white rounded">✓</button>
                                <button type="submit" name="status" value="revisi" class="px-2 py-1 text-xs bg-red-600 text-white rounded">✗</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-siakad-secondary">Belum ada logbook</div>
                @endforelse
            </div>
        </div>
        <div class="space-y-6">
            <div class="card-saas p-5"><h3 class="font-semibold text-siakad-dark mb-3">Info Perusahaan</h3><div class="text-sm space-y-2"><p class="text-siakad-dark">{{ $kp->nama_perusahaan }}</p><p class="text-xs text-siakad-secondary">{{ $kp->alamat_perusahaan }}</p><p class="text-xs text-siakad-secondary">Bidang: {{ $kp->bidang_usaha ?? '-' }}</p></div></div>
            <div class="card-saas p-5"><h3 class="font-semibold text-siakad-dark mb-3">Pembimbing Lapangan</h3><p class="text-sm text-siakad-dark">{{ $kp->nama_pembimbing_lapangan ?? '-' }}</p><p class="text-xs text-siakad-secondary">{{ $kp->jabatan_pembimbing_lapangan }}</p><p class="text-xs text-siakad-secondary">{{ $kp->no_telp_pembimbing }}</p></div>
        </div>
    </div>
</x-app-layout>
