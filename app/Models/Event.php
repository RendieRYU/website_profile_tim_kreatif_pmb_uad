<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['period_id', 'title', 'description', 'event_date', 'event_time', 'external_link', 'link', 'status'];
    protected function casts(): array { return ['event_date' => 'date', 'event_time' => 'datetime']; }

    public function period() { return $this->belongsTo(Period::class); }

    public function members() { return $this->belongsToMany(Member::class)->withPivot('division_id')->withTimestamps(); }

    public function categories() { return $this->belongsToMany(Category::class)->withTimestamps(); }
}
