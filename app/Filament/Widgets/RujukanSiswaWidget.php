<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Referral;

class RujukanSiswaWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 6;

    public static function canView(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return !$user->hasRole('petugas_posyandu');
    }

    protected function getTableQuery(): Builder
    {
        $user = auth()->user();

        return Referral::query()
            ->with(['student', 'school', 'studentClassHistory.schoolClass'])
            ->when(
                $user->hasRole('admin_kecamatan'),
                fn($q) => $q->whereHas('studentClassHistory.school', fn($sq) => $sq->where('kecamatan_id', $user->kecamatan_id))
            )
            ->when(
                $user->hasRole('admin_instansi') || $user->hasRole('petugas_pemeriksaan'),
                fn($q) => $q->whereHas('studentClassHistory.school', fn($sq) => $sq->where('instansi_id', $user->instansi_id))
            )
            ->when(
                $user->hasRole('admin_sekolah'),
                fn($q) => $q->whereHas('studentClassHistory', fn($sq) => $sq->where('school_id', $user->school_id))
            )
            ->latest('tanggal_pemeriksaan')
            ->limit(20);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('student.nama_lengkap')
                ->label('Nama Siswa')
                ->searchable()
                ->weight('bold'),

            Tables\Columns\TextColumn::make('school.nama_sekolah')
                ->label('Sekolah')
                ->searchable()
                ->limit(30),

            Tables\Columns\TextColumn::make('studentClassHistory.schoolClass.nama_kelas')
                ->label('Kelas'),

            Tables\Columns\TextColumn::make('jenis_pemeriksaan')
                ->label('Jenis Rujukan')
                ->badge()
                ->color(fn($state) => match($state) {
                    'Gizi' => 'danger',
                    'Gigi dan Mulut', 'Gigi' => 'warning',
                    'Mata' => 'info',
                    'Telinga' => 'gray',
                    'Umum' => 'success',
                    default => 'primary',
                }),

            Tables\Columns\TextColumn::make('alasan_rujukan')
                ->label('Alasan Rujukan')
                ->limit(50)
                ->tooltip(fn ($record) => $record->alasan_rujukan),

            Tables\Columns\TextColumn::make('status_rujukan')
                ->label('Status')
                ->badge()
                ->color(fn($state) => match($state) {
                    'Belum Dirujuk' => 'danger',
                    'Sudah Dirujuk' => 'warning',
                    'Dalam Tindak Lanjut' => 'info',
                    'Selesai' => 'success',
                    default => 'gray',
                }),

            Tables\Columns\TextColumn::make('tanggal_pemeriksaan')
                ->label('Tgl Pemeriksaan')
                ->date('d M Y'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('⚠️ Rujukan Siswa Terbaru (Perlu Tindak Lanjut)')
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->paginated([10, 20])
            ->striped();
    }
}
