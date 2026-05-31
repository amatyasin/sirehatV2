<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posyandus', function (Blueprint $table) {

            $table->id();

            $table->foreignId('instansi_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('nama_posyandu');

            $table->text('alamat')->nullable();

            $table->string('penanggung_jawab')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posyandus');
    }
};