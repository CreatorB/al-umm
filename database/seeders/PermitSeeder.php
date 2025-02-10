<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permit;

class PermitSeeder extends Seeder
{
    public function run()
    {
        $permits = [
            ['name' => 'Istri / Suami / Anak pegawai meninggal dunia', 'max_days' => 3],
            ['name' => 'Orang tua / Mertua pegawai meninggal dunia', 'max_days' => 2],
            ['name' => 'Orang tua sakit keras', 'max_days' => 1],
            ['name' => 'Saudara kandung pegawai meninggal dunia', 'max_days' => 1],
            ['name' => 'Orang yang tinggal serumah meninggal dunia', 'max_days' => 1],
            ['name' => 'Pernikahan saudara Orang tua / Mertua / Anak / Istri sakit parah', 'max_days' => 1],
            ['name' => 'Pernikahan Pegawai', 'max_days' => 3],
            ['name' => 'Pernikahan Anak Pegawai', 'max_days' => 2],
            ['name' => 'Kelahiran / Keguguran Anak Pegawai', 'max_days' => 2],
            ['name' => 'Khitanan Anak pegawai', 'max_days' => 2],
            ['name' => 'Menunaikan Umroh', 'max_days' => 10],
            ['name' => 'Menunaikan Haji', 'max_days' => 40],
        ];

        foreach ($permits as $permit) {
            Permit::create($permit);
        }
    }
}
