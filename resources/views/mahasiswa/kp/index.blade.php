<x-app-layout>
    <x-slot name="header">Kerja Praktek</x-slot>

    @if(!$kp)
    <div class="card-saas p-12 text-center">
        <div class="w-20 h-20 rounded-full bg-siakad-light/50 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-siakad-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-siakad-dark mb-2">Belum Ada Pengajuan KP</h3>
        <p class="text-siakad-secondary mb-6">Ajukan kerja praktek untuk memulai</p>
        <a href="{{ route('mahasiswa.kp.create') }}" class="btn-primary-saas px-6 py-3 rounded-lg text-sm font-medium inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Ajukan KP
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="lg:col-span-2 card-saas p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-{{ $kp->status_color }}-100 text-{{ $kp->status_color }}-700">{{ $kp->status_label }}</span>
                    <h2 class="text-lg font-bold text-siakad-dark mt-3">{{ $kp->nama_perusahaan }}</h2>
                    <p class="text-sm text-siakad-secondary">{{ $kp->bidang_usaha }}</p>
                </div>
                <span class="text-2xl font-bold text-siakad-primary">{{ $kp->progress_percent }}%</span>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
                <div class="p-3 rounded-lg bg-siakad-light/30">
                    <p class="text-xs text-siakad-secondary">Periode</p>
                    <p class="font-medium text-siakad-dark">{{ $kp->tanggal_mulai->format('d M') }} - {{ $kp->tanggal_selesai->format('d M Y') }}</p>
                    <p class="text-xs text-siakad-secondary">({{ $kp->durasi }})</p>
                </div>
                <div class="p-3 rounded-lg bg-siakad-light/30">
                    <p class="text-xs text-siakad-secondary">Pembimbing Kampus</p>
                    <p class="font-medium text-siakad-dark">{{ $kp->pembimbing?->user->name ?? 'Belum ditentukan' }}</p>
                </div>
                <div class="p-3 rounded-lg bg-siakad-light/30">
                    <p class="text-xs text-siakad-secondary">Pembimbing Lapangan</p>
                    <p class="font-medium text-siakad-dark">{{ $kp->nama_pembimbing_lapangan ?? '-' }}</p>
                </div>
                <div class="p-3 rounded-lg bg-siakad-light/30">
                    <p class="text-xs text-siakad-secondary">Alamat</p>
                    <p class="font-medium text-siakad-dark text-xs">{{ $kp->alamat_perusahaan ?? '-' }}</p>
                </div>
            </div>
        </div>
        <div class="card-saas p-5">
            <h3 class="font-semibold text-siakad-dark mb-4">Progress</h3>
            <div class="h-3 bg-siakad-light rounded-full overflow-hidden mb-4">
                <div class="h-full bg-siakad-primary rounded-full" style="width: {{ $kp->progress_percent }}%"></div>
            </div>
            <div class="text-center">
                <p class="text-3xl font-bold text-siakad-primary">{{ $logbookList->count() }}</p>
                <p class="text-sm text-siakad-secondary">Logbook Entries</p>
            </div>
        </div>
    </div>

    @if(in_array($kp->status, ['berlangsung', 'disetujui']))
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card-saas overflow-hidden">
            <div class="px-6 py-4 border-b border-siakad-light"><h3 class="font-semibold text-siakad-dark">Logbook Kegiatan</h3></div>
            @forelse($logbookList as $log)
            <div class="p-4 border-b border-siakad-light/50 flex items-start gap-4">
                <div class="text-center">
                    <p class="text-lg font-bold text-siakad-primary">{{ $log->tanggal->format('d') }}</p>
                    <p class="text-xs text-siakad-secondary">{{ $log->tanggal->format('M') }}</p>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs text-siakad-secondary">{{ $log->jam_masuk ?? '-' }} - {{ $log->jam_keluar ?? '-' }}</span>
                        <span class="px-2 py-0.5 text-xs rounded-full bg-{{ $log->status_color }}-100 text-{{ $log->status_color }}-700">{{ $log->status_label }}</span>
                    </div>
                    <p class="text-sm text-siakad-dark">{{ $log->kegiatan }}</p>
                    @if($log->catatan_pembimbing)
                    <p class="text-xs text-emerald-600 mt-2 p-2 bg-emerald-50 rounded">{{ $log->catatan_pembimbing }}</p>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-siakad-secondary">Belum ada logbook</div>
            @endforelse
        </div>
        <div class="card-saas p-6">
            <h3 class="font-semibold text-siakad-dark mb-4">Tambah Logbook</h3>
            <form action="{{ route('mahasiswa.kp.logbook.store') }}" method="POST" class="space-y-4">
                @csrf
                <div><label class="block text-xs font-medium text-siakad-dark mb-1">Tanggal</label><input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="input-saas w-full text-sm" required></div>
                <div class="grid grid-cols-2 gap-2">
                    <div><label class="block text-xs font-medium text-siakad-dark mb-1">Jam Masuk</label><input type="time" name="jam_masuk" class="input-saas w-full text-sm"></div>
                    <div><label class="block text-xs font-medium text-siakad-dark mb-1">Jam Keluar</label><input type="time" name="jam_keluar" class="input-saas w-full text-sm"></div>
                </div>
                <div><label class="block text-xs font-medium text-siakad-dark mb-1">Kegiatan</label><textarea name="kegiatan" rows="3" class="input-saas w-full text-sm" required></textarea></div>
                <button type="submit" class="btn-primary-saas w-full py-2.5 rounded-lg text-sm font-medium">Simpan</button>
            </form>
        </div>
    </div>
    @endif
    @endif
</x-app-layout>
