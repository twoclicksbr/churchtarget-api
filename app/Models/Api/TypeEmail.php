<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class TypeEmail extends Model
{
    protected string $tableName = 'type_email';

    protected $fillable = [
        'id_credential',
        'name',
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

    public function credential()
    {
        return $this->belongsTo(Credential::class, 'id_credential');
    }
}
