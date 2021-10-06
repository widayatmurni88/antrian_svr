<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Layanan as MLayanan;

class Layanan extends Controller
{
    public function getLayanan(){
        $layanan = MLayanan::get(['id','nama_layanan as layananName', 'kode_layanan as layananCode']);
        return response()->json(compact('layanan'));
    }

    public function getLayananName($code){
        $layanan = MLayanan::where('kode_layanan', '=', $code)->get(['id', 'nama_layanan as layananName', 'kode_layanan as layananCode'])->toArray();
        return response()->json(compact('layanan'), 200);
    }

    public function getIdLayanan($code){
        $layanan = MLayanan::where('kode_layanan', '=', $code)->get(['id', 'nama_layanan as layananName'])[0];
        return response()->json(compact('layanan'), 200);
    }
}
