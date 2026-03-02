<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
// Notificaciones personalizadas
use App\Notifications\CustomVerifyEmail;
use App\Notifications\CustomResetPassword;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use HasProfilePhoto;
    use HasTeams;
    use TwoFactorAuthenticatable;

    /**
     * Atributos asignables masivamente.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',                  // ✅ AGREGADO
        'cod_rol',
        'foto',
        'email_verified_at',    // ✅ AGREGADO
    ];

    /**
     * Atributos ocultos.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * Atributos agregados.
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Casts.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Notificaciones personalizadas.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    /**
     * Relaciones PPS
     */
    public function estudiantesAsignados()
    {
        return $this->hasMany(SolicitudPPS::class, 'supervisor_id');
    }

public function supervisor()
{
    return $this->hasOne(Supervisor::class);
}

public function rolInstitucional()
{
    return $this->belongsTo(Rol::class, 'cod_rol', 'COD_ROL');
}

/**
 * Helpers basados en Spatie
 */
public function isAdmin(): bool
{
    return $this->hasRole('admin');
}



public function isEstudiante(): bool
{
    return $this->hasRole('estudiante');
}

/**
 * Helpers usando el rol institucional
 */

public function esEstudianteInstitucional(): bool
{
    return optional($this->rolInstitucional)->NOM_ROL === 'Estudiante';
}

public function esSupervisorInstitucional(): bool
{
    return $this->supervisor()->exists();
}

public function esAdminInstitucional(): bool
{
    return $this->supervisor()
        ->where('es_admin', 1)
        ->exists();
}
}