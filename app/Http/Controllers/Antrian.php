<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use carbon\Carbon;
use App\Models\Antrian as MAntri;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

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

    protected function sisaAntrian($idLayanan){
        $sisa = MANtri::where('id_layanan', $idLayanan)
                        ->where('status_call', false)
                        ->whereDate('created_at', date('Y-m-d'))
                        ->get()->count();
    
        return $sisa;
    }

    public function getSisaAntrian($idLayanan){
        $response = [
            'id' => $idLayanan,
            'sisa' => $this->sisaAntrian($idLayanan)
        ];

        return response()->json(compact('response'), 200);
    }

    public function cekPembatasan($idLayanan){
        $res = [
            'status' => true,
            'msg' => []
        ];
        $limit = \DB::table('layanans')
                ->where('id', $idLayanan)
                ->get();
        $limit_quota = $limit[0]->limit_quota;
        $limit_end = Carbon::createFromFormat('H:i:s', $limit[0]->limit_time_end);
        $now = Carbon::createFromFormat('H:i:s',date('H:i:s'));
        
        if ($limit_quota > 0) {
            $cur_antrian = MAntri::where('id_layanan', $idLayanan)
                                ->whereDate('antrians.created_at', '=', date('Y-m-d'))
                                ->get()->count();

            if ($cur_antrian + 1 > $limit_quota) {
                $res['status'] = false;
                $res['msg'] = Arr::prepend($res['msg'], 'Kuota sudah habis');
            }
            
        }
        
        if ($limit_end->ne(date('Y-m-d') .' '. '00:00:00')) {
            if ($now->gt($limit_end)) {
                $res['status'] = false;
                $res['msg'] = Arr::prepend($res['msg'], 'Waktu pelayanan sudah berakhir');
            }
        }
        
        return $res;
    }

    public function getNomorAntrian($idLayanan){
        $limiter = $this->cekPembatasan($idLayanan);
        
        if ($limiter['status']) {
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
            
            $response = [
                'status' => true,
                'antrian' => $antrian
            ];                    
            return response()->json(compact('response'), 200);
        }else{
            $response = [
                'status' => $limiter['status'],
                'msg' => $limiter['msg']
            ];
            return response()->json(compact('response'), 200);
        }

    }

    public function postGetNomorAntrian(Request $req){
        $idLayanan = $req->id_layanan;
        $codeBooking = $req->code_booking;

        $limiter = $this->cekPembatasan($idLayanan);
        
        if ($limiter['status']) {
            $nomorAntrian = $this->getLastNomorAntrian($idLayanan);
            $id = date('Ymd') . $idLayanan . $nomorAntrian + 1;
            $antri = new MAntri();
            $antri->id = $id;
            $antri->nomor_antrian = $nomorAntrian + 1;
            $antri->id_layanan = $idLayanan;
            $antri->kode_booking_online = $codeBooking;
            $antri->save();
    
            $antrian = MAntri::where('antrians.id', '=', $id)
                                ->join('layanans', 'layanans.id', '=', 'antrians.id_layanan')
                                ->get(['nomor_antrian', 
                                        'antrians.kode_booking_online as kode_booking', 
                                        'layanans.id as id_layanan', 'kode_layanan', 
                                        'layanans.show_footer',
                                        'layanans.show_qr',
                                        'layanans.qr_text',
                                        'layanans.notes',
                                        'nama_layanan', 
                                        'antrians.created_at'])[0];

            $tmpAntrian = collect([
                'id_layanan' => $antrian['id_layanan'],
                'kode_booking' => $antrian['kode_booking'],
                'kode_layanan' => $antrian['kode_layanan'],
                'nama_layanan' => $antrian['nama_layanan'],
                'nomor_antrian' => $antrian['nomor_antrian'],
                'created_at' => $antrian['created_at']
            ]);

            $tmpConf = collect([
                'show_footer' => $antrian['show_footer'],
                'show_qr' => $antrian['show_qr']
            ]);

            if ($antrian['show_qr']) {
                $tmpConf->prepend($antrian['qr_text'], 'qr_text');
                $tmpConf->prepend($antrian['notes'], 'notes');
            }


            $tmp = DB::table('global_confs')->get(['name', 'value']);

            foreach ($tmp as $itm) {
                if ($itm->name == 'footer') {
                    if ($antrian['show_footer']) {
                        $tmpConf->prepend($itm->value, $itm->name);   
                    }
                }else if($itm->name == 'logo'){
                    $tmpConf->prepend(asset('images/' . $itm->value), $itm->name);
                }else{
                    $tmpConf->prepend($itm->value, $itm->name);
                }
            }

            $tmpAntrian->prepend($tmpConf, 'configs');
            
            $response = [
                'status' => true,
                'antrian' => $tmpAntrian
            ];                    
            return response()->json(compact('response'), 200);
        }else{
            $response = [
                'status' => $limiter['status'],
                'msg' => $limiter['msg']
            ];
            return response()->json(compact('response'), 200);
        }

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
                                'antrians.kode_booking_online'
                            ])->first();
        if($antrian){
            $update = MAntri::find($antrian->id);
            $update->status_call = true;
            $update->id_loket = $idLoket;
            $update->save();
        }
        return response()->json(compact('antrian'), 200);
    }

    public function getAllAntrianTerahir(){
        $antrian = []; 
        $lokets = \DB::table('lokets')->select('id', 'nama_loket')->get();

        foreach ($lokets as $loket) {
            $last = MAntri::join('layanans', 'layanans.id', '=', 'antrians.id_layanan')
                            ->where('status_call', true)
                            ->where('id_loket', '=', $loket->id)
                            ->whereDate('antrians.created_at', '=', date('Y-m-d'))
                            ->orderBy('nomor_antrian', 'DESC')
                            ->get(['nomor_antrian', 'layanans.kode_layanan'])->first();
            $res = [
                'loket' => $loket->nama_loket,
                'nomor_antri'=>$last['nomor_antrian'],
                'kode_layanan' => $last['kode_layanan']
            ];
            
            $antrian = Arr::prepend($antrian, $res);
        }

        return response()->json(compact('antrian'), 200);
    }

    public function getAntrianTrakhirInLoket($idLayanan){
        $antrian = MAntri::join('layanans', 'layanans.id', '=', 'antrians.id_layanan')
                            ->where('id_layanan', $idLayanan)
                            ->where('status_call', true)
                            ->whereDate('antrians.created_at', '=', date('Y-m-d'))
                            ->orderBy('nomor_antrian', 'DESC')
                            ->get(['nomor_antrian as nomor', 'layanans.kode_layanan as layanan'])
                            ->first();
                            
        return response()->json(compact('antrian'), 200);
    }

    public function getPengambilan($idLayanan) {
        $pengambilans = MAntri::where('antrians.status_call', '=', false)
                        ->where('antrians.id_layanan', '=', $idLayanan)
                        ->whereDate('antrians.created_at', '=', date('Y-m-d'))
                        ->join('layanans', 'layanans.id', '=', 'antrians.id_layanan')
                        ->orderBy('antrians.nomor_antrian', 'ASC')
                        ->get([
                            'antrians.nomor_antrian as no_antri',
                            'antrians.kode_booking_online as no_permohonan'
                        ]);
        return response()->json(compact('pengambilans'));
    }

}
