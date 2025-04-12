<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class TypeDocument extends Model
{
    protected $tableName = 'type_document';
    protected $fillable = [
        'id_credential',
        'name',
        'mask',
        'input_type',
        'active',
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

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucfirst($value);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'id_type_document');
    }
}
