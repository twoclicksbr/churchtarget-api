<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class TypeContact extends Model
{
    protected string $tableName = 'type_contact';

    protected $fillable = [
        'id_credential',
        'name',
        'input_type',
        'mask',
        'active',
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
