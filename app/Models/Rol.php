<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'COD_ROL';
    public $timestamps = false;

    public function users()
    {
        return $this->hasMany(User::class, 'cod_rol');
    }
}
