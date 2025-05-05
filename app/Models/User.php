<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'title',
        'avatar',
        'bio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => 'string',
    ];
    
    /**
     * Get the courses that belong to the user as an instructor.
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }
    
    /**
     * Get the enrollments for the user.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
    
    /**
     * Get the discussions created by the user.
     */
    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }
    
    /**
     * Get the discussion replies created by the user.
     */
    public function discussionReplies()
    {
        return $this->hasMany(DiscussionReply::class);
    }
    
    /**
     * Get the event registrations for the user.
     */
    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }
    
    /**
     * Get the job applications for the user.
     */
    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }
    
    /**
     * Get the mentorship programs where the user is a mentor.
     */
    public function mentorshipPrograms()
    {
        return $this->hasMany(MentorshipProgram::class, 'mentor_id');
    }
    
    /**
     * Get the mentorship applications for the user.
     */
    public function mentorshipApplications()
    {
        return $this->hasMany(MentorshipApplication::class);
    }
    
    /**
     * Get the mentorship sessions where the user is a mentor.
     */
    public function mentorSessions()
    {
        return $this->hasMany(MentorshipSession::class, 'mentor_id');
    }
    
    /**
     * Get the mentorship sessions where the user is a mentee.
     */
    public function menteeSessions()
    {
        return $this->hasMany(MentorshipSession::class, 'mentee_id');
    }
    
    /**
     * Check if the user is an instructor or admin.
     */
    public function isInstructorOrAdmin()
    {
        return in_array($this->role, ['instructor', 'admin', 'superAdmin']);
    }
    
    /**
     * Check if the user is an admin.
     */
    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'superAdmin']);
    }
}
