<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Berita;
use App\Http\Controllers\Layanan;
use App\Http\Controllers\Loket;
use App\Http\Controllers\User;
use App\Http\Controllers\Antrian;

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

Route::group(['prefix' => 'news'], function ($router){
    Route::get('/getNews', [Berita::class, 'getNews']);
    
    // admin area
    Route::get('/getAllNews', [Berita::class, 'getAllNews']);
    Route::post('/saveNews', [Berita::class, 'saveNews']);
});

Route::group(['prefix' => 'layanan'], function ($router){
    Route::get('/getLayanan', [Layanan::class, 'getLayanan']);
    Route::get('/getLayananName/{code}', [Layanan::class, 'getLayananName']);
    Route::get('/getIdLayanan/{code}', [layanan::class, 'getIdLayanan']);
});

Route::group(['prefix' => 'loket'], function ($router){
    Route::get('/getLoket', [Loket::class, 'getLoket']);
    Route::get('/getLoketSingle/{id}', [Loket::class, 'getLoketSingle']);
    // admin area
    Route::get('/getAllLoket', [Loket::class, 'getAllLoket']);
    Route::post('/addLoket', [Loket::class, 'addLoket']);
    Route::get('/removeLoket/{id}', [Loket::class, 'removeLoket']);
});

Route::group(['prefix' => 'antrian'], function($router){
    Route::get('getLast/{idLayanan}', [Antrian::class, 'getNomorAntrian']);
    
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
});