<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;


// 🔁 ALTERAR O NOME DA CLASSE
class TypeGender extends Model
{   
    // 🔁 ALTERAR O NOME DA TABELA
    protected string $tableName = 'type_gender';

    protected $fillable = [
        'id_credential',
        'name',
        'active',
        // 🔁 ADICIONAR OUTROS CAMPOS SE NECESSÁRIO
    ];

    protected $hidden = [
        // 'id_credential',
    ];

    protected $casts = [
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

    // Formata datas
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null;
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null;
    }

    // Padroniza o nome
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst(strtolower($value));
    }
}
