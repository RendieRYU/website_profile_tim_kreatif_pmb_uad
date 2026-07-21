<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['period_id', 'title', 'description', 'event_date', 'event_time', 'external_link', 'link', 'status'];
    protected function casts(): array { return ['event_date' => 'date', 'event_time' => 'datetime']; }

    protected static function booted()
    {
        static::saved(function ($event) {
            if ($event->link && str_contains($event->link, 'instagram.com')) {
                \App\Models\InstagramPost::updateOrCreate(
                    ['event_id' => $event->id],
                    [
                        'period_id' => $event->period_id,
                        'tanggal_posting' => $event->event_date,
                        'link_konten' => $event->link,
                    ]
                );
            } else {
                $post = \App\Models\InstagramPost::where('event_id', $event->id)->first();
                if ($post && $post->total_engagement == 0 && $post->reach == 0) {
                    $post->delete();
                }
            }

            if ($event->link && str_contains($event->link, 'tiktok.com')) {
                \App\Models\TiktokPost::updateOrCreate(
                    ['event_id' => $event->id],
                    [
                        'period_id' => $event->period_id,
                        'tanggal_posting' => $event->event_date,
                        'link_konten' => $event->link,
                    ]
                );
            } else {
                $ttPost = \App\Models\TiktokPost::where('event_id', $event->id)->first();
                if ($ttPost && $ttPost->total_engagement == 0 && $ttPost->reach == 0) {
                    $ttPost->delete();
                }
            }
        });

        static::deleted(function ($event) {
            $post = \App\Models\InstagramPost::where('event_id', $event->id)->first();
            if ($post) {
                if ($post->total_engagement == 0 && $post->reach == 0) {
                    $post->delete();
                } else {
                    $post->update(['event_id' => null]);
                }
            }

            $ttPost = \App\Models\TiktokPost::where('event_id', $event->id)->first();
            if ($ttPost) {
                if ($ttPost->total_engagement == 0 && $ttPost->reach == 0) {
                    $ttPost->delete();
                } else {
                    $ttPost->update(['event_id' => null]);
                }
            }
        });
    }

    public function period() { return $this->belongsTo(Period::class); }

    public function members() { return $this->belongsToMany(Member::class)->withPivot('division_id', 'role')->withTimestamps(); }

    public function categories() { return $this->belongsToMany(Category::class)->withTimestamps(); }
}
