<?php

// MODELO BASE - NAO UTILIZAR DIRETAMENTE
// Use este arquivo como referência para criar novas models

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// 🔁 ALTERAR O NOME DA CLASSE
class NomeModel extends Model
{   
    // 🔁 ALTERAR O NOME DA TABELA
    protected string $tableName = 'nome_da_tabela';

    protected $fillable = [
        'id_credential',
        'name',
        'active',
        // 🔁 ADICIONAR OUTROS CAMPOS SE NECESSÁRIO
    ];

    protected $hidden = [
        'id_credential',
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

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at?->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at?->format('Y-m-d H:i:s');
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst(strtolower($value));
    }
}
