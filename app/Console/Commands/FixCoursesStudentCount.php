<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixCoursesStudentCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:fix-student-count {course_id? : Optional specific course ID to fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the students_count field for all courses or a specific course';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $courseId = $this->argument('course_id');
        
        if ($courseId) {
            $this->fixCourseStudentCount($courseId);
        } else {
            $this->fixAllCoursesStudentCount();
        }
        
        $this->info('Student count fix completed.');
    }
    
    /**
     * Fix student count for a specific course.
     */
    protected function fixCourseStudentCount($courseId)
    {
        $course = Course::find($courseId);
        
        if (!$course) {
            $this->error("Course with ID {$courseId} not found.");
            return;
        }
        
        $this->info("Fixing student count for course: {$course->title} (ID: {$course->id})");
        
        // Get actual active/completed enrollment count
        $actualCount = Enrollment::where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->count();
            
        // Store current count for reporting
        $previousCount = $course->students_count;
        
        // Update with direct SQL to avoid any model issues
        DB::table('courses')
            ->where('id', $course->id)
            ->update(['students_count' => $actualCount]);
            
        $this->info("Updated course student count from {$previousCount} to {$actualCount}");
    }
    
    /**
     * Fix student count for all courses.
     */
    protected function fixAllCoursesStudentCount()
    {
        $courses = Course::all();
        $this->info("Fixing student count for {$courses->count()} courses...");
        
        $fixedCount = 0;
        
        foreach ($courses as $course) {
            // Get actual active/completed enrollment count
            $actualCount = Enrollment::where('course_id', $course->id)
                ->whereIn('status', ['active', 'completed'])
                ->count();
                
            // Only update if different to avoid unnecessary updates
            if ($course->students_count != $actualCount) {
                // Store for reporting
                $previousCount = $course->students_count;
                
                // Update with direct SQL
                DB::table('courses')
                    ->where('id', $course->id)
                    ->update(['students_count' => $actualCount]);
                    
                $this->line("Course '{$course->title}' (ID: {$course->id}): count updated from {$previousCount} to {$actualCount}");
                $fixedCount++;
            }
        }
        
        $this->info("Fixed student count for {$fixedCount} courses.");
    }
}
