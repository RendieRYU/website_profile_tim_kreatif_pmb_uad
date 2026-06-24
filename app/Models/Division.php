<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = ['name', 'color_hex'];

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
