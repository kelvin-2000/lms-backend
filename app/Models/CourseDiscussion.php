<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseDiscussion extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'title',
        'content',
        'reply_count',
    ];

    protected static function booted()
    {
        static::created(function ($discussion) {
            $discussion->update(['reply_count' => 0]);
        });

        static::deleted(function ($discussion) {
            $discussion->replies()->delete();
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(CourseDiscussionReply::class);
    }

    public function incrementReplyCount()
    {
        $this->increment('reply_count');
    }

    public function decrementReplyCount()
    {
        $this->decrement('reply_count');
    }
} 