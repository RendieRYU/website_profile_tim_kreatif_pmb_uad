<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title', 'description', 'event_date', 'event_time', 'external_link'];
    protected function casts(): array { return ['event_date' => 'date', 'event_time' => 'datetime']; }

    public function members() { return $this->belongsToMany(Member::class)->withPivot('division_id')->withTimestamps(); }
}
