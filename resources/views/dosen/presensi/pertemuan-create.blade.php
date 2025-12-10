<x-app-layout>
    <x-slot name="header">
        Buat Pertemuan Baru
    </x-slot>

    <div class="mb-6">
        <a href="{{ route('dosen.presensi.kelas', $kelas) }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke {{ $kelas->mataKuliah->nama_mk }}
        </a>
    </div>

    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
                <h2 class="text-xl font-bold">{{ $kelas->mataKuliah->nama_mk }}</h2>
                <p class="opacity-80 mt-1">{{ $kelas->mataKuliah->kode_mk }} • Kelas {{ $kelas->nama_kelas }}</p>
            </div>

            <form action="{{ route('dosen.presensi.pertemuan.store', $kelas) }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Jadwal Selection -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Jadwal Kuliah</label>
                    @if($jadwalList->isEmpty())
                    <div class="p-4 bg-amber-50 text-amber-700 rounded-lg">
                        Tidak ada jadwal untuk kelas ini. Hubungi admin.
                    </div>
                    @else
                    <div class="space-y-2">
                        @foreach($jadwalList as $jadwal)
                        <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-lg cursor-pointer hover:border-indigo-400 transition has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50">
                            <input type="radio" name="jadwal_kuliah_id" value="{{ $jadwal->id }}" class="text-indigo-600" {{ old('jadwal_kuliah_id') == $jadwal->id ? 'checked' : ($loop->first && !old('jadwal_kuliah_id') ? 'checked' : '') }} required>
                            <div>
                                <p class="font-medium text-slate-800">{{ $jadwal->hari }}</p>
                                <p class="text-sm text-slate-500">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }} • {{ $jadwal->ruangan ?? 'Ruangan TBA' }}</p>
                                <p class="text-xs text-indigo-600 mt-1">Pertemuan selanjutnya: ke-{{ $nextPertemuanKe[$jadwal->id] ?? 1 }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @endif
                    @error('jadwal_kuliah_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pertemuan Ke -->
                <div>
                    <label for="pertemuan_ke" class="block text-sm font-medium text-slate-700 mb-2">Pertemuan Ke</label>
                    <input type="number" name="pertemuan_ke" id="pertemuan_ke" min="1" max="16" 
                        value="{{ old('pertemuan_ke', $nextPertemuanKe[$jadwalList->first()?->id] ?? 1) }}"
                        class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required>
                    @error('pertemuan_ke')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal -->
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-slate-700 mb-2">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" 
                        value="{{ old('tanggal', date('Y-m-d')) }}"
                        class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition" required>
                    @error('tanggal')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Materi -->
                <div>
                    <label for="materi" class="block text-sm font-medium text-slate-700 mb-2">Materi (Opsional)</label>
                    <input type="text" name="materi" id="materi" 
                        value="{{ old('materi') }}"
                        placeholder="Topik atau materi yang dibahas"
                        class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition">
                    @error('materi')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                        Buat & Input Presensi
                    </button>
                    <a href="{{ route('dosen.presensi.kelas', $kelas) }}" class="px-6 py-3 border border-slate-200 text-slate-600 rounded-lg font-medium hover:bg-slate-50 transition">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
