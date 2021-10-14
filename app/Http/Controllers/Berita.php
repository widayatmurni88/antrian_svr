<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\berita as News;

class Berita extends Controller
{
    public function getNews(){
        $news=[];
        $query = News::where('tampilkan', '=', true)->get(['isi_berita'])->toArray();
        foreach ($query as $item) {
            array_push($news, $item['isi_berita']);
        }
        return response()->json(compact('news'));
    }

    public function getAllNews(){
        $news = News::get(['id', 'isi_berita as isi', 'tampilkan as status']);
        return response()->json(compact('news'), 200);
    }

    public function saveNews(Request $req){
        $validate = \Validator::make($req->all(), [
            'isi_berita' => 'required'
        ]);
        if ($validate->fails()) {
            $response = [
                'status' => false,
                'msg' => 'Berita harus diisi',
                'content' => ''
            ];
            return response()->json(compact('response'), 200);
        }else{
            $news = new News();
            $news->tampilkan = $req->status;
            $news->isi_berita = $req->isi_berita;
            $news->save();
            $response = [
                'status' => true,
                'msg' => 'Success',
                'content' => $news
            ];
            return response()->json(compact('response'), 200);
        }
    }
}
