<x-app-layout>
    <x-slot name="header">Bimbingan KP</x-slot>
    <div class="mb-6 flex items-center justify-between">
        <p class="text-sm text-siakad-secondary">Mahasiswa Kerja Praktek bimbingan Anda</p>
        @if($pendingLogbook > 0)<span class="px-3 py-1 text-sm bg-amber-100 text-amber-700 rounded-full">{{ $pendingLogbook }} logbook pending</span>@endif
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="card-saas p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center"><svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></div>
            <div><p class="text-2xl font-bold text-siakad-dark">{{ $kpList->count() }}</p><p class="text-xs text-siakad-secondary">Total</p></div>
        </div>
        <div class="card-saas p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center"><svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
            <div><p class="text-2xl font-bold text-siakad-dark">{{ $kpList->where('status', 'berlangsung')->count() }}</p><p class="text-xs text-siakad-secondary">Berlangsung</p></div>
        </div>
        <div class="card-saas p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>
            <div><p class="text-2xl font-bold text-siakad-dark">{{ $kpList->where('status', 'selesai')->count() }}</p><p class="text-xs text-siakad-secondary">Selesai</p></div>
        </div>
    </div>
    <div class="card-saas overflow-hidden">
        <table class="w-full table-saas">
            <thead><tr class="bg-siakad-light/30"><th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Mahasiswa</th><th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Perusahaan</th><th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Periode</th><th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Status</th><th class="text-right py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Aksi</th></tr></thead>
            <tbody>
            @forelse($kpList as $kp)
            <tr class="border-b border-siakad-light/50">
                <td class="py-4 px-5"><p class="font-medium text-siakad-dark">{{ $kp->mahasiswa->user->name }}</p><p class="text-xs text-siakad-secondary">{{ $kp->mahasiswa->nim }}</p></td>
                <td class="py-4 px-5 text-sm text-siakad-dark">{{ $kp->nama_perusahaan }}</td>
                <td class="py-4 px-5 text-center text-xs text-siakad-secondary">{{ $kp->tanggal_mulai->format('d/m') }} - {{ $kp->tanggal_selesai->format('d/m/Y') }}</td>
                <td class="py-4 px-5 text-center"><span class="px-2.5 py-1 text-xs font-medium rounded-full bg-{{ $kp->status_color }}-100 text-{{ $kp->status_color }}-700">{{ $kp->status_label }}</span></td>
                <td class="py-4 px-5 text-right"><a href="{{ route('dosen.kp.show', $kp) }}" class="text-sm text-siakad-primary hover:underline">Detail →</a></td>
            </tr>
            @empty
            <tr><td colspan="5" class="py-12 text-center text-siakad-secondary">Belum ada mahasiswa KP</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
