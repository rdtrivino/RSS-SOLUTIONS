<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Radicado extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero', 'modulo', 'user_id',
    ];

    public function radicable()
    {
        return $this->morphTo();
    }
}
