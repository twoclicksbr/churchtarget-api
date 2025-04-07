<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credential extends Model
{
    protected $table = 'credential';

    protected $fillable = [
        'username',
        'token',
        'active',
    ];

    protected $hidden = [
        'token',
    ];

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}

