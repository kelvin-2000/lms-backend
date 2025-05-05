<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseVideo extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'video_url',
        'duration',
        'order',
        'is_free',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'duration' => 'integer',
        'order' => 'integer',
        'is_free' => 'boolean',
    ];

    /**
     * Get the course that owns the video.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
} 