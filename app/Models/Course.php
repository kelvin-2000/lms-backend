<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'instructor_id',
        'title',
        'slug',
        'description',
        'long_description',
        'thumbnail',
        'thumbnail_url',
        'duration',
        'level',
        'category',
        'price',
        'status',
        'total_videos',
        'rating',
        'rating_count',
        'students_count',
        'last_update',
        'requirements',
        'what_you_will_learn',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
        'level' => 'string',
        'category' => 'string',
        'status' => 'string',
    ];

    /**
     * Get the instructor that owns the course.
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the sections for the course.
     */
    public function sections()
    {
        return $this->hasMany(CourseSection::class);
    }

    /**
     * Get the videos for the course.
     */
    public function videos()
    {
        return $this->hasMany(CourseVideo::class);
    }

    /**
     * Get the discussions for the course.
     */
    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    /**
     * Get the enrollments for the course.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
} 