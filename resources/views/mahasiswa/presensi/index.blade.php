<x-app-layout>
    <x-slot name="header">
        Rekap Presensi
    </x-slot>

    <div class="mb-8">
        <h1 class="text-xl font-semibold text-slate-800">Rekap Kehadiran</h1>
        <p class="text-slate-500 mt-1">Ringkasan presensi untuk semua mata kuliah yang diambil</p>
    </div>

    @if($rekapList->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-12 text-center">
        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Belum Ada Data</h3>
        <p class="text-slate-500">Anda belum memiliki mata kuliah aktif atau presensi belum tercatat.</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($rekapList as $item)
        @php
            $kelas = $item['kelas'];
            $rekap = $item['rekap'];
            $persentaseColor = $rekap['persentase'] >= 75 ? 'emerald' : ($rekap['persentase'] >= 50 ? 'amber' : 'red');
        @endphp
        <a href="{{ route('mahasiswa.presensi.show', $kelas) }}" class="group bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:border-indigo-300 hover:shadow-md transition-all">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                        {{ $kelas->nama_kelas }}
                    </div>
                    <div class="text-right">
                        <span class="text-2xl font-bold text-{{ $persentaseColor }}-600">{{ $rekap['persentase'] }}%</span>
                        <p class="text-xs text-slate-500">Kehadiran</p>
                    </div>
                </div>
                
                <h3 class="font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition">{{ $kelas->mataKuliah->nama_mk }}</h3>
                <p class="text-sm text-slate-500 mb-4">{{ $kelas->mataKuliah->kode_mk }} • {{ $kelas->mataKuliah->sks }} SKS</p>
                
                <div class="flex gap-2 text-xs">
                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded-full">H: {{ $rekap['hadir'] }}</span>
                    <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded-full">S: {{ $rekap['sakit'] }}</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full">I: {{ $rekap['izin'] }}</span>
                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full">A: {{ $rekap['alpa'] }}</span>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-4 h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-{{ $persentaseColor }}-500 rounded-full transition-all" style="width: {{ $rekap['persentase'] }}%"></div>
                </div>
                <p class="text-xs text-slate-400 mt-2">{{ $rekap['hadir'] }} dari {{ $rekap['total_pertemuan'] }} pertemuan</p>
            </div>
            
            @if($rekap['persentase'] < 75 && $rekap['total_pertemuan'] > 0)
            <div class="px-6 py-3 bg-red-50 border-t border-red-100">
                <p class="text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Kehadiran di bawah 75%
                </p>
            </div>
            @endif
        </a>
        @endforeach
    </div>
    @endif
</x-app-layout>
