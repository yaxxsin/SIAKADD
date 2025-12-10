<x-app-layout>
    <x-slot name="header">
        Manajemen Skripsi
    </x-slot>

    <div class="mb-6">
        <p class="text-sm text-siakad-secondary">Kelola semua pengajuan skripsi mahasiswa</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card-saas p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-siakad-dark">{{ $stats['total'] }}</p>
                <p class="text-xs text-siakad-secondary">Total Skripsi</p>
            </div>
        </div>
        <div class="card-saas p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-siakad-dark">{{ $stats['aktif'] }}</p>
                <p class="text-xs text-siakad-secondary">Aktif</p>
            </div>
        </div>
        <div class="card-saas p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-siakad-dark">{{ $stats['menunggu_pembimbing'] }}</p>
                <p class="text-xs text-siakad-secondary">Perlu Pembimbing</p>
            </div>
        </div>
        <div class="card-saas p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-siakad-dark">{{ $stats['selesai'] }}</p>
                <p class="text-xs text-siakad-secondary">Selesai</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card-saas p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-siakad-dark mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="input-saas w-full text-sm" placeholder="Nama, NIM, atau judul...">
            </div>
            <div class="w-48">
                <label class="block text-xs font-medium text-siakad-dark mb-1">Status</label>
                <select name="status" class="input-saas w-full text-sm">
                    <option value="">Semua Status</option>
                    @foreach($statusList as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary-saas px-4 py-2.5 rounded-lg text-sm font-medium">Filter</button>
        </form>
    </div>

    <!-- Table -->
    <div class="card-saas overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-saas">
                <thead>
                    <tr class="bg-siakad-light/30">
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">#</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Mahasiswa</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Judul</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Pembimbing</th>
                        <th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Status</th>
                        <th class="text-right py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skripsiList as $index => $skripsi)
                    <tr class="border-b border-siakad-light/50">
                        <td class="py-4 px-5 text-sm text-siakad-secondary">{{ $skripsiList->firstItem() + $index }}</td>
                        <td class="py-4 px-5">
                            <p class="font-medium text-siakad-dark">{{ $skripsi->mahasiswa->user->name }}</p>
                            <p class="text-xs text-siakad-secondary">{{ $skripsi->mahasiswa->nim }}</p>
                        </td>
                        <td class="py-4 px-5">
                            <p class="text-sm text-siakad-dark" title="{{ $skripsi->judul }}">{{ Str::limit($skripsi->judul, 50) }}</p>
                            @if($skripsi->bidang_kajian)
                            <p class="text-xs text-siakad-secondary">{{ $skripsi->bidang_kajian }}</p>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-sm">
                            @if($skripsi->pembimbing1)
                            <p class="text-siakad-dark">{{ $skripsi->pembimbing1->user->name }}</p>
                            @if($skripsi->pembimbing2)
                            <p class="text-xs text-siakad-secondary">{{ $skripsi->pembimbing2->user->name }}</p>
                            @endif
                            @else
                            <span class="text-amber-600 text-xs">Belum ditentukan</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-center">
                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-{{ $skripsi->status_color }}-100 text-{{ $skripsi->status_color }}-700">{{ $skripsi->status_label }}</span>
                        </td>
                        <td class="py-4 px-5 text-right">
                            <a href="{{ route('admin.skripsi.show', $skripsi) }}" class="inline-flex items-center gap-1 text-sm text-siakad-primary hover:underline">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-siakad-secondary">Tidak ada data skripsi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($skripsiList->hasPages())
        <div class="px-5 py-4 border-t border-siakad-light">
            {{ $skripsiList->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
