<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'incident_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the incident this comment belongs to.
     */
    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }

    /**
     * Get the user who made this comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
