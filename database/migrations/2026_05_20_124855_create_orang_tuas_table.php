<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orang_tuas', function (Blueprint $table) {

            $table->id();

            $table->foreignId('instansi_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('posyandu_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('nik')
                ->nullable();

            $table->string('nama_lengkap');

            $table->date('tanggal_lahir')
                ->nullable();

            $table->string('no_wa')
                ->nullable();

            $table->text('alamat')
                ->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orang_tuas');
    }
};