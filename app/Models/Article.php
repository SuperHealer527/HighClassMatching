<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['author_id', 'title', 'slug', 'category', 'excerpt', 'body', 'cover_image_path', 'status', 'published_at'];
    protected $casts = ['published_at' => 'datetime'];

    public function author() { return $this->belongsTo(User::class, 'author_id'); }
    public function scopePublished($query) { return $query->where('status', 'published')->where('published_at', '<=', now()); }
    public function getRouteKeyName() { return 'slug'; }
}
