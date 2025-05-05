<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MentorshipProgram extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'mentor_id',
        'duration',
        'capacity',
        'status',
        'category',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'capacity' => 'integer',
        'status' => 'string',
    ];

    /**
     * Get the mentor that owns the program.
     */
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /**
     * Get the applications for the program.
     */
    public function applications()
    {
        return $this->hasMany(MentorshipApplication::class, 'program_id');
    }

    /**
     * Get the sessions for the program.
     */
    public function sessions()
    {
        return $this->hasMany(MentorshipSession::class, 'program_id');
    }

    /**
     * Get the mentees for the program.
     */
    public function mentees()
    {
        return $this->belongsToMany(User::class, 'mentorship_applications', 'program_id', 'user_id')
            ->withPivot('status')
            ->withTimestamps()
            ->wherePivot('status', 'accepted');
    }
} 