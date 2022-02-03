<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class TiketConf extends Controller
{
    protected function getLayanans(){
        $layanans = new Layanan();
        return $layanans->getLayanans();
    }

    protected function getGlobalConfs(){
        $temp=[];
        $confs = DB::table('global_confs')->orderBy('id', 'DESC')->get(['id', 'name', 'value']);
        foreach ($confs as $conf) {
            if ($conf->name == 'logo') {
                $temp = Arr::prepend($temp, ['id' => $conf->id, 'name' => $conf->name, 'value' => asset('images/' . $conf->value . '?' . date('YmdHis'))]);
            }else{
                $temp = Arr::prepend($temp, $conf);
            }
        }
        return $temp;
    }

    public function getGlobalConf(){
        $layanans = $this->getLayanans();
        $globalConf = $this->getGlobalConfs();

        $response = [
            'layanans' => $layanans,
            'globalConfs' => $globalConf,
        ];
        return response()->json(compact('response'), 200);
    }

    public function uploadLogo(Request $req){
        $validate = \Validator::make($req->all(), [
            'logo' => 'mimes: jpg,jpeg,png|required'
        ]);

        if ($validate->fails()) {
            $response = [
                'status' => false,
                'msg' => $validate->errors()
            ];

            return response()->json(compact('response'), 200);
        } else {
            $logoFile = $req->logo;
            $logoName = 'logo' . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move('images', $logoName);
            
            $response = [
                'status' => true,
                'msg' => 'Success',
                'content' => [
                    'filename' => $logoName,
                    'link' => asset('images/'. $logoName),
                ]
            ];

            return response()->json(compact('response'), 200);
        }
    }

    public function saveEdited(Request $req){
        $fields = $req->field;
        $res=[];
        
        // SAVE TO FIELD
        if ($fields) {
            $fields = json_decode($fields);

            foreach ($fields as $field) {
                $aksi = DB::table('global_confs')->where('id', $field->id)
                ->update(['value' => $field->value]);
            }
        }

        //SAVE LOGO
        $logoFile = $req->logo;
        if ($logoFile) {
            $logoName = 'logo' . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move('images', $logoName);
        }

        $dbRes = DB::table('global_confs')->get(['id', 'name', 'value']);
        $res= [
            'status' => true,
            'msg' => 'Success',
            'content' => $this->getGlobalConfs(),
        ];
        
        return response()->json(compact('res'), 200);
    }

    public function getSpesificConf($idLayanan){
        $response = DB::table('layanans')
                ->where('id', $idLayanan)
                ->get([ 'id', 
                        'kode_layanan as code',
                        'nama_layanan as name',
                        'show_footer',
                        'show_qr',
                        'qr_text',
                        'notes']);

        return response()->json(compact('response'), 200); 
    }

    public function saveSpesificConf(Request $req){
        $data = [];
        if ($req->show_footer) {
            $data = Arr::prepend($data, filter_var($req->show_footer, FILTER_VALIDATE_BOOLEAN), 'show_footer');
        }
        if ($req->show_qr) {
            $data = Arr::prepend($data, filter_var($req->show_qr, FILTER_VALIDATE_BOOLEAN), 'show_qr');

            if (filter_var($req->show_qr, FILTER_VALIDATE_BOOLEAN)) {
                $data = Arr::prepend($data, $req->qr_text, 'qr_text');
                $data = Arr::prepend($data, $req->notes, 'notes');
            }
        }

        $res = DB::table('layanans')->where('id', $req->id)
                ->update($data);
        
        $response = [];
        if ($res) {
            $response = [
                'status' => true,
                'msg' => 'Success',
                'content' => DB::table('layanans')->where('id', $req->id)->first([ 'id', 'kode_layanan as code', 'nama_layanan as name', 'show_footer', 'show_qr', 'qr_text', 'notes'])
            ];
        }else {
            $response = [
                'status' => false,
                'msg' => 'fieled',
                'content' => ''
            ];
        }
        

        return response()->json(compact('response') , 200);
    }
}
