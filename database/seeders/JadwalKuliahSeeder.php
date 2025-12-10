<?php

namespace Database\Seeders;

use App\Models\JadwalKuliah;
use App\Models\Kelas;
use Illuminate\Database\Seeder;

class JadwalKuliahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelasList = Kelas::all();
        
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jamList = [
            ['07:30', '09:10'],
            ['09:20', '11:00'],
            ['13:00', '14:40'],
            ['14:50', '16:30'],
        ];
        $ruanganList = ['LT-101', 'LT-102', 'LT-201', 'LT-202', 'LAB-1', 'LAB-2', 'R-301', 'R-302'];
        
        foreach ($kelasList as $index => $kelas) {
            $hariIndex = $index % count($hariList);
            $jamIndex = $index % count($jamList);
            $ruanganIndex = $index % count($ruanganList);
            
            JadwalKuliah::create([
                'kelas_id' => $kelas->id,
                'hari' => $hariList[$hariIndex],
                'jam_mulai' => $jamList[$jamIndex][0],
                'jam_selesai' => $jamList[$jamIndex][1],
                'ruangan' => $ruanganList[$ruanganIndex],
            ]);
        }
        
        $this->command->info('Created ' . $kelasList->count() . ' jadwal kuliah');
    }
}
