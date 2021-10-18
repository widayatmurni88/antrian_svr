<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Multimedia as Media;

class Multimedia extends Controller{
    public function getMedia(){
        $media=[];
        $medias = Media::get();
        foreach ($medias as $item) {
            $itm = [
                'id' => $item->id,
                'title' => $item->title,
                'video_link' => asset('video/' . $item->filename),
                'thumb_link' => asset('thumbs/' . $item->thumbnail_link),
                'visible' => $item->visible
            ];
            $media = Arr::prepend($media, $itm);
        }
        return response()->json(compact('media'), 200);
    }

    public function uploadFile(Request $req){
        # code...
        $validate = \Validator::make($req->all(),[
            'video' => 'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts|required'
        ]);

        if ($validate->fails()) {
            $response = [
                'status'=> false,
                'msg'   => $validate->errors()
            ];
            return response()->json(compact('response'), 200);

        }else{
            $file = $req->video;
            $fileName = md5(date('YYYYMMDDHis')) . '.' . $file->getClientOriginalExtension();
            $file->move('video', $fileName);

            $media = new Media();
            $media->title = $file->getClientOriginalName();
            $media->filename = $fileName;
            $media->thumbnail_link; 
            $media->visible = true;
            $media->save();


            $response = [
                'status' => true,
                'msg' => 'Success',
                'content' => [
                    'id' => $media->id,
                    'title' => $media->title,
                    'filename' => asset('video/' . $media->filename),
                    'thumb_link' => asset('thumbs/' . $media->thumbnail_link),
                    'visible' => $media->visible
                ]
            ];
            return response()->json(compact('response'), 200);
        }

    }

    public function deleteVideo($id){
        $response=[];
        $vid = Media::find($id);
        if ($vid) {
            $vid->delete();
            $response = [
                'status' => true,
                'msg' => 'Success',
                'content' => $vid
            ];
        }else{
            $response = [
                'status' => false,
                'msg' => 'Not found'
            ]; 
        }

        return response()->json(compact('response'), 200);
    }

    public function setVisible($id){
        $response=[];
        $vid = Media::find($id);
        if ($vid) {
            $vid->visible = !$vid->visible;
            $vid = [
                'id' => $vid->id,
                'title' => $vid->title,
                'visible' => $vid->visible,
                'filename' => asset('video/' . $vid->filename),
                'thumb_link' => asset('thumbs/' . $vid->thumbnail_link)
            ];
            $response = [
                'status' => true,
                'msg' => 'Success',
                'content' => $vid
            ];
        }else{
            $response = [
                'status' => false,
                'msg' => 'Not found'
            ]; 
        }

        return response()->json(compact('response'), 200);
    }
}
