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

    public function addData(Request $req){
        // validasi
        $validate = \Validator::make($req->all(), [
            'code' => 'required',
            'name' => 'required'
        ]);

        if ($validate->fails()) {
            $response=[
                'status' => false,
                'msg' => $validate->errors(),
            ];
            return response()->json(compact('response'), 200);
        }else{
            $layanan= new MLayanan(); 
            $layanan->kode_layanan = $req->code;
            $layanan->nama_layanan = $req->name;
            $layanan->save();

            $response=[
                'status' => true,
                'msg' => 'Success',
                'content' => $layanan
            ];
            return response()->json(compact('response'), 200);
        }

    }

    public function updateData(Request $req){
        # validate

        $layanan= MLayanan::find($req->id);
        $layanan->kode_layanan = $req->code;
        $layanan->nama_layanan = $req->name;
        $layanan->save();

        $response=[
            'status' => true,
            'msg' => 'Success',
            'content' => $layanan
        ];

        return response()->json(compact('response'), 200);
    }

    public function removeLayanan($id){
        $layanan = MLayanan::find($id);
        $layanan->delete();
        $response = [
            'status' => true,
            'msg' => 'Success'
        ];

        return response()->json(compact('response'), 200);
    }
}
