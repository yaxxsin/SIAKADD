<x-app-layout>
    <x-slot name="header">Kehadiran Mengajar</x-slot>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        @foreach([['Hadir', $stats['hadir'] ?? 0, 'emerald'], ['Izin', $stats['izin'] ?? 0, 'blue'], ['Sakit', $stats['sakit'] ?? 0, 'amber'], ['Tugas', $stats['tugas'] ?? 0, 'purple'], ['Alpa', $stats['alpa'] ?? 0, 'red']] as $s)
        <div class="card-saas p-4 text-center">
            <p class="text-2xl font-bold text-{{ $s[2] }}-600">{{ $s[1] }}</p>
            <p class="text-xs text-siakad-secondary">{{ $s[0] }}</p>
        </div>
        @endforeach
    </div>

    <!-- Jadwal Hari Ini -->
    <div class="card-saas mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-siakad-light bg-gradient-to-r from-siakad-primary to-siakad-primary/80">
            <h3 class="font-semibold text-white">Jadwal Hari Ini - {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</h3>
        </div>
        @forelse($jadwalHariIni as $jadwal)
        <div class="p-4 border-b border-siakad-light/50 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="text-center px-3 py-2 rounded-lg bg-siakad-primary/10">
                    <p class="text-sm font-bold text-siakad-primary">{{ substr($jadwal->jam_mulai, 0, 5) }}</p>
                    <p class="text-xs text-siakad-secondary">{{ substr($jadwal->jam_selesai, 0, 5) }}</p>
                </div>
                <div>
                    <p class="font-medium text-siakad-dark">{{ $jadwal->kelas->mataKuliah->nama }}</p>
                    <p class="text-xs text-siakad-secondary">{{ $jadwal->kelas->nama_kelas }} • {{ $jadwal->ruangan ?? '-' }}</p>
                </div>
            </div>
            @if(in_array($jadwal->id, $kehadiranHariIni))
            <span class="px-3 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">✓ Sudah Absen</span>
            @else
            <button onclick="showModal({{ $jadwal->id }}, '{{ $jadwal->kelas->mataKuliah->nama }}')" class="btn-primary-saas px-4 py-2 rounded-lg text-sm font-medium">Absen Masuk</button>
            @endif
        </div>
        @empty
        <div class="p-8 text-center text-siakad-secondary">Tidak ada jadwal mengajar hari ini</div>
        @endforelse
    </div>

    <!-- Riwayat -->
    <div class="card-saas overflow-hidden">
        <div class="px-6 py-4 border-b border-siakad-light flex items-center justify-between">
            <h3 class="font-semibold text-siakad-dark">Riwayat Kehadiran</h3>
            <form method="GET" class="flex items-center gap-2">
                <select name="month" class="input-saas text-sm py-1.5">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->locale('id')->monthName }}</option>
                    @endfor
                </select>
                <select name="year" class="input-saas text-sm py-1.5">
                    @for($y = now()->year; $y >= now()->year - 2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn-primary-saas px-3 py-1.5 rounded-lg text-sm">Filter</button>
            </form>
        </div>
        <table class="w-full table-saas">
            <thead><tr class="bg-siakad-light/30"><th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Tanggal</th><th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Mata Kuliah</th><th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Jam</th><th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Status</th></tr></thead>
            <tbody>
            @forelse($riwayat as $r)
            <tr class="border-b border-siakad-light/50">
                <td class="py-3 px-5 text-sm text-siakad-dark">{{ $r->tanggal->format('d M Y') }}</td>
                <td class="py-3 px-5 text-sm text-siakad-dark">{{ $r->jadwalKuliah?->kelas?->mataKuliah?->nama ?? '-' }}</td>
                <td class="py-3 px-5 text-center text-sm text-siakad-secondary">{{ $r->jam_masuk ? substr($r->jam_masuk, 0, 5) : '-' }} - {{ $r->jam_keluar ? substr($r->jam_keluar, 0, 5) : '-' }}</td>
                <td class="py-3 px-5 text-center"><span class="px-2.5 py-1 text-xs font-medium rounded-full bg-{{ $r->status_color }}-100 text-{{ $r->status_color }}-700">{{ $r->status_label }}</span></td>
            </tr>
            @empty
            <tr><td colspan="4" class="py-8 text-center text-siakad-secondary">Belum ada data</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Absen -->
    <div id="absenModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6">
            <h3 class="text-lg font-bold text-siakad-dark mb-4">Absen Kehadiran</h3>
            <p id="modalMatkul" class="text-sm text-siakad-secondary mb-4"></p>
            <form action="{{ route('dosen.kehadiran.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="jadwal_kuliah_id" id="modalJadwalId">
                <div class="grid grid-cols-3 gap-4">
                    <div><label class="block text-xs font-medium text-siakad-dark mb-1">Jam Masuk</label><input type="time" name="jam_masuk" value="{{ now()->format('H:i') }}" class="input-saas w-full text-sm"></div>
                    <div><label class="block text-xs font-medium text-siakad-dark mb-1">Jam Keluar</label><input type="time" name="jam_keluar" class="input-saas w-full text-sm"></div>
                    <div><label class="block text-xs font-medium text-siakad-dark mb-1">Status</label><select name="status" class="input-saas w-full text-sm">@foreach(\App\Models\KehadiranDosen::getStatusList() as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select></div>
                </div>
                <div><label class="block text-xs font-medium text-siakad-dark mb-1">Keterangan</label><textarea name="keterangan" rows="2" class="input-saas w-full text-sm"></textarea></div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="hideModal()" class="btn-ghost-saas px-4 py-2 rounded-lg text-sm">Batal</button>
                    <button type="submit" class="btn-primary-saas px-4 py-2 rounded-lg text-sm font-medium">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showModal(id, matkul) {
            document.getElementById('modalJadwalId').value = id;
            document.getElementById('modalMatkul').textContent = matkul;
            document.getElementById('absenModal').classList.remove('hidden');
            document.getElementById('absenModal').classList.add('flex');
        }
        function hideModal() {
            document.getElementById('absenModal').classList.add('hidden');
            document.getElementById('absenModal').classList.remove('flex');
        }
    </script>
</x-app-layout>
