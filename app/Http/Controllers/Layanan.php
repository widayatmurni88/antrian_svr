<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use App\Models\Layanan as MLayanan;

class Layanan extends Controller
{

    public function getLayanans(){
        return MLayanan::orderBy('id', 'DESC')
                        ->get(['id', 'nama_layanan as name', 'kode_layanan as code']);
    }

    public function getLayanan(){
        $layanan = MLayanan::get(['id','nama_layanan as layananName', 'kode_layanan as layananCode']);
        return response()->json(compact('layanan'));
    }

    public function getLayananWithLastAntrian(){
        $layanans = [];
        $lays = DB::table('layanans')
                    ->orderBy('kode_layanan', 'DESC')
                    ->get(['layanans.id','layanans.nama_layanan', 'layanans.kode_layanan']);
        foreach ($lays as $layanan) {
            $last_antrian = DB::table('antrians')
                    ->where('id_layanan', $layanan->id)
                    ->whereDate('created_at', date('Y-m-d'))
                    ->orderBy('nomor_antrian', 'DESC')
                    ->first();
            
            if ($last_antrian) {
                $last = $last_antrian->nomor_antrian;
            }else{
                $last = 0;
            }

            $layanan_with_nomor = [
                'id' => $layanan->id,
                'kode' => $layanan->kode_layanan,
                'nama' => $layanan->nama_layanan,
                'nomor_antrian_terakhir' => $last
            ];

            $layanans = Arr::prepend($layanans, $layanan_with_nomor);

        }
        return response()->json(compact('layanans'), 200);
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

    public function getLimiter(){
        $limiter = MLayanan::get(['id', 'nama_layanan as layanan', 'limit_time_start as limit_start', 'limit_time_end as limit_end', 'limit_quota']);
        return response()->json(compact('limiter'), 200);
    }

    public function updateLimit(Request $req){
        $limit = Mlayanan::find($req->id);

        if ($limit) {
            $limit->limit_time_start = $req->limit_start;
            $limit->limit_time_end = $req->limit_end;
            $limit->limit_quota = $req->limit_quota;
            $limit->save();

            $response = [
                'status' => true,
                'msg' => 'Success'
            ];

            return response()->json(compact('response'), 200);
        }
    }
}
