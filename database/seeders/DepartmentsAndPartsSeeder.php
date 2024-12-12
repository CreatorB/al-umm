<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartmentsAndPartsSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            'Kesantrian',
            'Akademik', 
            'Kepala Bagian', 
            'Umum', 
            'Staff'
        ];

        foreach ($departments as $departmentName) {
            DB::table('departments')->insertOrIgnore([
                'name' => $departmentName,
                'code' => strtoupper(str_replace(' ', '_', $departmentName)),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }

        $parts = [
            'Kesehatan',
            'PKBM',
            'Kabag Kurikulum',
            'Guru Diniyyah',
            'Mudir',
            'Security',
            'HRD',
            'Kabag Kesantrian',
            'Pengampu',
            'Helper',
            'Kesantrian',
            'Bendahara',
            'Kaur Diniyyah',
            'Kesehatan',
            'Kabag Bahasa',
            'Maintenance',
            'OB',
            'Admin Akademik',
            'Sekreteris',
            'IT Support',
            'Kabag Umum',
            'Kaur Maktabah',
            'Multimedia',
            'Kaur Tahfizh',
            'Kaur Umum'
        ];

        $departmentMappings = [
            'Kesehatan' => 'Kesantrian',
            'PKBM' => 'Akademik',
            'Kabag Kurikulum' => 'Kepala Bagian',
            'Guru Diniyyah' => 'Akademik',
            'Mudir' => 'Kepala Bagian',
            'Security' => 'Umum',
            'HRD' => 'Kepala Bagian',
            'Kabag Kesantrian' => 'Kepala Bagian',
            'Pengampu' => 'Akademik',
            'Helper' => 'Staff',
            'Kesantrian' => 'Kesantrian',
            'Bendahara' => 'Kepala Bagian',
            'Kaur Diniyyah' => 'Akademik',
            'Kabag Bahasa' => 'Kepala Bagian',
            'Maintenance' => 'Umum',
            'OB' => 'Umum',
            'Admin Akademik' => 'Akademik',
            'Sekreteris' => 'Kepala Bagian',
            'IT Support' => 'Staff',
            'Kabag Umum' => 'Kepala Bagian',
            'Kaur Maktabah' => 'Kepala Bagian',
            'Multimedia' => 'Staff',
            'Kaur Tahfizh' => 'Akademik',
            'Kaur Umum' => 'Akademik'
        ];

        foreach ($parts as $partName) {
            $departmentName = $departmentMappings[$partName] ?? 'Staff';
            $departmentId = DB::table('departments')
                ->where('name', $departmentName)
                ->value('id');
            DB::table('parts')->insertOrIgnore([
                'name' => $partName,
                'code' => strtoupper(str_replace(' ', '_', $partName)),
                'department_id' => $departmentId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}