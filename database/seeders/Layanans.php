<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Layanans extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['nama_layanan' => 'Layanan Online',
             'kode_layanan' => 'A'],
            ['nama_layanan' => 'Layanan BAP',
             'kode_layanan' => 'B'],
            ['nama_layanan' => 'Layanan Asing',
             'kode_layanan' => 'C'],
        ];

        foreach ($items as $item) {
            DB::table('layanans')->insert($item);
        }
    }
}
