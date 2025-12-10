<x-app-layout>
    <x-slot name="header">
        Input Presensi - Pertemuan {{ $pertemuan->pertemuan_ke }}
    </x-slot>

    <div class="mb-6">
        <a href="{{ route('dosen.presensi.kelas', $kelas) }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke {{ $kelas->mataKuliah->nama_mk }}
        </a>
    </div>

    <!-- Pertemuan Info -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 text-white mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Pertemuan {{ $pertemuan->pertemuan_ke }}</h2>
                <p class="opacity-80 mt-1">{{ $kelas->mataKuliah->nama_mk }} • Kelas {{ $kelas->nama_kelas }}</p>
                <p class="opacity-60 text-sm mt-2">{{ $pertemuan->tanggal->format('l, d F Y') }} • {{ $pertemuan->materi ?? 'Belum ada materi' }}</p>
            </div>
            <div class="text-right">
                <p class="text-3xl font-bold">{{ $mahasiswaList->count() }}</p>
                <p class="opacity-80 text-sm">Mahasiswa</p>
            </div>
        </div>
    </div>

    <form action="{{ route('dosen.presensi.store', $pertemuan) }}" method="POST">
        @csrf
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-6">
            <!-- Quick Actions -->
            <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                <p class="text-sm text-slate-600">Pilih status kehadiran untuk setiap mahasiswa</p>
                <div class="flex gap-2">
                    <button type="button" onclick="setAllStatus('hadir')" class="px-3 py-1.5 text-xs font-medium bg-emerald-100 text-emerald-700 rounded-lg hover:bg-emerald-200 transition">
                        Semua Hadir
                    </button>
                    <button type="button" onclick="setAllStatus('alpa')" class="px-3 py-1.5 text-xs font-medium bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                        Semua Alpa
                    </button>
                </div>
            </div>

            @if($mahasiswaList->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <p class="text-slate-500">Belum ada mahasiswa yang terdaftar di kelas ini</p>
            </div>
            @else
            <div class="divide-y divide-slate-100">
                @foreach($mahasiswaList as $mahasiswa)
                @php
                    $existing = $existingPresensi[$mahasiswa->id] ?? null;
                    $currentStatus = old("presensi.{$mahasiswa->id}", $existing?->status ?? 'hadir');
                @endphp
                <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($mahasiswa->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-slate-800">{{ $mahasiswa->user->name }}</p>
                            <p class="text-sm text-slate-500">{{ $mahasiswa->nim }}</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="presensi[{{ $mahasiswa->id }}]" value="hadir" class="peer hidden" {{ $currentStatus === 'hadir' ? 'checked' : '' }}>
                            <span class="px-3 py-2 rounded-lg text-sm font-medium border transition peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 bg-white text-slate-600 border-slate-200 hover:border-emerald-400">
                                Hadir
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="presensi[{{ $mahasiswa->id }}]" value="sakit" class="peer hidden" {{ $currentStatus === 'sakit' ? 'checked' : '' }}>
                            <span class="px-3 py-2 rounded-lg text-sm font-medium border transition peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500 bg-white text-slate-600 border-slate-200 hover:border-amber-400">
                                Sakit
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="presensi[{{ $mahasiswa->id }}]" value="izin" class="peer hidden" {{ $currentStatus === 'izin' ? 'checked' : '' }}>
                            <span class="px-3 py-2 rounded-lg text-sm font-medium border transition peer-checked:bg-blue-500 peer-checked:text-white peer-checked:border-blue-500 bg-white text-slate-600 border-slate-200 hover:border-blue-400">
                                Izin
                            </span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="presensi[{{ $mahasiswa->id }}]" value="alpa" class="peer hidden" {{ $currentStatus === 'alpa' ? 'checked' : '' }}>
                            <span class="px-3 py-2 rounded-lg text-sm font-medium border transition peer-checked:bg-red-500 peer-checked:text-white peer-checked:border-red-500 bg-white text-slate-600 border-slate-200 hover:border-red-400">
                                Alpa
                            </span>
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        @if($mahasiswaList->isNotEmpty())
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Simpan Presensi
            </button>
            <a href="{{ route('dosen.presensi.kelas', $kelas) }}" class="px-6 py-3 border border-slate-200 text-slate-600 rounded-lg font-medium hover:bg-slate-50 transition">
                Batal
            </a>
        </div>
        @endif
    </form>

    <script>
        function setAllStatus(status) {
            document.querySelectorAll(`input[value="${status}"]`).forEach(input => {
                input.checked = true;
            });
        }
    </script>
</x-app-layout>
