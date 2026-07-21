<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InstagramPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'event_id',
        'tanggal_posting',
        'link_konten',
        'jenis_konten',
        'pillar',
        'reach',
        'impression_home',
        'impression_profile',
        'impression_explore',
        'impression_hashtag',
        'impression_other',
        'interaction_followers',
        'interaction_non_followers',
        'likes',
        'comments',
        'shares',
        'saves',
        'reposts',
        'new_followers',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_posting' => 'date',
            'reach' => 'integer',
            'impression_home' => 'integer',
            'impression_profile' => 'integer',
            'impression_explore' => 'integer',
            'impression_hashtag' => 'integer',
            'impression_other' => 'integer',
            'interaction_followers' => 'float',
            'interaction_non_followers' => 'float',
            'likes' => 'integer',
            'comments' => 'integer',
            'shares' => 'integer',
            'saves' => 'integer',
            'reposts' => 'integer',
            'new_followers' => 'integer',
        ];
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function getFollowersCount()
    {
        $date = Carbon::parse($this->tanggal_posting);
        
        // Cari metrik pengikut Instagram terbaru pada bulan postingan tersebut
        $metric = Metric::where('period_id', $this->period_id)
            ->where('type', 'instagram')
            ->whereYear('date', $date->year)
            ->whereMonth('date', $date->month)
            ->orderByDesc('date')
            ->first();

        if (!$metric) {
            // Fallback mencari metrik pengikut Instagram terakhir sebelum atau pada bulan postingan
            $metric = Metric::where('period_id', $this->period_id)
                ->where('type', 'instagram')
                ->where('date', '<=', $date->endOfMonth())
                ->orderByDesc('date')
                ->first();
        }

        return $metric ? $metric->value : 0;
    }

    public function getTotalEngagementAttribute()
    {
        return $this->likes + $this->comments + $this->shares + $this->saves + $this->reposts;
    }

    public function getErByPostAttribute()
    {
        $followers = $this->getFollowersCount();
        if ($followers <= 0) return 0.0;
        return round(($this->total_engagement / $followers) * 100, 2);
    }

    public function getErByReachAttribute()
    {
        if ($this->reach <= 0) return 0.0;
        return round(($this->total_engagement / $this->reach) * 100, 2);
    }
}
