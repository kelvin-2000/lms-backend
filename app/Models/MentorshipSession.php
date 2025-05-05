<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorshipSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'program_id',
        'mentor_id',
        'mentee_id',
        'title',
        'description',
        'scheduled_at',
        'duration',
        'meeting_link',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration' => 'integer',
        'status' => 'string',
    ];

    /**
     * Get the program that owns the session.
     */
    public function program()
    {
        return $this->belongsTo(MentorshipProgram::class, 'program_id');
    }

    /**
     * Get the mentor for the session.
     */
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /**
     * Get the mentee for the session.
     */
    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }
} 