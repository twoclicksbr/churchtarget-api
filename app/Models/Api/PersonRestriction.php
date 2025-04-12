<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class PersonRestriction extends Model
{
    protected string $tableName = 'person_restriction';

    protected $fillable = [
        'id_credential',
        'id_person',
        'id_type_user',
    ];

    protected $appends = [
        'created_at_formatted',
        'updated_at_formatted',
    ];

    protected $casts = [];

    protected $hidden = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = $this->tableName;

        // Esconde o campo id_credential exceto se for credential = 1
        if (session('id_credential') !== 1) {
            $this->hidden[] = 'id_credential';
        }
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at?->format('Y-m-d H:i:s');
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at?->format('Y-m-d H:i:s');
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'id_person');
    }

    public function typeUser()
    {
        return $this->belongsTo(TypeUser::class, 'id_type_user');
    }
}
