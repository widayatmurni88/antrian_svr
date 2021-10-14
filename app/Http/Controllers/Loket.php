<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Loket as MLoket;

class Loket extends Controller
{
    public function getLoket(){
        $loket = DB::table('lokets')
                 ->join('layanans', 'lokets.id_layanan', '=', 'layanans.id')
                 ->select('lokets.id','lokets.nama_loket as loketNo', 'layanans.kode_layanan as layananCode', 'lokets.desc')
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

    public function getAllLoket(){
        $loket = DB::table('lokets')
                 ->select('lokets.id','lokets.nama_loket as loketNo', 'lokets.desc')
                 ->get();
        return response()->json(compact('loket'));
    }

    public function addLoket(Request $req){
        $valid = \Validator::make($req->all(), [
            'loket_no' => 'required'
        ]);

        if ($valid->fails()) {
            $response = [
                'status' => false,
                'msg'    => 'Nomor loket harus diisi!'
            ];

            return response()->json(compact('response'), 200);
        }else{
            // save
            $loket = new MLoket();
            $loket->nama_loket = $req->loket_no;
            $loket->desc = $req->desc;
            $loket->save();

            $response = [
                'status' => true,
                'msg' => 'Success',
                'content' => $loket
            ];

            return response()->json(compact('response'), 200);
        }
    }

    public function removeLoket($id){
        $loket = MLoket::find($id);
        $loket->delete();
        $response = [
            'status' => true,
            'msg' => 'Success'
        ];

        return response()->json(compact('response'), 200);
    }
}
