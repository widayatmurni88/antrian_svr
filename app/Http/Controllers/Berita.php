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
}
