<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = ['period_id', 'division_id', 'full_name', 'nickname', 'photo', 'role', 'linkedin', 'instagram', 'github'];

    public function period() { return $this->belongsTo(Period::class); }
    public function division() { return $this->belongsTo(Division::class); }
    public function events() { return $this->belongsToMany(Event::class)->withPivot('division_id')->withTimestamps(); }
    public function news() { return $this->belongsToMany(News::class, 'news_member')->withTimestamps(); }

    public function getTotalEventsAttribute() { return $this->events()->count(); }
    public function getTotalWorksAttribute() { return $this->news()->count(); }
    public function getSlugAttribute() { return \Illuminate\Support\Str::slug($this->full_name); }

    protected static function booted()
    {
        static::updating(function ($member) {
            if ($member->isDirty('photo') && $member->getOriginal('photo')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($member->getOriginal('photo'));
            }
        });

        static::deleted(function ($member) {
            if ($member->photo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($member->photo);
            }
        });
    }
}
