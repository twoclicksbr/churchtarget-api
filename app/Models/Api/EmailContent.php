<?php

namespace App\Models\Api;

use Illuminate\Database\Eloquent\Model;

class EmailContent extends Model
{
    protected string $tableName = 'email_content';

    protected $fillable = [
        'id_credential',
        'id_type_email',
        'subject',
        'banner_url',
        'body',
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

    public function typeEmail()
    {
        return $this->belongsTo(TypeEmail::class, 'id_type_email');
    }

    public function credential()
    {
        return $this->belongsTo(Credential::class, 'id_credential');
    }
}
