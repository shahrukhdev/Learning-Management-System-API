<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JuzSeeder extends Seeder
{
    public function run(): void
    {
        $juz = [
            ['number' => 1, 'name' => 'Alif Lam Meem'],
            ['number' => 2, 'name' => 'Sayaqool'],
            ['number' => 3, 'name' => 'Tilkal Rusul'],
            ['number' => 4, 'name' => 'Lan Tana Loo'],
            ['number' => 5, 'name' => 'Wal Muhsanat'],
            ['number' => 6, 'name' => 'La Yuhibbullah'],
            ['number' => 7, 'name' => 'Wa Iza Samiu'],
            ['number' => 8, 'name' => 'Wa Lau Annana'],
            ['number' => 9, 'name' => 'Qalal Malao'],
            ['number' => 10, 'name' => "Wa A'lamu"],
            ['number' => 11, 'name' => 'Yatazeroon'],
            ['number' => 12, 'name' => "Wa Mamin Da'abat"],
            ['number' => 13, 'name' => 'Wa Ma Ubrioo'],
            ['number' => 14, 'name' => 'Rubama'],
            ['number' => 15, 'name' => 'Subhanallazi'],
            ['number' => 16, 'name' => 'Qal Alam'],
            ['number' => 17, 'name' => 'Iqtaraba'],
            ['number' => 18, 'name' => 'Qad Aflaha'],
            ['number' => 19, 'name' => 'Wa Qalallazina'],
            ['number' => 20, 'name' => "A'man Khalaq"],
            ['number' => 21, 'name' => 'Utlu Ma Oohi'],
            ['number' => 22, 'name' => 'Wa Manyaqnut'],
            ['number' => 23, 'name' => 'Wa Mali'],
            ['number' => 24, 'name' => 'Faman Azlam'],
            ['number' => 25, 'name' => 'Ilayhi Yurad'],
            ['number' => 26, 'name' => 'Ha Meem'],
            ['number' => 27, 'name' => 'Qala Fama Khatbukum'],
            ['number' => 28, 'name' => 'Qad Sami Allah'],
            ['number' => 29, 'name' => 'Tabarakallazi'],
            ['number' => 30, 'name' => 'Amma'],
        ];

        DB::table('juz')->upsert(
            $juz,
            ['number'],
            ['name']
        );
    }
}