<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['title', 'slug', 'banner_image', 'content', 'published_date'];
    protected function casts(): array { return ['published_date' => 'date']; }

    public function members() { return $this->belongsToMany(Member::class, 'news_member')->withTimestamps(); }
}
