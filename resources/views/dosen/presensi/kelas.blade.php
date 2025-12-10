<x-app-layout>
    <x-slot name="header">
        Presensi: {{ $kelas->mataKuliah->nama_mk }} ({{ $kelas->nama_kelas }})
    </x-slot>

    <div class="mb-6">
        <a href="{{ route('dosen.presensi.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-indigo-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar Kelas
        </a>
    </div>

    <!-- Kelas Info Card -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 text-white mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">{{ $kelas->mataKuliah->nama_mk }}</h2>
                <p class="opacity-80 mt-1">{{ $kelas->mataKuliah->kode_mk }} • {{ $kelas->mataKuliah->sks }} SKS • Kelas {{ $kelas->nama_kelas }}</p>
            </div>
            <a href="{{ route('dosen.presensi.pertemuan.create', $kelas) }}" class="px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg font-medium transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Buat Pertemuan
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Pertemuan List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800">Daftar Pertemuan</h3>
                </div>
                
                @if($pertemuanList->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <p class="text-slate-500 mb-4">Belum ada pertemuan</p>
                    <a href="{{ route('dosen.presensi.pertemuan.create', $kelas) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Buat Pertemuan Pertama
                    </a>
                </div>
                @else
                <div class="divide-y divide-slate-100">
                    @foreach($pertemuanList as $pertemuan)
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                                {{ $pertemuan->pertemuan_ke }}
                            </div>
                            <div>
                                <p class="font-medium text-slate-800">Pertemuan {{ $pertemuan->pertemuan_ke }}</p>
                                <p class="text-sm text-slate-500">{{ $pertemuan->tanggal->format('d M Y') }} • {{ $pertemuan->materi ?? 'Belum ada materi' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs px-2 py-1 rounded-full {{ $pertemuan->presensi->count() > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $pertemuan->presensi->count() }} tercatat
                            </span>
                            <a href="{{ route('dosen.presensi.input', $pertemuan) }}" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <!-- Rekap Mahasiswa -->
        <div>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800">Rekap Kehadiran</h3>
                </div>
                
                @if($rekapMahasiswa->isEmpty())
                <div class="p-8 text-center text-slate-500">
                    Belum ada mahasiswa
                </div>
                @else
                <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    @foreach($rekapMahasiswa as $item)
                    <div class="p-4">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                {{ strtoupper(substr($item['mahasiswa']->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $item['mahasiswa']->user->name }}</p>
                                <p class="text-xs text-slate-500">{{ $item['mahasiswa']->nim }}</p>
                            </div>
                            <span class="text-sm font-bold {{ $item['rekap']['persentase'] >= 75 ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $item['rekap']['persentase'] }}%
                            </span>
                        </div>
                        <div class="flex gap-1 text-xs">
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded">H:{{ $item['rekap']['hadir'] }}</span>
                            <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded">S:{{ $item['rekap']['sakit'] }}</span>
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded">I:{{ $item['rekap']['izin'] }}</span>
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 rounded">A:{{ $item['rekap']['alpa'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
