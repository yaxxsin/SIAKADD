<x-app-layout>
    <x-slot name="header">
        Input Nilai Mahasiswa
    </x-slot>

    @if($kelasAjar->isEmpty())
    <div class="card-saas p-12 text-center">
        <div class="w-20 h-20 rounded-full bg-siakad-light/50 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-siakad-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-siakad-dark mb-2">Belum Ada Kelas</h3>
        <p class="text-siakad-secondary">Anda belum memiliki kelas yang diampu untuk semester ini.</p>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($kelasAjar as $kelas)
        <a href="{{ route('dosen.penilaian.show', $kelas->id) }}" class="card-saas group hover:ring-2 hover:ring-siakad-primary transition duration-300">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-siakad-primary/10 flex items-center justify-center group-hover:bg-siakad-primary transition duration-300">
                         <span class="font-bold text-lg text-siakad-primary group-hover:text-white transition duration-300">{{ $kelas->nama_kelas }}</span>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-siakad-light text-siakad-secondary group-hover:bg-siakad-primary/10 group-hover:text-siakad-primary transition">
                        {{ $kelas->krsDetail->count() }} Mahasiswa
                    </span>
                </div>
                
                <h3 class="text-lg font-bold text-siakad-dark mb-1 group-hover:text-siakad-primary transition">{{ $kelas->mataKuliah->nama_mk }}</h3>
                <p class="text-sm text-siakad-secondary mb-4">{{ $kelas->mataKuliah->kode_mk }} • {{ $kelas->mataKuliah->sks }} SKS</p>
                
                <div class="flex items-center gap-2 text-sm text-siakad-secondary group-hover:text-siakad-primary/70 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <span>Input Nilai</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</x-app-layout>
