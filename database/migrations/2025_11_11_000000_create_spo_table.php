<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpoTable extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up()
    {
        Schema::create('spo', function (Blueprint $table) {
            $table->id();
            $table->string('no_spo')->unique();
            $table->string('nama_spo');
            $table->unsignedBigInteger('pk_id')->nullable();
            $table->integer('row')->default(0)->comment('Urutan tampilan SPO');
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down()
    {
        Schema::dropIfExists('spo');
    }
}
