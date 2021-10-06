<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAntriansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('antrians', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->integer('nomor_antrian');
            $table->boolean('status_call')->default(false);
            $table->string('kode_booking_online')->nullable();
            $table->unsignedBigInteger('id_layanan');
            $table->foreign('id_layanan')->references('id')->on('layanans');
            $table->unsignedBigInteger('id_loket')->nullable();
            $table->foreign('id_loket')->references('id')->on('lokets');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->foreign('id_user')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('antrians');
    }
}
