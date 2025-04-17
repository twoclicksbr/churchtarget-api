<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class RecPassword extends Model
{
    protected $table = 'rec_password';

    protected $fillable = [
        'id_credential',
        'id_person',
        'email',
        'token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public $timestamps = true;
}
