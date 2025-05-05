<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Discussion;
use App\Models\User;
use Illuminate\Database\Seeder;

class DiscussionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $webDevCourse = Course::where('title', 'Introduction to Web Development')->first();
        
        if ($webDevCourse) {
            $discussions = [
                [
                    'user_name' => 'Sarah Johnson',
                    'title' => 'Question about CSS Flexbox',
                    'content' => 'I\'m having trouble understanding how to center elements vertically with flexbox. Can someone please explain?',
                ],
                [
                    'user_name' => 'Michael Brown',
                    'title' => 'JavaScript Function Scope',
                    'content' => 'Can someone explain the difference between let, const, and var in JavaScript? I\'m confused about scope.',
                ],
            ];

            foreach ($discussions as $discussion) {
                $user = User::where('name', $discussion['user_name'])->where('role', 'student')->first();
                
                if ($user) {
                    Discussion::create([
                        'course_id' => $webDevCourse->id,
                        'user_id' => $user->id,
                        'title' => $discussion['title'],
                        'content' => $discussion['content'],
                    ]);
                }
            }
        }

        // Add some discussions to other courses
        $courses = Course::where('id', '!=', $webDevCourse->id ?? 0)->get();
        $students = User::where('role', 'student')->get();
        
        $discussionTopics = [
            'How to debug effectively?',
            'Best practices for folder structure',
            'Recommended resources for deeper learning',
            'Is there a cheat sheet available?',
            'Stuck on project implementation',
        ];
        
        $discussionContents = [
            'I\'m struggling with debugging. What tools or techniques do you recommend for identifying issues more efficiently?',
            'What\'s the recommended folder structure for medium to large-scale projects? Any best practices to follow?',
            'Are there any books or online resources you would recommend for getting a deeper understanding of the subject?',
            'Is there a cheat sheet available that summarizes the key concepts covered in this course? It would be really helpful for quick reference.',
            'I\'m stuck on implementing the project from Module 3. Specifically, I\'m having trouble with [specific issue]. Any guidance would be appreciated!',
        ];

        foreach ($courses as $course) {
            // Add 1-3 random discussions per course
            $discussionCount = rand(1, 3);
            
            for ($i = 0; $i < $discussionCount; $i++) {
                $topicIndex = array_rand($discussionTopics);
                $student = $students->random();
                
                Discussion::create([
                    'course_id' => $course->id,
                    'user_id' => $student->id,
                    'title' => $discussionTopics[$topicIndex],
                    'content' => $discussionContents[$topicIndex],
                ]);
            }
        }
    }
} 