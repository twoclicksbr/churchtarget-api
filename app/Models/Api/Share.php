<?php

namespace App\Models\api;

use Illuminate\Database\Eloquent\Model;

class Share extends Model
{
    protected string $tableName = 'share';

    protected $fillable = [
        'id_credential',
        'id_type_share',
        'id_type_gender',
        'id_type_participation',
        'id_person_leader',
        'link',
        'active',
    ];

    protected $hidden = [];

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

    // 🔗 Relacionamentos
    public function typeShare()
    {
        return $this->belongsTo(TypeShare::class, 'id_type_share');
    }

    public function typeGender()
    {
        return $this->belongsTo(TypeGender::class, 'id_type_gender');
    }

    public function typeParticipation()
    {
        return $this->belongsTo(TypeParticipation::class, 'id_type_participation');
    }

    public function leader()
    {
        return $this->belongsTo(Person::class, 'id_person_leader');
    }
}
