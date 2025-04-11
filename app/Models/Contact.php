<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
use App\Models\TypeContact;

class Contact extends Model
{
    protected string $tableName = 'contact';

    protected $fillable = [
        'id_credential',
        'route',
        'id_parent',
        'id_type_contact',
        'value',
        'active',
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

    public function typeContact()
    {
        return $this->belongsTo(TypeContact::class, 'id_type_contact');
    }

    public function setValueAttribute($value)
    {
        // Checa se existe id_type_contact e o tipo de input é "number"
        if (isset($this->attributes['id_type_contact'])) {
            $type = TypeContact::find($this->attributes['id_type_contact']);
            if ($type && $type->input_type === 'number') {
                $this->attributes['value'] = preg_replace('/[^0-9]/', '', $value);
                return;
            }
        }

        // Para outros tipos, salva como veio
        $this->attributes['value'] = $value;
    }

    

}
