<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GlobalConfs extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $items = [
            ['name' => 'kop', 'value' => 'KOP DIATAS'],
            ['name' => 'footer', 'value' => 'FOOTER BAWAH'],
            ['name' => 'aduan', 'value' => 'Pengaduan'],
            ['name' => 'logo', 'value' => 'logo.png'],
        ];

        foreach ($items as $item) {
            DB::table('global_confs')->insert($item);
        }
    }
}
