<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PostMeta extends Model
{
    use HasUuids;

    protected $table = 'post_meta';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'post_id',
        'meta_key',
        'meta_value',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
