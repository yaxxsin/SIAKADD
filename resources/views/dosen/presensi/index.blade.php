<x-app-layout>
    <x-slot name="header">
        Manajemen Presensi
    </x-slot>

    @if($kelasList->isEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-12 text-center">
        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Belum Ada Kelas</h3>
        <p class="text-slate-500">Anda belum memiliki kelas yang diampu untuk semester ini.</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($kelasList as $kelas)
        <a href="{{ route('dosen.presensi.kelas', $kelas) }}" class="group bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:border-indigo-300 hover:shadow-md transition-all">
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                    {{ $kelas->nama_kelas }}
                </div>
                <span class="text-xs font-medium bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full">
                    {{ $kelas->mataKuliah->sks }} SKS
                </span>
            </div>
            
            <h3 class="font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition">{{ $kelas->mataKuliah->nama_mk }}</h3>
            <p class="text-sm text-slate-500 mb-4">{{ $kelas->mataKuliah->kode_mk }}</p>
            
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2 text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    <span>{{ $kelas->jumlah_mahasiswa ?? 0 }} Mahasiswa</span>
                </div>
                @if($kelas->jadwal->isNotEmpty())
                <span class="text-xs text-slate-400">
                    {{ $kelas->jadwal->first()->hari }}
                </span>
                @endif
            </div>
        </a>
        @endforeach
    </div>
    @endif
</x-app-layout>
