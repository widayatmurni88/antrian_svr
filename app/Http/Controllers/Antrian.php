<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antrian as MAntri;

class Antrian extends Controller
{
    
    protected function getLastNomorAntrian($idLayanan){
        $lastNum = MAntri::where('id_layanan', '=', $idLayanan)
                            ->whereDate('created_at', '=', date('Y-m-d'))
                            ->orderBy('nomor_antrian', 'DESC')
                            ->get(['nomor_antrian'])->first();
        if ($lastNum) {
            return $lastNum->nomor_antrian;
        }else{
            return 0;
        }
    }

    public function getNomorAntrian($idLayanan){
        $nomorAntrian = $this->getLastNomorAntrian($idLayanan);
        $id = date('Ymd') . $idLayanan . $nomorAntrian + 1;
        $antri = new MAntri();
        $antri->id = $id;
        $antri->nomor_antrian = $nomorAntrian + 1;
        $antri->id_layanan = $idLayanan;
        $antri->save();

        $antrian = MAntri::where('antrians.id', '=', $id)
                            ->join('layanans', 'layanans.id', '=', 'antrians.id_layanan')
                            ->get(['nomor_antrian', 'kode_layanan', 'nama_layanan', 'antrians.created_at'])[0];

        return response()->json(compact('antrian'), 200);
    }

    public function getCallAntrian($idLayanan, $idLoket){

        $antrian = MAntri::where('antrians.status_call', '=', false)
                            ->where('antrians.id_layanan', '=', $idLayanan)
                            ->whereDate('antrians.created_at', '=', date('Y-m-d'))
                            ->join('layanans', 'layanans.id', '=', 'antrians.id_layanan')
                            ->orderBy('antrians.nomor_antrian', 'ASC')
                            ->get([
                                'antrians.id',
                                'antrians.nomor_antrian',
                                'layanans.nama_layanan',
                                'layanans.kode_layanan',
                            ])->first();
        if($antrian){
            $update = MAntri::find($antrian->id);
            $update->status_call = true;
            $update->id_loket = $idLoket;
            $update->save();
        }
        return response()->json(compact('antrian'), 200);
    }


}
