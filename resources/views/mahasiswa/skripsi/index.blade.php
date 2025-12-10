<x-app-layout>
    <x-slot name="header">
        Skripsi / Tugas Akhir
    </x-slot>

    <div class="mb-6">
        <p class="text-sm text-siakad-secondary">Kelola skripsi dan tugas akhir Anda</p>
    </div>

    @if(!$skripsi)
    <!-- No Skripsi Yet -->
    <div class="card-saas p-12 text-center">
        <div class="w-20 h-20 rounded-full bg-siakad-light/50 flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-siakad-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-siakad-dark mb-2">Belum Ada Pengajuan Skripsi</h3>
        <p class="text-siakad-secondary mb-6">Ajukan judul skripsi Anda untuk memulai proses bimbingan</p>
        <a href="{{ route('mahasiswa.skripsi.create') }}" class="btn-primary-saas px-6 py-3 rounded-lg text-sm font-medium inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Ajukan Judul Skripsi
        </a>
    </div>
    @else
    <!-- Skripsi Dashboard -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Status Card -->
        <div class="lg:col-span-2 card-saas p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-{{ $skripsi->status_color }}-100 text-{{ $skripsi->status_color }}-700">
                        {{ $skripsi->status_label }}
                    </span>
                    <h2 class="text-lg font-bold text-siakad-dark mt-3">{{ $skripsi->judul }}</h2>
                    @if($skripsi->bidang_kajian)
                    <p class="text-sm text-siakad-secondary mt-1">Bidang: {{ $skripsi->bidang_kajian }}</p>
                    @endif
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mt-6">
                <div class="flex items-center justify-between text-sm mb-2">
                    <span class="text-siakad-secondary">Progress</span>
                    <span class="font-semibold text-siakad-primary">{{ $skripsi->progress_percent }}%</span>
                </div>
                <div class="h-3 bg-siakad-light rounded-full overflow-hidden">
                    <div class="h-full bg-siakad-primary rounded-full transition-all" style="width: {{ $skripsi->progress_percent }}%"></div>
                </div>
            </div>

            <!-- Pembimbing -->
            <div class="mt-6 grid grid-cols-2 gap-4">
                <div class="p-4 rounded-xl bg-siakad-light/30">
                    <p class="text-xs text-siakad-secondary mb-1">Pembimbing 1</p>
                    <p class="font-medium text-siakad-dark">{{ $skripsi->pembimbing1?->user->name ?? 'Belum ditentukan' }}</p>
                </div>
                <div class="p-4 rounded-xl bg-siakad-light/30">
                    <p class="text-xs text-siakad-secondary mb-1">Pembimbing 2</p>
                    <p class="font-medium text-siakad-dark">{{ $skripsi->pembimbing2?->user->name ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card-saas p-6">
            <h3 class="font-semibold text-siakad-dark mb-4">Timeline</h3>
            <div class="space-y-3 text-sm">
                @php
                    $milestones = [
                        ['date' => $skripsi->tanggal_pengajuan, 'label' => 'Pengajuan'],
                        ['date' => $skripsi->tanggal_acc_judul, 'label' => 'ACC Judul'],
                        ['date' => $skripsi->tanggal_seminar_proposal, 'label' => 'Sempro'],
                        ['date' => $skripsi->tanggal_seminar_hasil, 'label' => 'Semhas'],
                        ['date' => $skripsi->tanggal_sidang, 'label' => 'Sidang'],
                        ['date' => $skripsi->tanggal_selesai, 'label' => 'Selesai'],
                    ];
                @endphp
                @foreach($milestones as $m)
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full {{ $m['date'] ? 'bg-emerald-500' : 'bg-siakad-light' }}"></div>
                    <span class="{{ $m['date'] ? 'text-siakad-dark' : 'text-siakad-secondary' }}">{{ $m['label'] }}</span>
                    <span class="ml-auto text-siakad-secondary">{{ $m['date'] ? $m['date']->format('d/m/Y') : '-' }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Bimbingan Section -->
    @if(in_array($skripsi->status, ['bimbingan', 'penelitian', 'diterima']))
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card-saas overflow-hidden">
            <div class="px-6 py-4 border-b border-siakad-light flex items-center justify-between">
                <h3 class="font-semibold text-siakad-dark">Riwayat Bimbingan</h3>
                <span class="text-sm text-siakad-secondary">{{ $bimbinganList->count() }} catatan</span>
            </div>
            @forelse($bimbinganList as $bimbingan)
            <div class="p-5 border-b border-siakad-light/50">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-lg bg-siakad-primary/10 flex items-center justify-center text-siakad-primary font-bold text-sm">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-medium text-siakad-dark">{{ $bimbingan->tanggal_bimbingan->format('d M Y') }}</span>
                            <span class="px-2 py-0.5 text-xs rounded-full bg-{{ $bimbingan->status_color }}-100 text-{{ $bimbingan->status_color }}-700">{{ $bimbingan->status_label }}</span>
                        </div>
                        <p class="text-sm text-siakad-secondary mb-2">{{ $bimbingan->catatan_mahasiswa }}</p>
                        @if($bimbingan->catatan_dosen)
                        <div class="mt-3 p-3 rounded-lg bg-siakad-light/30">
                            <p class="text-xs text-siakad-secondary mb-1">Feedback {{ $bimbingan->dosen->user->name }}:</p>
                            <p class="text-sm text-siakad-dark">{{ $bimbingan->catatan_dosen }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-siakad-secondary">Belum ada catatan bimbingan</div>
            @endforelse
        </div>

        <!-- Add Bimbingan Form -->
        <div class="card-saas p-6">
            <h3 class="font-semibold text-siakad-dark mb-4">Tambah Catatan Bimbingan</h3>
            <form action="{{ route('mahasiswa.skripsi.bimbingan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-siakad-dark mb-2">Progress / Catatan *</label>
                        <textarea name="catatan_mahasiswa" rows="4" class="input-saas w-full text-sm" placeholder="Deskripsikan progress Anda..." required></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-siakad-dark mb-2">Upload Dokumen</label>
                        <input type="file" name="file_dokumen" accept=".pdf,.doc,.docx" class="input-saas w-full text-sm">
                        <p class="text-xs text-siakad-secondary mt-1">PDF, DOC, DOCX (max 10MB)</p>
                    </div>
                    <button type="submit" class="btn-primary-saas w-full py-2.5 rounded-lg text-sm font-medium">Kirim</button>
                </div>
            </form>
        </div>
    </div>
    @endif
    @endif
</x-app-layout>
