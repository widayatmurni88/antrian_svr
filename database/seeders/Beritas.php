<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Beritas extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            ['isi_berita' => '1Lorem ipsum dolor sit amet consectetur adipisicing elit. Corporis quos, possimus harum cum dicta accusamus et eligendi delectus laboriosam magnam, quod deleniti architecto voluptate maiores excepturi numquam aspernatur non dolores!'],
            ['isi_berita' => '2Lorem ipsum dolor sit amet consectetur adipisicing elit. Corporis quos, possimus harum cum dicta accusamus et eligendi delectus laboriosam magnam, quod deleniti architecto voluptate maiores excepturi numquam aspernatur non dolores!'],
            ['isi_berita' => '3Lorem ipsum dolor sit amet consectetur adipisicing elit. Corporis quos, possimus harum cum dicta accusamus et eligendi delectus laboriosam magnam, quod deleniti architecto voluptate maiores excepturi numquam aspernatur non dolores!'],
            ['isi_berita' => '4Lorem ipsum dolor sit amet consectetur adipisicing elit. Corporis quos, possimus harum cum dicta accusamus et eligendi delectus laboriosam magnam, quod deleniti architecto voluptate maiores excepturi numquam aspernatur non dolores!'],
            ['isi_berita' => '5Lorem ipsum dolor sit amet consectetur adipisicing elit. Corporis quos, possimus harum cum dicta accusamus et eligendi delectus laboriosam magnam, quod deleniti architecto voluptate maiores excepturi numquam aspernatur non dolores!'],
        ];

        foreach ($items as $item) {
            DB::table('beritas')->insert($item);
        }
    }
}
