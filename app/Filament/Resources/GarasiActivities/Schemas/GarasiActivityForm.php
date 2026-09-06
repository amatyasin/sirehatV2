<?php

namespace App\Filament\Resources\GarasiActivities\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class GarasiActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Kegiatan')
                    ->schema([
                        \Filament\Forms\Components\Select::make('posyandu_id')
                            ->label('Posyandu')
                            ->relationship('posyandu', 'nama_posyandu', function (\Illuminate\Database\Eloquent\Builder $query) {
                                $user = auth()->user();
                                if ($user->hasRole('petugas_posyandu')) {
                                    return $query->where('id', $user->posyandu_id);
                                }
                                if ($user->hasRole('admin_instansi')) {
                                    return $query->where('instansi_id', $user->instansi_id);
                                }
                                if ($user->hasRole('admin_kecamatan')) {
                                    return $query->whereHas('kelurahan', fn ($q) => $q->where('kecamatan_id', $user->kecamatan_id));
                                }
                                return $query;
                            })
                            ->getOptionLabelFromRecordUsing(function (\App\Models\Posyandu $record) {
                                $puskesmas = $record->instansi?->nama_instansi;
                                $kelurahan = $record->kelurahan?->nama_kelurahan;
                                if ($puskesmas && $kelurahan) {
                                    return "{$record->nama_posyandu} - {$puskesmas} (Kel. {$kelurahan})";
                                }
                                if ($puskesmas) {
                                    return "{$record->nama_posyandu} - {$puskesmas}";
                                }
                                if ($kelurahan) {
                                    return "{$record->nama_posyandu} - Kel. {$kelurahan}";
                                }
                                return $record->nama_posyandu;
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $posyandu = \App\Models\Posyandu::find($state);
                                    if ($posyandu) {
                                        $set('instansi_id', $posyandu->instansi_id);
                                    }
                                }
                            }),
                        \Filament\Forms\Components\Hidden::make('instansi_id'),
                        \Filament\Forms\Components\DatePicker::make('activity_date')
                            ->label('Tanggal Kegiatan')
                            ->default(now())
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('location')
                            ->label('Lokasi'),
                        \Filament\Forms\Components\Select::make('officer_id')
                            ->label('Petugas')
                            ->relationship('officer', 'name', function (\Illuminate\Database\Eloquent\Builder $query, callable $get) {
                                $user = auth()->user();
                                if ($user->hasRole('petugas_posyandu')) {
                                    return $query->where('id', $user->id);
                                }
                                
                                $posyanduId = $get('posyandu_id');
                                if ($posyanduId) {
                                    return $query->where(function ($q) use ($posyanduId) {
                                        $q->where('posyandu_id', $posyanduId)
                                          ->orWhereHas('roles', function ($r) {
                                              $r->whereIn('name', ['super_admin', 'admin_dinkes']);
                                          });
                                    });
                                }
                                
                                return $query->where('id', '<', 0); // Empty if no posyandu selected
                            })
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->user()->hasRole('petugas_posyandu') ? auth()->id() : null)
                            ->disabled(fn () => auth()->user()->hasRole('petugas_posyandu'))
                            ->dehydrated()
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('notes')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                        \Filament\Forms\Components\Select::make('status')
                            ->options([
                                'scheduled' => 'Terjadwal',
                                'ongoing' => 'Berlangsung',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->required()
                            ->default('scheduled'),
                    ])
                    ->columns(2),
            ]);
    }
}
