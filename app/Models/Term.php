<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'terms';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'media_id',
    ];

    public function taxonomies()
    {
        return $this->hasMany(TermTaxonomy::class, 'term_id');
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
}
