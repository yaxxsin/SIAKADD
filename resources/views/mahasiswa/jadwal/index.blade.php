<x-app-layout>
    <x-slot name="header">
        Jadwal Kuliah
    </x-slot>

    <div class="mb-8">
        <h1 class="text-xl font-semibold text-siakad-dark">Jadwal Kuliah Semester Ini</h1>
        @if($activeTA)
        <p class="text-siakad-secondary mt-1">{{ $activeTA->tahun }} - Semester {{ $activeTA->semester }}</p>
        @endif
    </div>

    @if(!$activeTA)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 text-center">
        <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <h3 class="text-lg font-semibold text-amber-800 mb-2">Tidak Ada Tahun Akademik Aktif</h3>
        <p class="text-amber-600">Hubungi admin untuk mengaktifkan tahun akademik.</p>
    </div>
    @elseif($jadwalPerHari->isEmpty())
    <div class="card-saas p-12 text-center">
        <div class="w-20 h-20 rounded-full bg-siakad-light/50 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-siakad-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-siakad-dark mb-2">Belum Ada Jadwal</h3>
        <p class="text-siakad-secondary mb-4">KRS Anda belum diapprove atau belum ada jadwal kuliah yang diatur.</p>
        <a href="{{ route('mahasiswa.krs.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-siakad-primary text-white rounded-lg hover:bg-siakad-primary/90 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Lihat KRS
        </a>
    </div>
    @else
    <!-- Schedule Grid -->
    <div class="space-y-6">
        @foreach($jadwalPerHari as $hari => $jadwalList)
        <div class="card-saas overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-siakad-primary to-emerald-600 text-white">
                <h3 class="font-bold text-lg">{{ $hari }}</h3>
                <p class="text-sm opacity-80">{{ $jadwalList->count() }} mata kuliah</p>
            </div>
            
            <div class="divide-y divide-siakad-light">
                @foreach($jadwalList as $item)
                @php
                    $kelas = $item['kelas'];
                    $jadwal = $item['jadwal'];
                @endphp
                <div class="p-5 flex items-center gap-5 hover:bg-siakad-light/20 transition">
                    <!-- Time -->
                    <div class="text-center w-24 flex-shrink-0">
                        <p class="text-lg font-bold text-siakad-primary">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</p>
                        <p class="text-xs text-siakad-secondary">sampai</p>
                        <p class="text-sm font-medium text-siakad-dark">{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</p>
                    </div>
                    
                    <!-- Divider -->
                    <div class="w-1 h-16 bg-siakad-primary/20 rounded-full"></div>
                    
                    <!-- Course Info -->
                    <div class="flex-1">
                        <h4 class="font-semibold text-siakad-dark">{{ $kelas->mataKuliah->nama_mk }}</h4>
                        <p class="text-sm text-siakad-secondary mt-1">{{ $kelas->mataKuliah->kode_mk }} • {{ $kelas->mataKuliah->sks }} SKS • Kelas {{ $kelas->nama_kelas }}</p>
                        <div class="flex items-center gap-4 mt-2">
                            <span class="inline-flex items-center gap-1 text-xs text-siakad-secondary">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ $kelas->dosen->user->name ?? 'TBA' }}
                            </span>
                            @if($jadwal->ruangan)
                            <span class="inline-flex items-center gap-1 text-xs text-siakad-primary bg-siakad-primary/10 px-2 py-0.5 rounded">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                {{ $jadwal->ruangan }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <!-- Summary Card -->
    <div class="mt-8 bg-siakad-dark rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold">Ringkasan Jadwal</h3>
                <p class="text-sm opacity-80 mt-1">Total mata kuliah yang diambil semester ini</p>
            </div>
            <div class="flex gap-6">
                <div class="text-center">
                    <p class="text-3xl font-bold">{{ $jadwalPerHari->flatten(1)->count() }}</p>
                    <p class="text-xs opacity-60">Sesi Kuliah</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold">{{ $jadwalPerHari->keys()->count() }}</p>
                    <p class="text-xs opacity-60">Hari Aktif</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
