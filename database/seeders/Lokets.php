<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Lokets extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['nama_loket' => 1, 'id_layanan' => 1],
            ['nama_loket' => 2, 'id_layanan' => 1],
            ['nama_loket' => 3, 'id_layanan' => 2],
            ['nama_loket' => 4, 'id_layanan' => 3],
            ['nama_loket' => 5, 'id_layanan' => 2],
        ];

        foreach ($items as $item) {
            DB::table('lokets')->insert($item);
        }
    }
}
