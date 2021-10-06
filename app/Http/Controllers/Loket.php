<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Loket extends Controller
{
    public function getLoket(){
        $loket = DB::table('lokets')
                 ->join('layanans', 'lokets.id_layanan', '=', 'layanans.id')
                 ->select('lokets.id','lokets.nama_loket as loketNo', 'layanans.kode_layanan as layananCode')
                 ->get();
        return response()->json(compact('loket'));
    }

    public function getLoketSingle($id){
        $loket = DB::table('lokets')
                 ->where('id', '=', $id)
                 ->select('id', 'nama_loket as loketNo')
                 ->get()[0];
        return response()->json(compact('loket'), 200);
    }
}
