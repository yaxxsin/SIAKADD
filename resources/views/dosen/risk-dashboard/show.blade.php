<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <a href="{{ route('dosen.risk-dashboard.index') }}" class="text-siakad-secondary hover:text-siakad-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            Detail Risiko: {{ $mahasiswa->user->name ?? '-' }}
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Student Profile -->
        <x-ui.card padding="p-6">
            <div class="flex items-center gap-4 mb-6">
                <div
                    class="w-16 h-16 rounded-xl bg-siakad-primary flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($mahasiswa->user->name ?? 'X', 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-siakad-dark dark:text-white">{{ $mahasiswa->user->name ?? '-' }}
                    </h2>
                    <p class="text-siakad-secondary font-mono">{{ $mahasiswa->nim }}</p>
                    <p class="text-sm text-siakad-secondary">{{ $mahasiswa->prodi->nama ?? '-' }}</p>
                </div>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-siakad-secondary">Angkatan</span>
                    <span class="font-medium text-siakad-dark dark:text-white">{{ $mahasiswa->angkatan }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-siakad-secondary">Status</span>
                    <x-ui.badge :type="$mahasiswa->status === 'aktif' ? 'success' : 'warning'">
                        {{ ucfirst($mahasiswa->status) }}
                    </x-ui.badge>
                </div>
                <div class="flex justify-between">
                    <span class="text-siakad-secondary">Email</span>
                    <span
                        class="font-medium text-siakad-dark dark:text-white">{{ $mahasiswa->user->email ?? '-' }}</span>
                </div>
            </div>
        </x-ui.card>

        <!-- Risk Score -->
        <x-ui.card padding="p-6" class="lg:col-span-2">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-siakad-dark dark:text-white">Profil Risiko</h3>
                    <p class="text-sm text-siakad-secondary">Berdasarkan 5 faktor akademik</p>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-bold {{ $riskProfile->getColorClass() }} px-4 py-2 rounded-xl">
                        {{ $riskProfile->score }}
                    </div>
                    <p class="text-sm font-medium mt-1 {{ $riskProfile->getColorClass() }}">
                        {{ $riskProfile->getLevelLabel() }}
                    </p>
                </div>
            </div>

            <!-- Risk Factors -->
            <div class="space-y-4">
                @php
                    $factorLabels = [
                        'ips_trend' => ['label' => 'Tren IPS', 'weight' => '25%'],
                        'attendance' => ['label' => 'Kehadiran', 'weight' => '20%'],
                        'retakes' => ['label' => 'Mengulang MK', 'weight' => '15%'],
                        'graduation_progress' => ['label' => 'Progress Kelulusan', 'weight' => '25%'],
                        'workload' => ['label' => 'Beban SKS', 'weight' => '15%'],
                    ];
                @endphp

                @foreach($riskProfile->factors as $key => $value)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-siakad-secondary">
                                {{ $factorLabels[$key]['label'] ?? $key }}
                                <span class="text-xs">({{ $factorLabels[$key]['weight'] ?? '-' }})</span>
                            </span>
                            <span
                                class="font-medium {{ $value >= 0.7 ? 'text-red-600' : ($value >= 0.5 ? 'text-amber-600' : 'text-emerald-600') }}">
                                {{ round($value * 100) }}%
                            </span>
                        </div>
                        <x-ui.progress :value="$value * 100" :max="100" :showPercentage="false" size="sm" color="auto" />
                    </div>
                @endforeach
            </div>

            <!-- Risk Flags -->
            @if($riskProfile->flags)
                <div class="mt-6 pt-6 border-t border-siakad-light/50">
                    <h4 class="text-sm font-semibold text-siakad-dark dark:text-white mb-3">Peringatan Aktif</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($riskProfile->flags as $flag)
                            <x-ui.badge type="danger" size="md">{{ str_replace('_', ' ', $flag) }}</x-ui.badge>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recommendations -->
            @if($riskProfile->recommendations)
                <div class="mt-6 pt-6 border-t border-siakad-light/50">
                    <h4 class="text-sm font-semibold text-siakad-dark dark:text-white mb-3">Rekomendasi</h4>
                    <ul class="space-y-2">
                        @foreach($riskProfile->recommendations as $rec)
                            <li class="flex items-start gap-2 text-sm text-siakad-secondary">
                                <svg class="w-4 h-4 mt-0.5 text-siakad-primary flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $rec }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </x-ui.card>
    </div>

    <!-- KRS History -->
    @if($krsHistory->isNotEmpty())
        <x-ui.card class="mt-6" padding="p-6">
            <h3 class="text-lg font-semibold text-siakad-dark dark:text-white mb-4">Riwayat KRS Terbaru</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-siakad-light dark:border-gray-700">
                            <th class="text-left py-3 px-4 text-siakad-secondary font-medium">Periode</th>
                            <th class="text-center py-3 px-4 text-siakad-secondary font-medium">Jumlah MK</th>
                            <th class="text-center py-3 px-4 text-siakad-secondary font-medium">Total SKS</th>
                            <th class="text-center py-3 px-4 text-siakad-secondary font-medium">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($krsHistory as $krs)
                                    <tr class="border-b border-siakad-light/50 dark:border-gray-700/50">
                                        <td class="py-3 px-4 text-siakad-dark dark:text-white">
                                            {{ $krs->tahunAkademik->tahun ?? '-' }} {{ $krs->tahunAkademik->semester ?? '' }}
                                        </td>
                                        <td class="py-3 px-4 text-center text-siakad-dark dark:text-white">
                                            {{ $krs->krsDetail->count() }}
                                        </td>
                                        <td class="py-3 px-4 text-center text-siakad-dark dark:text-white">
                                            {{ $krs->krsDetail->sum(fn($d) => $d->kelas->mataKuliah->sks ?? 0) }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <x-ui.badge :type="match ($krs->status) {
                                'approved' => 'success',
                                'pending' => 'warning',
                                'rejected' => 'danger',
                                default => 'default'
                            }">
                                                {{ ucfirst($krs->status) }}
                                            </x-ui.badge>
                                        </td>
                                    </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-ui.card>
    @endif

    <!-- Actions -->
    <div class="mt-6 flex gap-3">
        <a href="{{ route('dosen.bimbingan.index') }}" class="btn-primary">
            Lihat Bimbingan PA
        </a>
        @if($krsHistory->where('status', 'pending')->isNotEmpty())
            <a href="{{ route('dosen.bimbingan.index') }}" class="btn-secondary">
                Review KRS Pending
            </a>
        @endif
    </div>
</x-app-layout>