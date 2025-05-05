<?php

namespace Database\Seeders;

use App\Models\MentorshipProgram;
use App\Models\User;
use Illuminate\Database\Seeder;

class MentorshipProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'mentor_name' => 'Jennifer Adams',
                'title' => 'Frontend Development Career Guidance',
                'description' => 'Get personalized guidance on building a successful career in frontend development. Topics include portfolio building, interview preparation, and skill development.',
                'duration' => '3 months',
                'capacity' => 5,
                'status' => 'open',
            ],
            [
                'mentor_name' => 'Michael Johnson',
                'title' => 'Backend Development with Laravel',
                'description' => 'Enhance your Laravel skills with hands-on mentorship focused on best practices, architecture patterns, and advanced features.',
                'duration' => '4 months',
                'capacity' => 3,
                'status' => 'open',
            ],
            [
                'mentor_name' => 'Sarah Williams',
                'title' => 'UI/UX Design Principles',
                'description' => 'Learn the fundamentals of effective UI/UX design through real-world projects and feedback sessions.',
                'duration' => '2 months',
                'capacity' => 8,
                'status' => 'closed',
            ],
            [
                'mentor_name' => 'John Doe',
                'title' => 'Full-Stack Web Development',
                'description' => 'Comprehensive mentorship covering both frontend and backend development, with focus on building complete web applications.',
                'duration' => '6 months',
                'capacity' => 4,
                'status' => 'open',
            ],
            [
                'mentor_name' => 'Emily Davis',
                'title' => 'Mobile App Development',
                'description' => 'Guidance on building mobile applications with React Native or Flutter, including deployment and app store optimization.',
                'duration' => '4 months',
                'capacity' => 6,
                'status' => 'open',
            ],
            [
                'mentor_name' => 'Sarah Johnson',
                'title' => 'Frontend Development Career Acceleration',
                'description' => 'Accelerate your frontend development career with personalized mentorship from a Google Senior Frontend Engineer.',
                'duration' => '12 weeks',
                'capacity' => 5,
                'status' => 'open',
            ],
        ];

        foreach ($programs as $program) {
            $mentor = User::where('name', $program['mentor_name'])->where('role', 'instructor')->first();
            
            if ($mentor) {
                MentorshipProgram::create([
                    'mentor_id' => $mentor->id,
                    'title' => $program['title'],
                    'description' => $program['description'],
                    'duration' => $program['duration'],
                    'capacity' => $program['capacity'],
                    'status' => $program['status'],
                ]);
            }
        }

        // Add detailed description to the Frontend Development Career Acceleration program
        $frontendProgram = MentorshipProgram::where('title', 'Frontend Development Career Acceleration')->first();
        if ($frontendProgram) {
            $frontendProgram->description = 'This 12-week mentorship program is designed to help you master modern frontend development skills and advance your career. You\'ll receive personalized guidance, code reviews, career advice, and exclusive resources from a senior engineer with experience at top tech companies.

Whether you\'re looking to break into tech, level up your skills, or prepare for senior roles, this program will provide you with the knowledge, feedback, and support you need to achieve your goals.';
            $frontendProgram->save();
        }
    }
} 