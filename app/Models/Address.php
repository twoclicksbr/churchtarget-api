<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;
use App\Models\TypeAddress;

class Address extends Model
{
    protected string $tableName = 'address';

    protected $fillable = [
        'id_credential',
        'route',
        'id_parent',
        'id_type_address',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'localidade',
        'uf',
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

    // Relacionamento
    public function typeAddress()
    {
        return $this->belongsTo(TypeAddress::class, 'id_type_address');
    }

    public function setCepAttribute($value)
    {
        $this->attributes['cep'] = preg_replace('/[^0-9]/', '', $value);
    }
}
