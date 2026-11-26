<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'menus';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'location_slug',
    ];

    public function items()
    {
        return $this->hasMany(MenuItem::class, 'menu_id')->orderBy('order', 'asc');
    }
}
