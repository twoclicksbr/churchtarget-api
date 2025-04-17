<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $tableName = 'document';

    protected $fillable = [
        'id_credential',
        'id_person',
        'id_type_document',
        'value',
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

    public function person()
    {
        return $this->belongsTo(Person::class, 'id_person');
    }

    public function type()
    {
        return $this->belongsTo(TypeDocument::class, 'id_type_document');
    }

    public function setValueAttribute($value)
    {
        if (isset($this->attributes['id_type_document'])) {
            $type = TypeDocument::find($this->attributes['id_type_document']);
            if ($type && $type->input_type === 'number') {
                $this->attributes['value'] = preg_replace('/[^0-9]/', '', $value);
                return;
            }
        }
        $this->attributes['value'] = $value;
    }
}
