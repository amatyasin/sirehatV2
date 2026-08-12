<?php

namespace App\Filament\Resources\Kecamatans\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class KecamatanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\TextInput::make('nama_kecamatan')
                ->label('Nama Kecamatan')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
        ]);
    }
}
