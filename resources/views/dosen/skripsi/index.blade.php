<x-app-layout>
    <x-slot name="header">
        Bimbingan Skripsi
    </x-slot>

    <div class="mb-6 flex items-center justify-between">
        <p class="text-sm text-siakad-secondary">Daftar mahasiswa bimbingan skripsi Anda</p>
        @if($pendingBimbingan > 0)
        <span class="px-3 py-1 text-sm font-medium bg-amber-100 text-amber-700 rounded-full">{{ $pendingBimbingan }} menunggu review</span>
        @endif
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card-saas p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-siakad-dark">{{ $skripsiList->count() }}</p>
                <p class="text-xs text-siakad-secondary">Total Bimbingan</p>
            </div>
        </div>
        <div class="card-saas p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-siakad-dark">{{ $skripsiList->where('status', 'bimbingan')->count() }}</p>
                <p class="text-xs text-siakad-secondary">Aktif Bimbingan</p>
            </div>
        </div>
        <div class="card-saas p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-siakad-dark">{{ $pendingBimbingan }}</p>
                <p class="text-xs text-siakad-secondary">Menunggu Review</p>
            </div>
        </div>
        <div class="card-saas p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-siakad-dark">{{ $skripsiList->where('status', 'selesai')->count() }}</p>
                <p class="text-xs text-siakad-secondary">Selesai</p>
            </div>
        </div>
    </div>

    <!-- Skripsi List -->
    <div class="card-saas overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-saas">
                <thead>
                    <tr class="bg-siakad-light/30">
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">#</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Mahasiswa</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Judul</th>
                        <th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Progress</th>
                        <th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Status</th>
                        <th class="text-right py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skripsiList as $index => $skripsi)
                    <tr class="border-b border-siakad-light/50">
                        <td class="py-4 px-5 text-sm text-siakad-secondary">{{ $index + 1 }}</td>
                        <td class="py-4 px-5">
                            <p class="font-medium text-siakad-dark">{{ $skripsi->mahasiswa->user->name }}</p>
                            <p class="text-xs text-siakad-secondary">{{ $skripsi->mahasiswa->nim }}</p>
                        </td>
                        <td class="py-4 px-5">
                            <p class="text-sm text-siakad-dark" title="{{ $skripsi->judul }}">{{ Str::limit($skripsi->judul, 60) }}</p>
                        </td>
                        <td class="py-4 px-5">
                            <div class="w-24 mx-auto">
                                <div class="h-2 bg-siakad-light rounded-full overflow-hidden">
                                    <div class="h-full bg-siakad-primary rounded-full" style="width: {{ $skripsi->progress_percent }}%"></div>
                                </div>
                                <p class="text-xs text-siakad-secondary text-center mt-1">{{ $skripsi->progress_percent }}%</p>
                            </div>
                        </td>
                        <td class="py-4 px-5 text-center">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-{{ $skripsi->status_color }}-100 text-{{ $skripsi->status_color }}-700">{{ $skripsi->status_label }}</span>
                        </td>
                        <td class="py-4 px-5 text-right">
                            <a href="{{ route('dosen.skripsi.show', $skripsi) }}" class="inline-flex items-center gap-1 text-sm text-siakad-primary hover:underline">
                                Detail
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-siakad-secondary">Belum ada mahasiswa bimbingan skripsi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
