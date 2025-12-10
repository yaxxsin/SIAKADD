<x-app-layout>
    <x-slot name="header">Monitoring Kehadiran Dosen</x-slot>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        @foreach([['Hadir', $stats['hadir'] ?? 0, 'emerald'], ['Izin', $stats['izin'] ?? 0, 'blue'], ['Sakit', $stats['sakit'] ?? 0, 'amber'], ['Tugas', $stats['tugas'] ?? 0, 'purple'], ['Alpa', $stats['alpa'] ?? 0, 'red']] as $s)
        <div class="card-saas p-4 text-center">
            <p class="text-2xl font-bold text-{{ $s[2] }}-600">{{ $s[1] }}</p>
            <p class="text-xs text-siakad-secondary">{{ $s[0] }}</p>
        </div>
        @endforeach
    </div>

    <!-- Filter -->
    <div class="card-saas p-4 mb-6">
        <form method="GET" class="flex flex-wrap items-end gap-4">
            <div class="w-48"><label class="block text-xs font-medium text-siakad-dark mb-1">Dosen</label><select name="dosen_id" class="input-saas w-full text-sm"><option value="">Semua Dosen</option>@foreach($dosenList as $d)<option value="{{ $d->id }}" {{ $dosenId == $d->id ? 'selected' : '' }}>{{ $d->user->name }}</option>@endforeach</select></div>
            <div><label class="block text-xs font-medium text-siakad-dark mb-1">Bulan</label><select name="month" class="input-saas text-sm">@for($m = 1; $m <= 12; $m++)<option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->locale('id')->monthName }}</option>@endfor</select></div>
            <div><label class="block text-xs font-medium text-siakad-dark mb-1">Tahun</label><select name="year" class="input-saas text-sm">@for($y = now()->year; $y >= now()->year - 2; $y--)<option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>@endfor</select></div>
            <button type="submit" class="btn-primary-saas px-4 py-2.5 rounded-lg text-sm font-medium">Filter</button>
        </form>
    </div>

    <!-- Rekap Per Dosen -->
    <div class="card-saas mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-siakad-light"><h3 class="font-semibold text-siakad-dark">Rekap Per Dosen</h3></div>
        <table class="w-full table-saas">
            <thead><tr class="bg-siakad-light/30"><th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Dosen</th><th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Hadir</th><th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Izin</th><th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Sakit</th><th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Tugas</th><th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Alpa</th><th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">%</th></tr></thead>
            <tbody>
            @foreach($dosenList as $d)
            @php 
                $rekap = $rekapDosen[$d->id] ?? collect();
                $hadirCount = $rekap->where('status', 'hadir')->first()?->count ?? 0;
                $total = $rekap->sum('count') ?: 1;
                $persen = round(($hadirCount / $total) * 100);
            @endphp
            <tr class="border-b border-siakad-light/50">
                <td class="py-3 px-5"><p class="font-medium text-siakad-dark">{{ $d->user->name }}</p><p class="text-xs text-siakad-secondary">{{ $d->nidn }}</p></td>
                <td class="py-3 px-5 text-center text-sm text-emerald-600 font-medium">{{ $rekap->where('status', 'hadir')->first()?->count ?? 0 }}</td>
                <td class="py-3 px-5 text-center text-sm text-blue-600">{{ $rekap->where('status', 'izin')->first()?->count ?? 0 }}</td>
                <td class="py-3 px-5 text-center text-sm text-amber-600">{{ $rekap->where('status', 'sakit')->first()?->count ?? 0 }}</td>
                <td class="py-3 px-5 text-center text-sm text-purple-600">{{ $rekap->where('status', 'tugas')->first()?->count ?? 0 }}</td>
                <td class="py-3 px-5 text-center text-sm text-red-600">{{ $rekap->where('status', 'alpa')->first()?->count ?? 0 }}</td>
                <td class="py-3 px-5 text-center"><span class="px-2 py-1 text-xs font-medium rounded-full {{ $persen >= 80 ? 'bg-emerald-100 text-emerald-700' : ($persen >= 60 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">{{ $persen }}%</span></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Detail -->
    <div class="card-saas overflow-hidden">
        <div class="px-6 py-4 border-b border-siakad-light"><h3 class="font-semibold text-siakad-dark">Detail Kehadiran</h3></div>
        <table class="w-full table-saas">
            <thead><tr class="bg-siakad-light/30"><th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Tanggal</th><th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Dosen</th><th class="text-left py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Mata Kuliah</th><th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Jam</th><th class="text-center py-3 px-5 text-xs font-semibold text-siakad-secondary uppercase">Status</th></tr></thead>
            <tbody>
            @forelse($kehadiranList as $k)
            <tr class="border-b border-siakad-light/50">
                <td class="py-3 px-5 text-sm text-siakad-dark">{{ $k->tanggal->format('d M Y') }}</td>
                <td class="py-3 px-5 text-sm text-siakad-dark">{{ $k->dosen->user->name }}</td>
                <td class="py-3 px-5 text-sm text-siakad-secondary">{{ $k->jadwalKuliah?->kelas?->mataKuliah?->nama ?? '-' }}</td>
                <td class="py-3 px-5 text-center text-sm text-siakad-secondary">{{ $k->jam_masuk ? substr($k->jam_masuk, 0, 5) : '-' }} - {{ $k->jam_keluar ? substr($k->jam_keluar, 0, 5) : '-' }}</td>
                <td class="py-3 px-5 text-center"><span class="px-2.5 py-1 text-xs font-medium rounded-full bg-{{ $k->status_color }}-100 text-{{ $k->status_color }}-700">{{ $k->status_label }}</span></td>
            </tr>
            @empty
            <tr><td colspan="5" class="py-8 text-center text-siakad-secondary">Belum ada data</td></tr>
            @endforelse
            </tbody>
        </table>
        @if($kehadiranList->hasPages())<div class="px-5 py-4 border-t border-siakad-light">{{ $kehadiranList->links() }}</div>@endif
    </div>
</x-app-layout>
