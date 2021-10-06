<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Antrian extends Model
{
    // use HasFactory;
    protected $table='antrians';
    protected $fillable = ['id', 'nomor_antrian', 'status_call','kode_booking_online', 'id_layanan', 'id_loket', 'id_user'];
}
