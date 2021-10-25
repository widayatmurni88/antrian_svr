<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Berita;
use App\Http\Controllers\Layanan;
use App\Http\Controllers\Loket;
use App\Http\Controllers\User;
use App\Http\Controllers\Antrian;
use App\Http\Controllers\Multimedia;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'multimedia'], function ($router){
    Route::get('/getVideos', [Multimedia::class, 'getVideo']);
    // admin area
    Route::get('/getMedia', [Multimedia::class, 'getMedia']);
    Route::post('/uploadFile', [Multimedia::class, 'uploadFile']);
    Route::delete('/delete/{id}', [Multimedia::class, 'deleteVideo']);
    Route::get('/setVisible/{id}', [Multimedia::class, 'setVisible']);
});

Route::group(['prefix' => 'news'], function ($router){
    Route::get('/getNews', [Berita::class, 'getNews']);
    
    // admin area
    Route::get('/getAllNews', [Berita::class, 'getAllNews']);
    Route::post('/saveNews', [Berita::class, 'saveNews']);
    Route::put('/updateNews', [Berita::class, 'updateNews']);
    Route::delete('/delete/{id}', [Berita::class, 'deleteNews']);
    Route::post('/setStatus', [Berita::class, 'setStatus']);
});

Route::group(['prefix' => 'layanan'], function ($router){
    Route::get('/getLayanan', [Layanan::class, 'getLayanan']);
    Route::get('/getLayananWithLastAntrian', [Layanan::class, 'getLayananWithLastAntrian']);
    Route::get('/getLayananName/{code}', [Layanan::class, 'getLayananName']);
    Route::get('/getIdLayanan/{code}', [Layanan::class, 'getIdLayanan']);
    // admin area
    Route::post('/addData', [Layanan::class, 'addData']);
    Route::put('/updateData', [Layanan::class, 'updateData']);
    Route::get('/removeLayanan/{id}',[Layanan::class, 'removeLayanan']);

    Route::get('/getLimiter', [Layanan::class, 'getLimiter']);
    Route::post('updateLimit', [Layanan::class, 'updateLimit']);
    
});

Route::group(['prefix' => 'loket'], function ($router){
    Route::get('/getLokets', [Loket::class, 'getLokets']);
    Route::get('/getLoket', [Loket::class, 'getLoket']);
    Route::get('/getLoketSingle/{id}', [Loket::class, 'getLoketSingle']);
    // admin area
    Route::get('/getAllLoket', [Loket::class, 'getAllLoket']);
    Route::post('/addLoket', [Loket::class, 'addLoket']);
    Route::get('/removeLoket/{id}', [Loket::class, 'removeLoket']);
    Route::put('/updateData', [Loket::class, 'updateData']);
});

Route::group(['prefix' => 'antrian'], function($router){
    Route::get('/getAllAntrianTerahir', [Antrian::class, 'getAllAntrianTerahir']);
    Route::get('/getAntrianTrakhirInLoket/{idLayanan}/{idLoket}', [Antrian::class, 'getAntrianTrakhirInLoket']);
    Route::get('/getLast/{idLayanan}', [Antrian::class, 'getNomorAntrian']);
    Route::post('/postGetNomorAntrian', [Antrian::class, 'postGetNomorAntrian']);
    Route::get('/cekPembatasan/{id}', [Antrian::class, 'cekPembatasan']);
    
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('getCallAntrian/{idLayanan}/{idLoket}', [Antrian::class, 'getCallAntrian']);
    });
});

Route::group(['prefix' => 'user'], function ($router){
    Route::post('/postCekLogin', [User::class, 'postCekLogin']);

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('/logout', [User::class, 'logout']);
        Route::get('/getProfile/{id}', [User::class, 'getProfile']);
    });

    // admin area
    Route::post('superLogin', [User::class, 'superLogin']);
    Route::get('/getUsers', [User::class, 'getUsers']);
    Route::post('/addUser', [User::class, 'addUser']);
    Route::post('/editUser', [User::class, 'editUser']);
    Route::post('/resetPassword', [User::class, 'resetPassword']);
    Route::delete('/removeUser/{id}', [User::class, 'removeUser']);
});