<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $fillable = [

        'name',
        'email',
        'password',
        'instansi_id',
        'kecamatan_id',
        'school_id',
        'posyandu_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(
        Panel $panel
    ): bool {

        return true;

    }

    public function instansi()
    {
        return $this->belongsTo(
            Instansi::class
        );
    }

    public function school()
    {
        return $this->belongsTo(
            School::class
        );
    }

    public function posyandu()
    {
        return $this->belongsTo(
            Posyandu::class
        );
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(
            'super_admin'
        );
    }

    public function isAdminDinkes(): bool
    {
        return $this->hasRole(
            'admin_dinkes'
        );
    }

    public function kecamatan()
    {
        return $this->belongsTo(
            Kecamatan::class
        );
    }

    public function isAdminInstansi(): bool
    {
        return $this->hasRole(
            'admin_instansi'
        );
    }

    public function isPetugasPosyandu(): bool
    {
        return $this->hasRole(
            'petugas_posyandu'
        );
    }
}
