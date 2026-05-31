<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('children', function (Blueprint $table) {

            $table->id();

            $table->foreignId('instansi_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('posyandu_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('nama_anak');

            $table->string('nik')->nullable();

            $table->enum('jenis_kelamin', [
                'L',
                'P'
            ]);

            $table->date('tanggal_lahir')->nullable();

            $table->string('nama_orang_tua')
                ->nullable();

            $table->text('alamat')
                ->nullable();

            $table->boolean('aktif')
                ->default(true);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};