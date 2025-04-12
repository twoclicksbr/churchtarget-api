<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;
use App\Models\ApiPerson;

// 🔁 ALTERAR O NOME DA CLASSE
class PersonUser extends Model
{   
    // 🔁 ALTERAR O NOME DA TABELA
    protected string $tableName = 'person_user';

    protected $fillable = [
        'id_credential',
        'id_person',
        'email',
        'password',
        'active',
    ];

    protected $hidden = [
        'id_credential',
        'password',
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

    // Relacionamento
    public function person()
    {
        return $this->belongsTo(Person::class, 'id_person');
    }
}
