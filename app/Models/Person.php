<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TypeGender;
use App\Models\TypeGroup;


// 🔁 ALTERAR O NOME DA CLASSE
class Person extends Model
{   
    // 🔁 ALTERAR O NOME DA TABELA
    protected string $tableName = 'person';

    protected $fillable = [
        'id_credential',
        'name',
        'birthdate',
        'id_type_gender',
        'id_type_group',
        'active',
        // 🔁 ADICIONAR OUTROS CAMPOS SE NECESSÁRIO
    ];

    protected $hidden = [
        'id_credential',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'active' => 'integer',
    ];

    protected $appends = [
        'created_at_formatted',
        'updated_at_formatted',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = $this->tableName;
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at?->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at?->format('Y-m-d H:i:s');
    }

    // public function setNameAttribute($value)
    // {
    //     // $this->attributes['name'] = ucfirst(strtolower($value));
    // }

    // Relacionamentos
    public function gender()
    {
        return $this->belongsTo(TypeGender::class, 'id_type_gender');
    }

    public function group()
    {
        return $this->belongsTo(TypeGroup::class, 'id_type_group');
    }
}
