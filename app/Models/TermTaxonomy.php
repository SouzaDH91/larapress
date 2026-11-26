<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermTaxonomy extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'term_taxonomies';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'term_id',
        'taxonomy',
        'parent_id',
        'count',
    ];

    protected function casts(): array
    {
        return [
            'count' => 'integer',
        ];
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id');
    }

    public function parent()
    {
        return $this->belongsTo(TermTaxonomy::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(TermTaxonomy::class, 'parent_id');
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_term', 'term_taxonomy_id', 'post_id');
    }
}
