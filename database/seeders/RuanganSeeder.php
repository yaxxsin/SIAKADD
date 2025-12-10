<?php

namespace Database\Seeders;

use App\Models\Ruangan;
use Illuminate\Database\Seeder;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        $ruanganData = [
            // Gedung A - Kelas Reguler
            ['kode_ruangan' => 'A-101', 'nama_ruangan' => 'Ruang Kelas A101', 'kapasitas' => 40, 'gedung' => 'Gedung A', 'lantai' => 1, 'fasilitas' => 'AC, Proyektor, Whiteboard'],
            ['kode_ruangan' => 'A-102', 'nama_ruangan' => 'Ruang Kelas A102', 'kapasitas' => 40, 'gedung' => 'Gedung A', 'lantai' => 1, 'fasilitas' => 'AC, Proyektor, Whiteboard'],
            ['kode_ruangan' => 'A-201', 'nama_ruangan' => 'Ruang Kelas A201', 'kapasitas' => 50, 'gedung' => 'Gedung A', 'lantai' => 2, 'fasilitas' => 'AC, Proyektor, Whiteboard'],
            ['kode_ruangan' => 'A-202', 'nama_ruangan' => 'Ruang Kelas A202', 'kapasitas' => 50, 'gedung' => 'Gedung A', 'lantai' => 2, 'fasilitas' => 'AC, Proyektor, Whiteboard'],
            ['kode_ruangan' => 'A-301', 'nama_ruangan' => 'Ruang Kelas A301', 'kapasitas' => 60, 'gedung' => 'Gedung A', 'lantai' => 3, 'fasilitas' => 'AC, Proyektor, Whiteboard, Sound System'],
            
            // Gedung B - Laboratorium
            ['kode_ruangan' => 'LAB-1', 'nama_ruangan' => 'Lab Komputer 1', 'kapasitas' => 30, 'gedung' => 'Gedung B', 'lantai' => 1, 'fasilitas' => 'AC, 30 PC, Proyektor, Printer'],
            ['kode_ruangan' => 'LAB-2', 'nama_ruangan' => 'Lab Komputer 2', 'kapasitas' => 30, 'gedung' => 'Gedung B', 'lantai' => 1, 'fasilitas' => 'AC, 30 PC, Proyektor, Printer'],
            ['kode_ruangan' => 'LAB-3', 'nama_ruangan' => 'Lab Jaringan', 'kapasitas' => 25, 'gedung' => 'Gedung B', 'lantai' => 2, 'fasilitas' => 'AC, 25 PC, Switch, Router Cisco'],
            ['kode_ruangan' => 'LAB-4', 'nama_ruangan' => 'Lab Multimedia', 'kapasitas' => 20, 'gedung' => 'Gedung B', 'lantai' => 2, 'fasilitas' => 'AC, 20 iMac, Green Screen, Kamera'],
            
            // Gedung C - Aula dan Ruang Besar
            ['kode_ruangan' => 'AULA', 'nama_ruangan' => 'Aula Utama', 'kapasitas' => 200, 'gedung' => 'Gedung C', 'lantai' => 1, 'fasilitas' => 'AC Central, Proyektor Besar, Sound System, Podium'],
            ['kode_ruangan' => 'C-101', 'nama_ruangan' => 'Ruang Seminar', 'kapasitas' => 80, 'gedung' => 'Gedung C', 'lantai' => 1, 'fasilitas' => 'AC, Proyektor, Sound System, Podium'],
            ['kode_ruangan' => 'C-201', 'nama_ruangan' => 'Ruang Sidang', 'kapasitas' => 30, 'gedung' => 'Gedung C', 'lantai' => 2, 'fasilitas' => 'AC, Proyektor, Meja U-Shape'],
        ];

        foreach ($ruanganData as $data) {
            Ruangan::create(array_merge($data, ['is_active' => true]));
        }

        $this->command->info('Created ' . count($ruanganData) . ' ruangan');
    }
}
