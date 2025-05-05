<?php

namespace Database\Seeders;

use App\Models\MentorshipApplication;
use App\Models\MentorshipProgram;
use App\Models\User;
use Illuminate\Database\Seeder;

class MentorshipApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = MentorshipProgram::where('status', 'open')->get();
        $students = User::where('role', 'student')->get();
        
        // Array of possible motivations
        $motivations = [
            "I'm passionate about learning %s and believe your mentorship would help me reach my career goals faster. I'm particularly interested in improving my skills in %s.",
            "I've been studying %s for the past few months and would love guidance from an industry expert like you. I'm eager to learn best practices and gain practical experience.",
            "Your experience in %s is exactly what I need to take my skills to the next level. I'm hoping to transition into a career in this field and would value your guidance.",
            "I admire your work in %s and would be honored to learn from you. I'm committed to putting in the effort required and believe I would benefit greatly from your mentorship.",
            "As someone new to %s, I'm looking for a mentor who can help me avoid common pitfalls and accelerate my learning. Your program seems perfect for my needs."
        ];
        
        // Keywords for different program types
        $keywords = [
            'Frontend' => ['React', 'JavaScript', 'UI development'],
            'Backend' => ['Laravel', 'API design', 'database optimization'],
            'UI/UX' => ['user research', 'design systems', 'prototyping'],
            'Full-Stack' => ['end-to-end development', 'system architecture', 'full application lifecycle'],
            'Mobile' => ['React Native', 'Flutter', 'mobile UX'],
        ];
        
        // Each program gets 2-5 applications
        foreach ($programs as $program) {
            // Determine keyword set based on program title
            $keywordSet = null;
            foreach ($keywords as $key => $values) {
                if (strpos($program->title, $key) !== false) {
                    $keywordSet = $values;
                    break;
                }
            }
            
            // Default to frontend if no match
            if (!$keywordSet) {
                $keywordSet = $keywords['Frontend'];
            }
            
            // Get random students for applications
            $applicantCount = min($program->capacity + rand(0, 2), $students->count());
            $applicants = $students->random($applicantCount);
            
            foreach ($applicants as $student) {
                // Get a random motivation template and fill it
                $motivationTemplate = $motivations[array_rand($motivations)];
                $programType = preg_match('/^(.*?)\s/', $program->title, $matches) ? $matches[1] : 'development';
                $keyword = $keywordSet[array_rand($keywordSet)];
                
                $motivation = sprintf($motivationTemplate, $programType, $keyword);
                
                // Determine status - weight towards 'applied' for open programs
                $statusWeights = ['applied' => 70, 'accepted' => 20, 'rejected' => 10];
                $statusOptions = [];
                
                foreach ($statusWeights as $status => $weight) {
                    for ($i = 0; $i < $weight; $i++) {
                        $statusOptions[] = $status;
                    }
                }
                
                $status = $statusOptions[array_rand($statusOptions)];
                
                // For the detailed Frontend Career Acceleration program, ensure we have 3 enrolled as in mock data
                if ($program->title === 'Frontend Development Career Acceleration' && $status === 'accepted') {
                    $acceptedCount = MentorshipApplication::where('program_id', $program->id)
                        ->where('status', 'accepted')
                        ->count();
                    
                    if ($acceptedCount >= 3) {
                        $status = 'applied';
                    }
                }
                
                MentorshipApplication::create([
                    'program_id' => $program->id,
                    'user_id' => $student->id,
                    'motivation' => $motivation,
                    'status' => $status,
                ]);
            }
        }

        // Add reviews for the Frontend Career Acceleration program
        $frontendProgram = MentorshipProgram::where('title', 'Frontend Development Career Acceleration')->first();
        if ($frontendProgram) {
            // Ensure we have exactly 3 accepted applications for this program
            $acceptedCount = MentorshipApplication::where('program_id', $frontendProgram->id)
                ->where('status', 'accepted')
                ->count();
            
            if ($acceptedCount < 3) {
                // Create more accepted applications if needed
                $neededApplications = 3 - $acceptedCount;
                $existingApplicantIds = MentorshipApplication::where('program_id', $frontendProgram->id)
                    ->pluck('user_id')
                    ->toArray();
                
                $availableStudents = $students->filter(function ($student) use ($existingApplicantIds) {
                    return !in_array($student->id, $existingApplicantIds);
                })->take($neededApplications);
                
                foreach ($availableStudents as $student) {
                    MentorshipApplication::create([
                        'program_id' => $frontendProgram->id,
                        'user_id' => $student->id,
                        'motivation' => sprintf($motivations[array_rand($motivations)], 'frontend development', 'React'),
                        'status' => 'accepted',
                    ]);
                }
            } elseif ($acceptedCount > 3) {
                // Update excess accepted applications to applied
                $acceptedApplications = MentorshipApplication::where('program_id', $frontendProgram->id)
                    ->where('status', 'accepted')
                    ->get();
                
                foreach ($acceptedApplications->skip(3) as $application) {
                    $application->status = 'applied';
                    $application->save();
                }
            }
        }
    }
} 