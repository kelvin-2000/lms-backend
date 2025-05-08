<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = Course::all();
        $students = User::where('role', 'student')->get();
        
        // Each student enrolls in 2-4 random courses
        foreach ($students as $student) {
            $courseCount = rand(2, 4);
            $randomCourses = $courses->random($courseCount);
            
            foreach ($randomCourses as $course) {
                // Generate random progress and status
                $progress = rand(0, 100);
                $status = 'active';
                $completedAt = null;
                
                if ($progress == 100) {
                    $status = 'completed';
                    $completedAt = now()->subDays(rand(1, 30));
                } elseif (rand(1, 10) == 1) { // 10% chance of cancelled
                    $status = 'cancelled';
                }
                
                Enrollment::create([
                    'user_id' => $student->id,
                    'course_id' => $course->id,
                    'status' => $status,
                    'progress' => $progress,
                    'completed_at' => $completedAt,
                ]);
            }
        }
        
        // Ensure specific enrollments for the Web Development course to match the mock data
        $webDevCourse = Course::where('title', 'Introduction to Web Development')->first();
        
        if ($webDevCourse) {
            for ($i = 1; $i < 10; $i++) {
                $fakeStudent = User::create([
                    'name' => "Student $i",
                    'email' => "student$i@example.com",
                    'password' => bcrypt('password'),
                    'role' => 'student',
                ]);
                
                Enrollment::create([
                    'user_id' => $fakeStudent->id,
                    'course_id' => $webDevCourse->id,
                    'status' => rand(0, 1) ? 'active' : 'completed',
                    'progress' => rand(0, 100),
                    'completed_at' => rand(0, 1) ? now()->subDays(rand(1, 60)) : null,
                ]);
            }
        }
    }
} 