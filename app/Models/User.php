<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['name', 'username', 'password', 'role'];

    public function pekerjaans()
    {
        return $this->hasMany(Pekerjaan::class);
    }
}