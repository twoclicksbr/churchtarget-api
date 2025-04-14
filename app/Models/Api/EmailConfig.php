<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class EmailConfig extends Model
{
    protected $tableName = 'email_config';

    protected $fillable = [
        'id_credential',
        'id_ministry',
        'id_type_email_config',
        'banner_url',
        'events',
        'client_name',
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

    public function type()
    {
        return $this->belongsTo(TypeEmailConfig::class, 'id_type_email_config');
    }

    public function TypeEmailConfig()
    {
        return $this->belongsTo(Ministry::class, 'id_ministry');
    }
}
