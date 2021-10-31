<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
// use App\Controllers\Multimedia as Media;

class Display extends Controller
{
    public function getDisplayDataOnStartUp(){
        $layanans = $this->getAllLayanan();
        $lokets = $this->getAllLoket();
        $lays = [];
        $loks = [];

        // get antrian terakhir in layanan
        foreach ($layanans as $lay) {
            $last = $this->getLastAntrianInLayanan($lay->id);
            $data = [
                'id' => $lay->id,
                'name' => $lay->name,
                'code' => $lay->code,
                'nomor_antrian' => $last
            ];
            $lays = Arr::prepend($lays, $data);
        }

        // get antrian terakhir on loket
        foreach ($lokets as $lok) {
            $last = $this->getLastAntrianInLoket($lok->id);
            $data = [
                'id' => $lok->id,
                'name' => $lok->name,
                'nomor_antrian' => $last['nomor'],
                'code' => $last['code']
            ];

            $loks = Arr::prepend($loks, $data);
        }

        // get Multimedia
        $media = new Multimedia();
        $media = $media->getVideos();

        $display = [
            'layanans' => $lays,
            'lokets' => $loks,
            'medias' => $media
        ];

        return response()->json(compact('display'), 200);
    }

    protected function getAllLayanan(){
        return DB::table('layanans')
                    ->select(['id', 'nama_layanan as name', 'kode_layanan as code'])
                    ->orderBy('kode_layanan', 'DESC')
                    ->get();
    }

    protected function getAllLoket(){
        return DB::table('lokets')
                ->select(['id', 'nama_loket as name'])
                ->orderBy('nama_loket', 'DESC')
                ->get();
    }

    protected function getLastAntrianInLayanan($idLayanan){
        $last = DB::table('antrians')
                ->select(['nomor_antrian as nomor'])
                ->where('id_layanan', $idLayanan)
                ->where('status_call', true)
                ->whereDate('created_at', date('Y-m-d'))
                ->orderBy('nomor_antrian', 'DESC')
                ->first();
        
        if ($last == null) {
            $last = 0;
        }else {
            $last = $last->nomor;
        }

        return $last;
    }

    protected function getLastAntrianInLoket($idLoket){
        $last = DB::table('antrians')
                ->join('layanans', 'layanans.id', '=', 'antrians.id_layanan')
                ->select(['nomor_antrian as nomor', 'layanans.kode_layanan as code_layanan'])
                ->where('id_loket', $idLoket)
                ->where('status_call', true)
                ->whereDate('antrians.created_at', date('Y-m-d'))
                ->orderBy('nomor_antrian', 'DESC')
                ->first();
        
        if ($last == null) {
            $last = [
                'nomor' => 0,
                'code' => 0
            ];
        }else {
            $last = [
                'nomor' => $last->nomor,
                'code' => $last->code_layanan
            ];
        }

        return $last;
    }
}
