<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'type',
        'date',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'value' => 'integer',
        ];
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
