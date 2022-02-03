<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class Dashboard extends Controller
{
    protected function getLayanans(){
        return DB::table('layanans')
                ->select(['id', 'nama_layanan as name', 'kode_layanan as code'])
                ->orderBy('kode_layanan', 'DESC')
                ->get();
    }

    protected function getTotalAntrian($idLayanan){
        return DB::table('antrians')
                    ->whereDate('created_at', date('Y-m-d'))
                    ->where('id_layanan', $idLayanan)
                    ->get()
                    ->count();
        return $count;
    }

    protected function getJumlahAntrianSudahDilayani($idLayanan){
        return DB::table('antrians')
                    ->whereDate('created_at', date('Y-m-d'))
                    ->where('status_call', true)
                    ->where('id_layanan', $idLayanan)
                    ->get()
                    ->count();
    }

    protected function getAntrianLastCall($idLayanan){
        return DB::table('antrians')
                ->join('layanans', 'layanans.id', '=', 'antrians.id_layanan')
                ->join('lokets', 'lokets.id', '=', 'antrians.id_loket')
                ->where('status_call', true)
                ->where('antrians.id_layanan', $idLayanan)
                ->whereDate('antrians.created_at', date('Y-m-d'))
                ->orderBy('nomor_antrian', 'DESC')
                ->get(['nomor_antrian', 'lokets.nama_loket as loket', 'id_loket'])
                ->first();
    }

    public function getRekapAntrian(){
        $layanans = [];
        $lays = $this->getLayanans();
        if ($lays) {
            foreach ($lays as $layanan) {
                $data = [
                    'id' => $layanan->id,
                    'name' => $layanan->name,
                    'code' => $layanan->code,
                    'jml_antrian' => $this->getTotalAntrian($layanan->id),
                    'sudah_dilayani' => $this->getJumlahAntrianSudahDilayani($layanan->id),
                    'last_call' => $this->getAntrianLastCall($layanan->id),
                ];
                $layanans = Arr::prepend($layanans, $data);
            }
        }
        return response()->json(compact('layanans'), 200);
    }
}
