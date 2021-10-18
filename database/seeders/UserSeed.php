<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        // DB::table('users')->insert(['name'=>'loket', 'email'=>'loket','password'=>bcrypt('123')]);
        User::create([
            'email' => 'loket1@mail.com',
            'name' => 'Loket1',
            'password' => \Hash::make('123'),
            'status' => 'aktif',
        ]);

        User::create([
            'email' => 'admin@mail.com',
            'name' => 'admin',
            'password' => \Hash::make('123'),
            'status' => 'aktif',
            'role' => true
        ]);
    }
}
