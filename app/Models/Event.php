<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['period_id', 'title', 'description', 'event_date', 'event_time', 'external_link', 'link', 'links', 'custom_fields', 'status'];
    protected function casts(): array { 
        return [
            'event_date' => 'date:Y-m-d', 
            'event_time' => 'datetime:H:i',
            'links' => 'array',
            'custom_fields' => 'array'
        ]; 
    }

    protected static function booted()
    {
        static::saved(function ($event) {
            $allUrls = [];
            if ($event->link) {
                $allUrls[] = $event->link;
            }
            if (is_array($event->links)) {
                foreach ($event->links as $l) {
                    if (is_array($l) && !empty($l['url'])) {
                        $allUrls[] = $l['url'];
                    } elseif (is_string($l)) {
                        $allUrls[] = $l;
                    }
                }
            }

            $igUrl = null;
            $ttUrl = null;
            foreach ($allUrls as $u) {
                if (!$igUrl && str_contains($u, 'instagram.com')) $igUrl = $u;
                if (!$ttUrl && str_contains($u, 'tiktok.com')) $ttUrl = $u;
            }

            if ($igUrl) {
                \App\Models\InstagramPost::updateOrCreate(
                    ['event_id' => $event->id],
                    [
                        'period_id' => $event->period_id,
                        'tanggal_posting' => $event->event_date,
                        'link_konten' => $igUrl,
                    ]
                );
            } else {
                $post = \App\Models\InstagramPost::where('event_id', $event->id)->first();
                if ($post && $post->total_engagement == 0 && $post->reach == 0) {
                    $post->delete();
                }
            }

            if ($ttUrl) {
                \App\Models\TiktokPost::updateOrCreate(
                    ['event_id' => $event->id],
                    [
                        'period_id' => $event->period_id,
                        'tanggal_posting' => $event->event_date,
                        'link_konten' => $ttUrl,
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
