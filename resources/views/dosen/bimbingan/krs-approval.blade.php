<x-app-layout>
    <x-slot name="header">
        Approval KRS Mahasiswa Bimbingan
    </x-slot>

    <div class="mb-6">
        <p class="text-sm text-siakad-secondary">Daftar KRS mahasiswa bimbingan yang menunggu persetujuan</p>
    </div>

    <!-- Table Card -->
    <div class="card-saas overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-saas">
                <thead>
                    <tr class="bg-siakad-light/30">
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider w-16">#</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Mahasiswa</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">NIM</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Total SKS</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Status</th>
                        <th class="text-right py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($krsList as $index => $krs)
                    <tr class="border-b border-siakad-light/50">
                        <td class="py-4 px-5 text-sm text-siakad-secondary">{{ $index + 1 }}</td>
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-siakad-dark">{{ $krs->mahasiswa->user->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-5">
                            <span class="text-sm font-mono text-siakad-secondary">{{ $krs->mahasiswa->nim ?? '-' }}</span>
                        </td>
                        <td class="py-4 px-5">
                            <span class="inline-flex px-2.5 py-1 text-xs font-medium bg-siakad-primary/10 text-siakad-primary rounded-full">{{ $krs->krsDetail?->sum(fn($d) => $d->kelas->mataKuliah->sks ?? 0) ?? 0 }} SKS</span>
                        </td>
                        <td class="py-4 px-5">
                            @if($krs->status === 'approved')
                            <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold bg-emerald-100 text-emerald-700 rounded-full">Approved</span>
                            @elseif($krs->status === 'pending')
                            <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold bg-amber-100 text-amber-700 rounded-full">Pending</span>
                            @elseif($krs->status === 'rejected')
                            <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold bg-red-100 text-red-700 rounded-full">Rejected</span>
                            @else
                            <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold bg-slate-100 text-slate-600 rounded-full">Draft</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ url('dosen/bimbingan/krs/'.$krs->id) }}" class="inline-flex p-2 text-siakad-secondary hover:text-siakad-primary hover:bg-siakad-primary/10 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                @if($krs->status === 'pending')
                                <form action="{{ url('dosen/bimbingan/krs/'.$krs->id.'/approve') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-2 text-siakad-secondary hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Approve">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </button>
                                </form>
                                <button type="button" onclick="openRejectModal('{{ url('dosen/bimbingan/krs/'.$krs->id.'/reject') }}')" class="p-2 text-siakad-secondary hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Reject">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 bg-siakad-light/50 rounded-xl flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-siakad-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                </div>
                                <p class="text-siakad-secondary text-sm">Tidak ada KRS yang menunggu persetujuan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-md shadow-xl">
            <div class="px-6 py-4 border-b border-siakad-light">
                <h3 class="text-lg font-semibold text-siakad-dark">Tolak KRS</h3>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="p-6">
                    <label class="block text-sm font-medium text-siakad-dark mb-2">Alasan Penolakan</label>
                    <textarea name="catatan" rows="4" class="input-saas w-full resize-none" placeholder="Masukkan alasan mengapa KRS ditolak... (opsional)"></textarea>
                    <p class="text-xs text-siakad-secondary mt-2">Catatan ini akan dilihat oleh mahasiswa sebagai alasan penolakan.</p>
                </div>
                <div class="px-6 py-4 border-t border-siakad-light flex items-center justify-end gap-3">
                    <button type="button" onclick="closeRejectModal()" class="btn-ghost-saas px-4 py-2 rounded-lg text-sm">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition">Tolak KRS</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(url) {
            document.getElementById('rejectForm').action = url;
            document.getElementById('rejectModal').classList.remove('hidden');
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
