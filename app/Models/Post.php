<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'author_id',
        'parent_id',
        'title',
        'slug',
        'excerpt',
        'content_json',
        'content_html',
        'status',
        'type',
        'password',
        'comment_status',
        'ping_status',
        'menu_order',
        'featured_media_id',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'content_json' => 'array',
            'published_at' => 'datetime',
            'menu_order' => 'integer',
        ];
    }

    public function getUrlAttribute(): string
    {
        return app(\App\Services\PermalinkService::class)->getUrl($this);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function parent()
    {
        return $this->belongsTo(Post::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Post::class, 'parent_id');
    }

    public function featuredMedia()
    {
        return $this->belongsTo(Media::class, 'featured_media_id');
    }

    public function meta()
    {
        return $this->hasMany(PostMeta::class, 'post_id');
    }

    public function revisions()
    {
        return $this->hasMany(PostRevision::class, 'post_id')->orderBy('created_at', 'desc');
    }

    public function termTaxonomies()
    {
        return $this->belongsToMany(TermTaxonomy::class, 'post_term', 'post_id', 'term_taxonomy_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'post_id');
    }
}
