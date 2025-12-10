<x-app-layout>
    <x-slot name="header">Manajemen Kerja Praktek</x-slot>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        @foreach([['Total', $stats['total'], 'indigo'], ['Aktif', $stats['aktif'], 'blue'], ['Perlu Pembimbing', $stats['perlu_pembimbing'], 'amber'], ['Selesai', $stats['selesai'], 'emerald']] as $s)
        <div class="card-saas p-4 flex items-center gap-3"><div class="w-10 h-10 rounded-lg bg-{{ $s[2] }}-100 flex items-center justify-center"><svg class="w-5 h-5 text-{{ $s[2] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></div><div><p class="text-2xl font-bold text-siakad-dark">{{ $s[1] }}</p><p class="text-xs text-siakad-secondary">{{ $s[0] }}</p></div></div>
        @endforeach
    </div>
    <div class="card-saas p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]"><label class="block text-xs font-medium text-siakad-dark mb-1">Cari</label><input type="text" name="search" value="{{ request('search') }}" class="input-saas w-full text-sm" placeholder="Nama, NIM, perusahaan..."></div>
            <div class="w-48"><label class="block text-xs font-medium text-siakad-dark mb-1">Status</label><select name="status" class="input-saas w-full text-sm"><option value="">Semua</option>@foreach($statusList as $k => $v)<option value="{{ $k }}" {{ request('status') === $k ? 'selected' : '' }}>{{ $v }}</option>@endforeach</select></div>
            <button type="submit" class="btn-primary-saas px-4 py-2.5 rounded-lg text-sm font-medium">Filter</button>
        </form>
    </div>
    <div class="card-saas overflow-hidden">
        <table class="w-full table-saas">
            <thead><tr class="bg-siakad-light/30"><th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Mahasiswa</th><th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Perusahaan</th><th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Pembimbing</th><th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Status</th><th class="text-right py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Aksi</th></tr></thead>
            <tbody>
            @forelse($kpList as $kp)
            <tr class="border-b border-siakad-light/50">
                <td class="py-4 px-5"><p class="font-medium text-siakad-dark">{{ $kp->mahasiswa->user->name }}</p><p class="text-xs text-siakad-secondary">{{ $kp->mahasiswa->nim }}</p></td>
                <td class="py-4 px-5 text-sm text-siakad-dark">{{ $kp->nama_perusahaan }}</td>
                <td class="py-4 px-5 text-sm">@if($kp->pembimbing){{ $kp->pembimbing->user->name }}@else<span class="text-amber-600 text-xs">Belum</span>@endif</td>
                <td class="py-4 px-5 text-center"><span class="px-2.5 py-1 text-xs font-medium rounded-full bg-{{ $kp->status_color }}-100 text-{{ $kp->status_color }}-700">{{ $kp->status_label }}</span></td>
                <td class="py-4 px-5 text-right"><a href="{{ route('admin.kp.show', $kp) }}" class="text-sm text-siakad-primary hover:underline">Detail</a></td>
            </tr>
            @empty
            <tr><td colspan="5" class="py-12 text-center text-siakad-secondary">Tidak ada data</td></tr>
            @endforelse
            </tbody>
        </table>
        @if($kpList->hasPages())<div class="px-5 py-4 border-t border-siakad-light">{{ $kpList->links() }}</div>@endif
    </div>
</x-app-layout>
