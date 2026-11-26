<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plugin extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'plugins';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'slug',
        'name',
        'version',
        'is_active',
        'settings_json',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'settings_json' => 'array',
        ];
    }
}
