<table class="w-full table-saas">
    <thead>
        <tr class="bg-siakad-light/30">
            <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider w-16">#</th>
            <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Mahasiswa</th>
            <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">NIM</th>
            <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Angkatan</th>
            <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($mahasiswaBimbingan as $index => $m)
        <tr class="border-b border-siakad-light/50">
            <td class="py-4 px-5 text-sm text-siakad-secondary">{{ $mahasiswaBimbingan->firstItem() + $index }}</td>
            <td class="py-4 px-5">
                <span class="text-sm font-medium text-siakad-dark">{{ $m->user->name ?? '-' }}</span>
            </td>
            <td class="py-4 px-5">
                <span class="text-sm font-mono text-siakad-secondary">{{ $m->nim }}</span>
            </td>
            <td class="py-4 px-5">
                <span class="inline-flex px-2.5 py-1 text-xs font-medium bg-siakad-primary/10 text-siakad-primary rounded-full">{{ $m->angkatan }}</span>
            </td>
            <td class="py-4 px-5">
                <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold bg-emerald-100 text-emerald-700 rounded-full">Aktif</span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="py-12 text-center">
                <div class="flex flex-col items-center">
                    <div class="w-12 h-12 bg-siakad-light/50 rounded-xl flex items-center justify-center mb-3">
                        <svg class="w-6 h-6 text-siakad-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <p class="text-siakad-secondary text-sm">Tidak ada data ditemukan</p>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
@if($mahasiswaBimbingan->hasPages())
<div class="px-5 py-4 border-t border-siakad-light">
    {{ $mahasiswaBimbingan->links() }}
</div>
@endif
