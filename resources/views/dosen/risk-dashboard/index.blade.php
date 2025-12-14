<x-app-layout>
    <x-slot name="header">
        Dashboard Risiko Mahasiswa Bimbingan
    </x-slot>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <x-ui.card class="text-center">
            <p class="text-3xl font-bold text-siakad-dark dark:text-white">{{ $stats['total_advisees'] }}</p>
            <p class="text-sm text-siakad-secondary mt-1">Total Mahasiswa</p>
        </x-ui.card>

        <x-ui.card class="text-center" type="warning">
            <p class="text-3xl font-bold text-amber-600">{{ $stats['pending_krs'] }}</p>
            <p class="text-sm text-siakad-secondary mt-1">KRS Pending</p>
        </x-ui.card>

        <x-ui.card class="text-center" type="danger">
            <p class="text-3xl font-bold text-red-600">{{ $stats['high_risk'] }}</p>
            <p class="text-sm text-siakad-secondary mt-1">Risiko Tinggi</p>
        </x-ui.card>

        <x-ui.card class="text-center" type="info">
            <p class="text-3xl font-bold text-blue-600">{{ $stats['needs_attention'] }}</p>
            <p class="text-sm text-siakad-secondary mt-1">Perlu Perhatian</p>
        </x-ui.card>
    </div>

    <!-- Risk Groups -->
    @foreach(['critical' => 'Kritis', 'high' => 'Risiko Tinggi', 'medium' => 'Perlu Perhatian', 'low' => 'Risiko Rendah'] as $level => $label)
        @if($riskGroups[$level]->isNotEmpty())
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-siakad-dark dark:text-white mb-3 flex items-center gap-2">
                    @if($level === 'critical')
                        <span class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
                    @elseif($level === 'high')
                        <span class="w-3 h-3 bg-orange-500 rounded-full"></span>
                    @elseif($level === 'medium')
                        <span class="w-3 h-3 bg-amber-500 rounded-full"></span>
                    @else
                        <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                    @endif
                    {{ $label }} ({{ $riskGroups[$level]->count() }})
                </h2>

                <div class="grid gap-3">
                    @foreach($riskGroups[$level] as $student)
                        <x-ui.card hover :href="route('dosen.risk-dashboard.show', $student['mahasiswa']->id)"
                            class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-full bg-siakad-primary/10 flex items-center justify-center text-siakad-primary font-semibold">
                                    {{ strtoupper(substr($student['mahasiswa']->user->name ?? 'X', 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="font-semibold text-siakad-dark dark:text-white">
                                        {{ $student['mahasiswa']->user->name ?? '-' }}
                                    </h3>
                                    <p class="text-sm text-siakad-secondary">
                                        {{ $student['mahasiswa']->nim }} · {{ $student['mahasiswa']->prodi->nama ?? '-' }}
                                    </p>
                                    @if($student['risk']->flags)
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach(array_slice($student['risk']->flags, 0, 2) as $flag)
                                                <x-ui.badge type="warning" size="xs">{{ str_replace('_', ' ', $flag) }}</x-ui.badge>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                @if($student['pending_krs'])
                                    <x-ui.badge type="warning" size="sm">KRS Pending</x-ui.badge>
                                @endif

                                <div class="text-right">
                                    <div
                                        class="text-2xl font-bold {{ $student['risk']->level === 'CRITICAL' ? 'text-red-600' : ($student['risk']->level === 'HIGH' ? 'text-orange-600' : ($student['risk']->level === 'MEDIUM' ? 'text-amber-600' : 'text-emerald-600')) }}">
                                        {{ $student['risk']->score }}
                                    </div>
                                    <p class="text-[10px] text-siakad-secondary uppercase">Risk Score</p>
                                </div>

                                <svg class="w-5 h-5 text-siakad-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </x-ui.card>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    @if($studentsWithRisk->isEmpty())
        <x-ui.card class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-siakad-secondary/50 mb-4" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <p class="text-siakad-secondary">Belum ada mahasiswa bimbingan terdaftar</p>
        </x-ui.card>
    @endif
</x-app-layout>