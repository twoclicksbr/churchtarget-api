<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class MinistryPersonRegistered extends Model
{
    protected $tableName = 'ministry_person_registered';

    protected $fillable = [
        'id_credential',
        'id_ministry_cycle',
        'id_person',
    ];

    protected $appends = [
        'created_at_formatted',
        'updated_at_formatted',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = $this->tableName;

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

    // Relacionamentos
    public function credential()
    {
        return $this->belongsTo(Credential::class, 'id_credential');
    }

    public function ministryCycle()
    {
        return $this->belongsTo(MinistryCycle::class, 'id_ministry_cycle');
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'id_person');
    }
}
