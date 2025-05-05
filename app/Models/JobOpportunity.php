<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOpportunity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'company',
        'description',
        'requirements',
        'location',
        'salary_range',
        'job_type',
        'work_location_type',
        'experience_level',
        'application_url',
        'deadline',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deadline' => 'date',
        'status' => 'string',
        'job_type' => 'string',
        'work_location_type' => 'string',
        'experience_level' => 'string',
    ];

    /**
     * Get the applications for the job opportunity.
     */
    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'job_id');
    }

    /**
     * Get the applicants for the job opportunity.
     */
    public function applicants()
    {
        return $this->belongsToMany(User::class, 'job_applications', 'job_id', 'user_id')
            ->withPivot('status', 'resume_url', 'cover_letter')
            ->withTimestamps();
    }
} 