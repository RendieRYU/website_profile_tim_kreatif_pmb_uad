<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AdmissionLeader extends Model
{
    protected $fillable = ['name', 'position', 'photo', 'order'];

    protected static function booted()
    {
        static::deleting(function ($leader) {
            if ($leader->photo) {
                Storage::disk('public')->delete($leader->photo);
            }
        });

        static::updating(function ($leader) {
            if ($leader->isDirty('photo') && $leader->getOriginal('photo')) {
                Storage::disk('public')->delete($leader->getOriginal('photo'));
            }
        });
    }
}
