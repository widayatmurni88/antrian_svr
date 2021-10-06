<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Antrian as An;

class AntrianSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        An::create([
            'id' => date('Ymd'),
            'nomor_antrian' => 1,
            'id_layanan' => 2
        ]);   
    }
}
