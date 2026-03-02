<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Supervisor;

class CrearSupervisorAdmin extends Command
{
    protected $signature = 'admin:make-supervisor {email}';
    protected $description = 'Convierte un admin en supervisor';

    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->where('rol', 'admin')->first();
        
        if (!$user) {
            $this->error("No se encontró un admin con el email: {$email}");
            return 1;
        }
        
        // Verificar si ya existe
        $supervisorExistente = Supervisor::where('user_id', $user->id)->first();
        
        if ($supervisorExistente) {
            $this->info("Este admin ya es supervisor.");
            return 0;
        }
        
        // Crear supervisor
        Supervisor::create([
            'user_id' => $user->id,
            'nombre' => $user->name,
            'email' => $user->email,
            'especialidad' => 'Administración',
            'max_estudiantes' => 20,
            'activo' => true,
            'es_admin' => true,  // 👈 MARCAMOS QUE ES ADMIN
        ]);
        
        $this->info("✅ Admin convertido en supervisor exitosamente.");
        return 0;
    }
}