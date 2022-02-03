<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class Report extends Controller{

    public function index(){
      // GET DATA
      $columns = $this->getColumn();
      return response()->json(compact('columns'), 200);
    }

    protected function getColumn(){
      $lays = $this->getLayanans();
      $cols = [[
        'col_pos' => 0,
        'headerName' => 'Loket',
        'field' => 'name'
      ]];
      $i=1;
      foreach ($lays as $lay) {
        $l=[
          'col_pos' => $i++,
          'headerName' => $lay->name,
          'field' => $lay->name,
        ];
        $cols = Arr::prepend($cols, $l);
      }
      return $cols;
    }

    protected function getLayanans(){
      $layanans = new Layanan();
      return $layanans->getLayanans();
    }

    protected function getLokets(){
      $lokets = new Loket();
      return $lokets->getLoketss();
    }

    public function getMonthYears(){
      $month = ['-Semua-', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      $years = ['2020','2021', '2022'];
      $data = ['month' => $month, 'years' => $years];
      return response()->json(compact('data'), 200);
    }

    public function getReportData($month = 0, $year = 2021 ){
      
      $report = ['dilayani' => $this->getTotalByLokets($month, $year),
                 'all' => $this->getResumeReport($month, $year)];
      
      return response()->json(compact('report'), 200);
    }

    protected function getTotalByLokets($month=0, $year=2021){
      $isi=[];
      $lokets = $this->getLokets();
      if ($lokets) {
        foreach ($lokets as $loket) {
          
          $tmp= collect([
            'id' => $loket->id,
            'name' => 'Loket ' . $loket->loket,
          ]);
          
          $lays = $this->getTotalPerLoketPerLayanan($loket->id, $month, $year);
          
          foreach ($lays as $lay ) {
            $tmp->prepend($lay['value'], $lay['name']);
          }

          $isi = Arr::prepend($isi, $tmp);
        }
      }
      return $isi;
    }

    protected function getTotalByLayanan($idLayanan, $month=0, $year=2021){
      $data;
      if ($month==0) {
        $data = DB::table('antrians')
                ->join('layanans', 'layanans.id', '=', 'antrians.id_layanan')
                ->whereYear('antrians.created_at', $year)
                ->where('antrians.id_layanan', $idLayanan)
                ->selectRaw('count(*) total')
                ->get();
      }else{
        $data = DB::table('antrians')
                ->join('layanans', 'layanans.id', '=', 'antrians.id_layanan')
                ->whereYear('antrians.created_at', $year)
                ->whereMonth('antrians.created_at', $month)
                ->where('antrians.id_layanan', $idLayanan)
                ->selectRaw('count(*) total')
                ->get();
      }
      return $data[0]->total;
    }

    protected function getTotalAntrianByLayanan($month=0, $year=2021){
      $layanans = $this->getLayanans();
      $tmp;
      $isi=[];
      foreach ($layanans as $lay) {
        $tmp = [
          'id' => $lay->id,
          'name' => $lay->name,
          'code' => $lay->code,
          'total' => $this->getTotalByLayanan($lay->id, $month, $year),
        ];
        $isi = Arr::prepend($isi, $tmp);
      }

      return $isi;
    }

    protected function getTotalPerLoketPerLayanan($idLoket, $month=0, $year=2021){
      $layanans = $this->getLayanans();
      $tmp;
      $isi=[];
      foreach ($layanans as $lay) {
        $tmp = [
          'name' => $lay->name,
          'value' => $this->getTotalByLoketLayanan($idLoket, $lay->id, $month, $year),
        ];
        $isi = Arr::prepend($isi, $tmp);
      }

      return $isi;
    }

    protected function getTotalByLoketLayanan($idLoket, $idLayanan, $month=0, $year=2021){
      $data;
      if ($month==0) {
        // ALL YEAR
        $data = DB::table('antrians')
                ->join('layanans', 'layanans.id', '=', 'antrians.id_layanan')
                ->whereYear('antrians.created_at', $year)
                ->where('antrians.id_layanan', $idLayanan)
                ->where('antrians.id_loket', $idLoket)
                ->selectRaw('count(*) total')
                ->get();
      }else{
        $data = DB::table('antrians')
                ->join('layanans', 'layanans.id', '=', 'antrians.id_layanan')
                ->whereYear('antrians.created_at', $year)
                ->whereMonth('antrians.created_at', $month)
                ->where('antrians.id_layanan', $idLayanan)
                ->where('antrians.id_loket', $idLoket)
                ->selectRaw('count(*) total')
                ->get();
      }

      return $data[0]->total;
    }

    protected function getTotalCall($callStatus, $idLayanan, $month=0, $year=2021){
      $data=[];
      if ($month==0) {
        // ALL YEAR
        $data = DB::table('antrians')
                ->join('layanans', 'layanans.id', '=', 'antrians.id_layanan')
                ->whereYear('antrians.created_at', $year)
                ->where('antrians.id_layanan', $idLayanan)
                ->where('antrians.status_call', $callStatus)
                ->selectRaw('count(*) jml')
                ->get();
      }else{
        $data = DB::table('antrians')
                ->join('layanans', 'layanans.id', '=', 'antrians.id_layanan')
                ->whereYear('antrians.created_at', $year)
                ->whereMonth('antrians.created_at', $month)
                ->where('antrians.id_layanan', $idLayanan)
                ->where('antrians.status_call', $callStatus)
                ->selectRaw('count(*) jml')
                ->get();
      }
      return $data[0]->jml;
    }

    protected function getResumeReport($month, $year){
      $layanans = $this->getLayanans();
      $tmp=[];
      foreach ($layanans as $lay) {
        $called = $this->getTotalCall(true, $lay->id, $month, $year);
        $unCalled = $this->getTotalCall(false, $lay->id, $month, $year);
        $res = [
          'name' => $lay->name,
          'dilayani' => $called,
          'belum' => $unCalled,
          'total' => $called + $unCalled,
        ];

        $tmp = Arr::prepend($tmp, $res);
      }
      return $tmp;
    }

}
