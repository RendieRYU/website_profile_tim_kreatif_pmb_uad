<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'folder_id',
        'title',
        'content',
        'color',
        'is_pinned',
        'is_archived',
        'order',
        'note_date'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_archived' => 'boolean',
        'note_date' => 'date:Y-m-d'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(NoteFolder::class, 'folder_id');
    }
}
