<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'section_id',
        'title',
        'description',
        'video_url',
        'duration',
        'order',
    ];

    /**
     * Get the section that owns the lesson.
     */
    public function section()
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }
} 