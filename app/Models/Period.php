<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    protected $fillable = ['name', 'is_active'];

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function metrics()
    {
        return $this->hasMany(Metric::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
