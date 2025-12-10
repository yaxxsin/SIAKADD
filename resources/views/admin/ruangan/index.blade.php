<x-app-layout>
    <x-slot name="header">
        Master Data Ruangan
    </x-slot>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <p class="text-sm text-siakad-secondary">Kelola data ruangan kelas dalam sistem</p>
        </div>
        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="btn-primary-saas px-4 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Ruangan
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $ruanganList->count() }}</p>
                    <p class="text-xs text-slate-500">Total Ruangan</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $ruanganList->where('is_active', true)->count() }}</p>
                    <p class="text-xs text-slate-500">Ruangan Aktif</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $ruanganList->sum('kapasitas') }}</p>
                    <p class="text-xs text-slate-500">Total Kapasitas</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ $perGedung->keys()->filter()->count() }}</p>
                    <p class="text-xs text-slate-500">Gedung</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card-saas overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-saas">
                <thead>
                    <tr class="bg-siakad-light/30">
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider w-16">#</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Kode</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Nama Ruangan</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Lokasi</th>
                        <th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Kapasitas</th>
                        <th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Status</th>
                        <th class="text-right py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ruanganList as $index => $ruangan)
                    <tr class="border-b border-siakad-light/50">
                        <td class="py-4 px-5 text-sm text-siakad-secondary">{{ $index + 1 }}</td>
                        <td class="py-4 px-5">
                            <span class="inline-flex px-3 py-1.5 text-sm font-semibold bg-siakad-primary text-white rounded-lg">{{ $ruangan->kode_ruangan }}</span>
                        </td>
                        <td class="py-4 px-5">
                            <p class="font-medium text-siakad-dark">{{ $ruangan->nama_ruangan }}</p>
                            @if($ruangan->fasilitas)
                            <p class="text-xs text-siakad-secondary mt-1">{{ Str::limit($ruangan->fasilitas, 50) }}</p>
                            @endif
                        </td>
                        <td class="py-4 px-5">
                            @if($ruangan->gedung)
                            <span class="text-sm text-siakad-dark">{{ $ruangan->gedung }}</span>
                            @if($ruangan->lantai)
                            <span class="text-xs text-siakad-secondary"> • Lt. {{ $ruangan->lantai }}</span>
                            @endif
                            @else
                            <span class="text-sm text-siakad-secondary">-</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-center">
                            <span class="inline-flex px-2.5 py-1 text-xs font-medium bg-siakad-secondary/10 text-siakad-secondary rounded-full">{{ $ruangan->kapasitas }} orang</span>
                        </td>
                        <td class="py-4 px-5 text-center">
                            @if($ruangan->is_active)
                            <span class="inline-flex px-2.5 py-1 text-xs font-medium bg-emerald-100 text-emerald-700 rounded-full">Aktif</span>
                            @else
                            <span class="inline-flex px-2.5 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="editRuangan({{ json_encode($ruangan) }})" class="p-2 text-siakad-secondary hover:text-siakad-primary hover:bg-siakad-primary/10 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('admin.ruangan.destroy', $ruangan) }}" method="POST" onsubmit="return confirm('Hapus ruangan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-siakad-secondary hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 bg-siakad-light/50 rounded-xl flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-siakad-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                </div>
                                <p class="text-siakad-secondary text-sm">Belum ada data ruangan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Modal -->
    <div id="createModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-lg animate-fade-in max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-siakad-light">
                <h3 class="text-lg font-semibold text-siakad-dark">Tambah Ruangan</h3>
            </div>
            <form action="{{ route('admin.ruangan.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-siakad-dark mb-2">Kode Ruangan *</label>
                            <input type="text" name="kode_ruangan" class="input-saas w-full px-4 py-2.5 text-sm" placeholder="LT-101" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-siakad-dark mb-2">Kapasitas *</label>
                            <input type="number" name="kapasitas" min="1" class="input-saas w-full px-4 py-2.5 text-sm" placeholder="40" value="40" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-siakad-dark mb-2">Nama Ruangan *</label>
                        <input type="text" name="nama_ruangan" class="input-saas w-full px-4 py-2.5 text-sm" placeholder="Lab Komputer 1" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-siakad-dark mb-2">Gedung</label>
                            <input type="text" name="gedung" class="input-saas w-full px-4 py-2.5 text-sm" placeholder="Gedung A">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-siakad-dark mb-2">Lantai</label>
                            <input type="number" name="lantai" min="1" class="input-saas w-full px-4 py-2.5 text-sm" placeholder="1">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-siakad-dark mb-2">Fasilitas</label>
                        <textarea name="fasilitas" rows="2" class="input-saas w-full px-4 py-2.5 text-sm" placeholder="AC, Proyektor, Whiteboard"></textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="createIsActive" checked class="rounded border-siakad-light text-siakad-primary focus:ring-siakad-primary">
                        <label for="createIsActive" class="text-sm text-siakad-dark">Ruangan aktif</label>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-siakad-light flex items-center justify-end gap-3">
                    <button type="button" onclick="document.getElementById('createModal').classList.add('hidden')" class="btn-ghost-saas px-4 py-2 rounded-lg text-sm font-medium">Batal</button>
                    <button type="submit" class="btn-primary-saas px-4 py-2 rounded-lg text-sm font-medium">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl w-full max-w-lg animate-fade-in max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-siakad-light">
                <h3 class="text-lg font-semibold text-siakad-dark">Edit Ruangan</h3>
            </div>
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-siakad-dark mb-2">Kode Ruangan *</label>
                            <input type="text" name="kode_ruangan" id="editKode" class="input-saas w-full px-4 py-2.5 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-siakad-dark mb-2">Kapasitas *</label>
                            <input type="number" name="kapasitas" id="editKapasitas" min="1" class="input-saas w-full px-4 py-2.5 text-sm" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-siakad-dark mb-2">Nama Ruangan *</label>
                        <input type="text" name="nama_ruangan" id="editNama" class="input-saas w-full px-4 py-2.5 text-sm" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-siakad-dark mb-2">Gedung</label>
                            <input type="text" name="gedung" id="editGedung" class="input-saas w-full px-4 py-2.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-siakad-dark mb-2">Lantai</label>
                            <input type="number" name="lantai" id="editLantai" min="1" class="input-saas w-full px-4 py-2.5 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-siakad-dark mb-2">Fasilitas</label>
                        <textarea name="fasilitas" id="editFasilitas" rows="2" class="input-saas w-full px-4 py-2.5 text-sm"></textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="editIsActive" class="rounded border-siakad-light text-siakad-primary focus:ring-siakad-primary">
                        <label for="editIsActive" class="text-sm text-siakad-dark">Ruangan aktif</label>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-siakad-light flex items-center justify-end gap-3">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="btn-ghost-saas px-4 py-2 rounded-lg text-sm font-medium">Batal</button>
                    <button type="submit" class="btn-primary-saas px-4 py-2 rounded-lg text-sm font-medium">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editRuangan(data) {
            document.getElementById('editForm').action = `/admin/ruangan/${data.id}`;
            document.getElementById('editKode').value = data.kode_ruangan;
            document.getElementById('editNama').value = data.nama_ruangan;
            document.getElementById('editKapasitas').value = data.kapasitas;
            document.getElementById('editGedung').value = data.gedung || '';
            document.getElementById('editLantai').value = data.lantai || '';
            document.getElementById('editFasilitas').value = data.fasilitas || '';
            document.getElementById('editIsActive').checked = data.is_active;
            document.getElementById('editModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
