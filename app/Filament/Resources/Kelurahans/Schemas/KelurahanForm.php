<?php

namespace App\Filament\Resources\Kelurahans\Schemas;

use App\Models\Instansi;
use App\Models\Kecamatan;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class KelurahanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('kecamatan_id')
                ->label('Kecamatan')
                ->options(
                    Kecamatan::orderBy('nama_kecamatan')->pluck('nama_kecamatan', 'id')
                )
                ->searchable()
                ->preload()
                ->native(false)
                ->required()
                ->live()
                ->afterStateUpdated(fn (callable $set) => $set('instansi_id', null)),

            Forms\Components\TextInput::make('nama_kelurahan')
                ->label('Nama Kelurahan / Desa')
                ->required()
                ->maxLength(255),

            Forms\Components\Select::make('instansi_id')
                ->label('Puskesmas (Instansi)')
                ->options(function (Get $get) {
                    $kecamatanId = $get('kecamatan_id');
                    $query = Instansi::orderBy('nama_instansi');
                    if ($kecamatanId) {
                        // filter instansi yang berada di kecamatan ini jika ada relasi
                        // jika tidak ada filter, tampilkan semua
                    }
                    return $query->pluck('nama_instansi', 'id');
                })
                ->searchable()
                ->preload()
                ->native(false)
                ->nullable()
                ->helperText('Opsional — pilih Puskesmas yang menaungi kelurahan ini'),

            Forms\Components\Toggle::make('aktif')
                ->label('Status Aktif')
                ->default(true),
        ]);
    }
}
