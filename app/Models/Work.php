<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    protected $fillable = ['title', 'work_type', 'work_date', 'link'];
    protected function casts(): array { return ['work_date' => 'date']; }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'member_work')->withTimestamps();
    }
}
