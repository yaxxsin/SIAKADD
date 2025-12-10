<x-app-layout>
    <x-slot name="header">
        Data Kelas
    </x-slot>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <p class="text-sm text-siakad-secondary">Kelola data kelas dan jadwal kuliah dalam sistem</p>
        </div>
        <button onclick="document.getElementById('createModal').classList.remove('hidden')" class="btn-primary-saas px-4 py-2.5 rounded-lg text-sm font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Tambah Kelas
        </button>
    </div>

    <!-- Table Card -->
    <div class="card-saas overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-saas">
                <thead>
                    <tr class="bg-siakad-light/30">
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider w-16">#</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Kelas</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Mata Kuliah</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Dosen</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Jadwal</th>
                        <th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider">Kapasitas</th>
                        <th class="text-right py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kelas as $index => $k)
                    @php
                        $jadwal = $k->jadwal->first();
                    @endphp
                    <tr class="border-b border-siakad-light/50">
                        <td class="py-4 px-5 text-sm text-siakad-secondary">{{ $index + 1 }}</td>
                        <td class="py-4 px-5">
                            <span class="inline-flex px-3 py-1.5 text-sm font-semibold bg-siakad-primary text-white rounded-lg">{{ $k->nama_kelas }}</span>
                        </td>
                        <td class="py-4 px-5">
                            <div>
                                <span class="text-sm font-medium text-siakad-dark">{{ $k->mataKuliah->nama_mk ?? '-' }}</span>
                                <span class="block text-xs text-siakad-secondary font-mono">{{ $k->mataKuliah->kode_mk ?? '' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-5">
                            <span class="text-sm text-siakad-secondary">{{ $k->dosen->user->name ?? '-' }}</span>
                        </td>
                        <td class="py-4 px-5">
                            @if($jadwal)
                            <div class="text-sm">
                                <span class="font-medium text-siakad-dark">{{ $jadwal->hari }}</span>
                                <span class="block text-xs text-siakad-secondary">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span>
                                @if($jadwal->ruangan)
                                <span class="block text-xs text-siakad-primary">{{ $jadwal->ruangan }}</span>
                                @endif
                            </div>
                            @else
                            <span class="text-xs text-amber-600 bg-amber-50 px-2 py-1 rounded">Belum diatur</span>
                            @endif
                        </td>
                        <td class="py-4 px-5">
                            <span class="inline-flex px-2.5 py-1 text-xs font-medium bg-siakad-secondary/10 text-siakad-secondary rounded-full">{{ $k->kapasitas }} mhs</span>
                        </td>
                        <td class="py-4 px-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="editKelas({{ json_encode([
                                    'id' => $k->id,
                                    'nama_kelas' => $k->nama_kelas,
                                    'mata_kuliah_id' => $k->mata_kuliah_id,
                                    'dosen_id' => $k->dosen_id,
                                    'kapasitas' => $k->kapasitas,
                                    'hari' => $jadwal?->hari,
                                    'jam_mulai' => $jadwal ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : null,
                                    'jam_selesai' => $jadwal ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : null,
                                    'ruangan' => $jadwal?->ruangan,
                                ]) }})" class="p-2 text-siakad-secondary hover:text-siakad-primary hover:bg-siakad-primary/10 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                <form action="{{ route('admin.kelas.destroy', $k) }}" method="POST" onsubmit="return confirm('Hapus kelas ini?')">
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
                                    <svg class="w-6 h-6 text-siakad-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                                </div>
                                <p class="text-siakad-secondary text-sm">Belum ada data kelas</p>
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
                <h3 class="text-lg font-semibold text-siakad-dark">Tambah Kelas</h3>
            </div>
            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-siakad-dark mb-2">Nama Kelas</label>
                            <input type="text" name="nama_kelas" class="input-saas w-full px-4 py-2.5 text-sm" placeholder="Contoh: A" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-siakad-dark mb-2">Kapasitas</label>
                            <input type="number" name="kapasitas" min="1" class="input-saas w-full px-4 py-2.5 text-sm" placeholder="40" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-siakad-dark mb-2">Mata Kuliah</label>
                        <select name="mata_kuliah_id" class="input-saas w-full px-4 py-2.5 text-sm" required>
                            <option value="">Pilih Mata Kuliah</option>
                            @foreach($mataKuliah as $mk)
                            <option value="{{ $mk->id }}">{{ $mk->kode_mk }} - {{ $mk->nama_mk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-siakad-dark mb-2">Dosen Pengampu</label>
                        <select name="dosen_id" class="input-saas w-full px-4 py-2.5 text-sm" required>
                            <option value="">Pilih Dosen</option>
                            @foreach($dosen as $d)
                            <option value="{{ $d->id }}">{{ $d->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Jadwal Section -->
                    <div class="pt-4 border-t border-siakad-light">
                        <h4 class="text-sm font-semibold text-siakad-dark mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Jadwal Kuliah (Opsional)
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-siakad-dark mb-2">Hari</label>
                                <select name="hari" class="input-saas w-full px-4 py-2.5 text-sm">
                                    <option value="">Pilih Hari</option>
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-siakad-dark mb-2">Ruangan</label>
                                <input type="text" name="ruangan" class="input-saas w-full px-4 py-2.5 text-sm" placeholder="Contoh: LT-101">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-siakad-dark mb-2">Jam Mulai</label>
                                <input type="time" name="jam_mulai" class="input-saas w-full px-4 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-siakad-dark mb-2">Jam Selesai</label>
                                <input type="time" name="jam_selesai" class="input-saas w-full px-4 py-2.5 text-sm">
                            </div>
                        </div>
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
                <h3 class="text-lg font-semibold text-siakad-dark">Edit Kelas</h3>
            </div>
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-siakad-dark mb-2">Nama Kelas</label>
                            <input type="text" name="nama_kelas" id="editNama" class="input-saas w-full px-4 py-2.5 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-siakad-dark mb-2">Kapasitas</label>
                            <input type="number" name="kapasitas" id="editKapasitas" min="1" class="input-saas w-full px-4 py-2.5 text-sm" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-siakad-dark mb-2">Mata Kuliah</label>
                        <select name="mata_kuliah_id" id="editMK" class="input-saas w-full px-4 py-2.5 text-sm" required>
                            @foreach($mataKuliah as $mk)
                            <option value="{{ $mk->id }}">{{ $mk->kode_mk }} - {{ $mk->nama_mk }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-siakad-dark mb-2">Dosen Pengampu</label>
                        <select name="dosen_id" id="editDosen" class="input-saas w-full px-4 py-2.5 text-sm" required>
                            @foreach($dosen as $d)
                            <option value="{{ $d->id }}">{{ $d->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Jadwal Section -->
                    <div class="pt-4 border-t border-siakad-light">
                        <h4 class="text-sm font-semibold text-siakad-dark mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Jadwal Kuliah
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-siakad-dark mb-2">Hari</label>
                                <select name="hari" id="editHari" class="input-saas w-full px-4 py-2.5 text-sm">
                                    <option value="">Pilih Hari</option>
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-siakad-dark mb-2">Ruangan</label>
                                <input type="text" name="ruangan" id="editRuangan" class="input-saas w-full px-4 py-2.5 text-sm" placeholder="Contoh: LT-101">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div>
                                <label class="block text-sm font-medium text-siakad-dark mb-2">Jam Mulai</label>
                                <input type="time" name="jam_mulai" id="editJamMulai" class="input-saas w-full px-4 py-2.5 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-siakad-dark mb-2">Jam Selesai</label>
                                <input type="time" name="jam_selesai" id="editJamSelesai" class="input-saas w-full px-4 py-2.5 text-sm">
                            </div>
                        </div>
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
        function editKelas(data) {
            document.getElementById('editForm').action = `/admin/kelas/${data.id}`;
            document.getElementById('editNama').value = data.nama_kelas;
            document.getElementById('editMK').value = data.mata_kuliah_id;
            document.getElementById('editDosen').value = data.dosen_id;
            document.getElementById('editKapasitas').value = data.kapasitas;
            document.getElementById('editHari').value = data.hari || '';
            document.getElementById('editJamMulai').value = data.jam_mulai || '';
            document.getElementById('editJamSelesai').value = data.jam_selesai || '';
            document.getElementById('editRuangan').value = data.ruangan || '';
            document.getElementById('editModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
