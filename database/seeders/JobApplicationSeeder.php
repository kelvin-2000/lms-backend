<?php

namespace Database\Seeders;

use App\Models\JobApplication;
use App\Models\JobOpportunity;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = JobOpportunity::all();
        $students = User::where('role', 'student')->get();
        
        // Set of statuses with weighted distribution
        $statuses = [
            'applied' => 60,           // 60% chance
            'under review' => 20,      // 20% chance
            'interviewed' => 10,       // 10% chance
            'rejected' => 5,           // 5% chance
            'offered' => 5,            // 5% chance
        ];
        
        $statusOptions = [];
        foreach ($statuses as $status => $weight) {
            for ($i = 0; $i < $weight; $i++) {
                $statusOptions[] = $status;
            }
        }
        
        // Each student applies to 1-3 jobs
        foreach ($students as $student) {
            $jobCount = rand(1, 3);
            $randomJobs = $jobs->random($jobCount);
            
            foreach ($randomJobs as $job) {
                // Random status weighted by the distribution defined above
                $status = $statusOptions[array_rand($statusOptions)];
                
                JobApplication::create([
                    'job_id' => $job->id,
                    'user_id' => $student->id,
                    'resume_url' => "https://example.com/resumes/{$student->id}.pdf",
                    'cover_letter' => "Dear Hiring Manager,\n\nI am writing to express my interest in the {$job->title} position at {$job->company}. With my background in " . ($job->title == 'Frontend Developer' ? 'web development' : 'the relevant field') . ", I believe I would be a great fit for your team.\n\nSincerely,\n{$student->name}",
                    'status' => $status,
                ]);
            }
        }
    }
} 