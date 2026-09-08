<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pekerjaan extends Model
{
    protected $fillable = ['user_id', 'daftar_pekerjaan', 'total_pekerjaan', 'file_name', 'tanggal'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}