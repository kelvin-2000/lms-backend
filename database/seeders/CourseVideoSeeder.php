<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseVideo;
use Illuminate\Database\Seeder;

class CourseVideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $webDevCourse = Course::where('title', 'Introduction to Web Development')->first();

        if ($webDevCourse) {
            $videos = [
                [
                    'title' => 'Introduction to the Course',
                    'description' => 'Overview of what you will learn in this course and how to get the most out of it.',
                    'video_url' => '/assets/videos/intro.mp4',
                    'duration' => 615, // 10:15 in seconds
                    'order' => 1,
                    'is_free' => true,
                ],
                [
                    'title' => 'HTML Basics - Document Structure',
                    'description' => 'Learn the fundamental structure of HTML documents and how to create your first webpage.',
                    'video_url' => '/assets/videos/html-basics.mp4',
                    'duration' => 930, // 15:30 in seconds
                    'order' => 2,
                    'is_free' => true,
                ],
                [
                    'title' => 'HTML Elements and Attributes',
                    'description' => 'Deep dive into HTML elements, their purposes, and how to use attributes effectively.',
                    'video_url' => '/assets/videos/html-elements.mp4',
                    'duration' => 1245, // 20:45 in seconds
                    'order' => 3,
                    'is_free' => false,
                ],
                [
                    'title' => 'Introduction to CSS',
                    'description' => 'Learn the basics of CSS and how to style your web pages.',
                    'video_url' => '/assets/videos/css-intro.mp4',
                    'duration' => 1100, // 18:20 in seconds
                    'order' => 4,
                    'is_free' => false,
                ],
                [
                    'title' => 'CSS Selectors and Properties',
                    'description' => 'Master CSS selectors and commonly used properties for styling elements.',
                    'video_url' => '/assets/videos/css-selectors.mp4',
                    'duration' => 1330, // 22:10 in seconds
                    'order' => 5,
                    'is_free' => false,
                ],
            ];

            foreach ($videos as $video) {
                CourseVideo::create([
                    'course_id' => $webDevCourse->id,
                    'title' => $video['title'],
                    'description' => $video['description'],
                    'video_url' => $video['video_url'],
                    'duration' => $video['duration'],
                    'order' => $video['order'],
                    'is_free' => $video['is_free'],
                ]);
            }
        }

        // Add some videos to other courses
        $courses = Course::where('id', '!=', $webDevCourse->id ?? 0)->get();

        foreach ($courses as $index => $course) {
            for ($i = 1; $i <= 3; $i++) {
                CourseVideo::create([
                    'course_id' => $course->id,
                    'title' => "Module $i: " . ($i === 1 ? 'Introduction' : ($i === 2 ? 'Core Concepts' : 'Advanced Techniques')),
                    'description' => "This video covers " . ($i === 1 ? 'the basics' : ($i === 2 ? 'essential concepts' : 'advanced techniques')) . " of " . $course->title,
                    'video_url' => "/assets/videos/course-{$course->id}-module-$i.mp4",
                    'duration' => 900 + $i * 300, // Between 15-30 minutes
                    'order' => $i,
                    'is_free' => $i === 1, // First video is free
                ]);
            }
        }
    }
} 