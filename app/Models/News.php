<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['title', 'slug', 'banner_image', 'content', 'published_date'];
    protected function casts(): array { return ['published_date' => 'date']; }

    public function members() { return $this->belongsToMany(Member::class, 'news_member')->withTimestamps(); }

    protected static function booted()
    {
        static::updating(function ($news) {
            if ($news->isDirty('banner_image') && $news->getOriginal('banner_image')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($news->getOriginal('banner_image'));
            }
        });

        static::deleted(function ($news) {
            if ($news->banner_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($news->banner_image);
            }
        });
    }
}
