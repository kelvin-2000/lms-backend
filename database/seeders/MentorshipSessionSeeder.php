<?php

namespace Database\Seeders;

use App\Models\MentorshipApplication;
use App\Models\MentorshipProgram;
use App\Models\MentorshipSession;
use Illuminate\Database\Seeder;

class MentorshipSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // For each program with accepted applications, create sessions
        $programs = MentorshipProgram::whereHas('applications', function ($query) {
            $query->where('status', 'accepted');
        })->get();
        
        foreach ($programs as $program) {
            // Get accepted applications (mentees)
            $acceptedApplications = MentorshipApplication::where('program_id', $program->id)
                ->where('status', 'accepted')
                ->with('user')
                ->get();
            
            // Create multiple sessions for each mentee
            foreach ($acceptedApplications as $application) {
                // Create 3-6 sessions per mentee
                $sessionCount = rand(3, 6);
                
                for ($i = 1; $i <= $sessionCount; $i++) {
                    // Session dates range from 2 weeks ago to 2 months in the future
                    $weeksOffset = rand(-2, 8);
                    $scheduledAt = now()->addWeeks($weeksOffset)->setHour(rand(9, 17))->setMinute(0)->setSecond(0);
                    
                    // Determine session status
                    $status = 'scheduled';
                    $notes = null;
                    
                    if ($weeksOffset < 0) {
                        // Past sessions
                        $status = rand(1, 10) <= 9 ? 'completed' : 'cancelled'; // 90% completed, 10% cancelled
                        
                        if ($status === 'completed') {
                            $notes = $this->generateSessionNotes($i, $program->title);
                        } else {
                            $notes = "Session cancelled due to " . (rand(0, 1) ? "scheduling conflict" : "unforeseen circumstances");
                        }
                    }
                    
                    MentorshipSession::create([
                        'program_id' => $program->id,
                        'mentor_id' => $program->mentor_id,
                        'mentee_id' => $application->user_id,
                        'title' => $this->generateSessionTitle($i, $program->title),
                        'description' => $this->generateSessionDescription($i, $program->title),
                        'scheduled_at' => $scheduledAt,
                        'duration' => rand(1, 2) * 30, // 30 or 60 minutes
                        'meeting_link' => 'https://meet.example.com/' . strtolower(str_replace(' ', '-', $program->title)) . '-session-' . $i,
                        'status' => $status,
                        'notes' => $notes,
                    ]);
                }
            }
        }
    }
    
    /**
     * Generate a session title based on session number and program title
     */
    private function generateSessionTitle($sessionNumber, $programTitle)
    {
        $frontendTitles = [
            "Introduction and Goal Setting",
            "Frontend Fundamentals Review",
            "Advanced React Patterns",
            "State Management Deep Dive",
            "Performance Optimization Techniques",
            "Career Planning and Next Steps",
        ];
        
        $backendTitles = [
            "Project Structure and Setup",
            "Database Design and Optimization",
            "API Architecture Review",
            "Authentication and Security",
            "Testing and CI/CD Integration",
            "Deployment and Scalability",
        ];
        
        $defaultTitles = [
            "Initial Assessment and Planning",
            "Core Concepts Review",
            "Building Your First Project",
            "Advanced Techniques and Patterns",
            "Troubleshooting and Optimization",
            "Career Development and Next Steps",
        ];
        
        // Select the appropriate title set based on program title
        if (strpos(strtolower($programTitle), 'frontend') !== false) {
            $titles = $frontendTitles;
        } elseif (strpos(strtolower($programTitle), 'backend') !== false) {
            $titles = $backendTitles;
        } else {
            $titles = $defaultTitles;
        }
        
        // Use the corresponding title or fallback to session number
        return $sessionNumber <= count($titles) 
            ? $titles[$sessionNumber - 1] 
            : "Session $sessionNumber: Follow-up and Progress Review";
    }
    
    /**
     * Generate a session description based on session number and program title
     */
    private function generateSessionDescription($sessionNumber, $programTitle)
    {
        $frontendDescriptions = [
            "We'll discuss your current experience, set goals for the mentorship, and create a personalized learning plan.",
            "Review of key frontend concepts, identifying knowledge gaps, and setting up your development environment.",
            "Exploring advanced React patterns including hooks, context, and component composition.",
            "Deep dive into state management options including Redux, Context API, and other alternatives.",
            "Techniques for optimizing frontend performance including code splitting, memoization, and rendering optimizations.",
            "Review of progress, portfolio refinement, and creating a roadmap for continued growth.",
        ];
        
        $backendDescriptions = [
            "Setting up your project structure and discussing architectural best practices.",
            "Designing efficient database schemas and optimizing database queries.",
            "Building robust and well-documented APIs with proper error handling and validation.",
            "Implementing secure authentication and authorization systems.",
            "Setting up automated testing and continuous integration/deployment workflows.",
            "Strategies for deploying and scaling your applications in production environments.",
        ];
        
        $defaultDescriptions = [
            "Assessing your current skill level and creating a personalized plan to achieve your goals.",
            "Reviewing fundamental concepts to ensure a solid foundation before moving to advanced topics.",
            "Hands-on project development with real-time feedback and guidance.",
            "Exploring advanced patterns and techniques in your field of study.",
            "Identifying and fixing performance issues and bottlenecks in your projects.",
            "Career advice, portfolio review, and planning your next steps.",
        ];
        
        // Select the appropriate description set based on program title
        if (strpos(strtolower($programTitle), 'frontend') !== false) {
            $descriptions = $frontendDescriptions;
        } elseif (strpos(strtolower($programTitle), 'backend') !== false) {
            $descriptions = $backendDescriptions;
        } else {
            $descriptions = $defaultDescriptions;
        }
        
        // Use the corresponding description or fallback to a generic one
        return $sessionNumber <= count($descriptions) 
            ? $descriptions[$sessionNumber - 1] 
            : "Follow-up session to review progress, address questions, and plan next steps.";
    }
    
    /**
     * Generate session notes based on session number and program title
     */
    private function generateSessionNotes($sessionNumber, $programTitle)
    {
        $notes = [
            "The mentee demonstrated a good understanding of the concepts discussed. We identified several areas for improvement, particularly in {AREA}. Homework assigned: {HOMEWORK}",
            "Great progress since our last session! The mentee completed all assigned tasks and showed significant improvement in {AREA}. Next steps: {NEXT_STEPS}",
            "We reviewed the mentee's work and identified some challenges with {AREA}. We worked through some examples together and developed strategies to overcome these issues.",
            "The mentee shared some interesting approaches to solving {AREA} problems. We discussed the pros and cons of different solutions and refined their implementation.",
            "Focus session on {AREA}. The mentee had specific questions about best practices, which we addressed with practical examples and code reviews."
        ];
        
        $areas = [
            'frontend' => ['component architecture', 'state management', 'performance optimization', 'responsive design', 'CSS organization'],
            'backend' => ['API design', 'database optimization', 'authentication', 'error handling', 'testing strategies'],
            'fullstack' => ['system architecture', 'frontend-backend integration', 'data flow', 'state synchronization', 'deployment pipelines'],
        ];
        
        $homework = [
            'frontend' => ['refactor the component structure', 'implement a custom hook', 'optimize rendering performance', 'add responsive layouts', 'implement form validation'],
            'backend' => ['improve API endpoint structure', 'optimize database queries', 'implement JWT authentication', 'add comprehensive error handling', 'write unit tests'],
            'fullstack' => ['implement end-to-end feature', 'refine data flow between frontend and backend', 'add real-time capabilities', 'implement caching strategy', 'set up CI/CD pipeline'],
        ];
        
        $nextSteps = [
            'frontend' => ['explore advanced React patterns', 'implement state management solution', 'optimize bundle size', 'add accessibility features', 'implement animation'],
            'backend' => ['add caching layer', 'implement rate limiting', 'set up monitoring', 'implement background jobs', 'optimize for scalability'],
            'fullstack' => ['implement authentication flow', 'add offline capabilities', 'refine API contract', 'implement real-time features', 'optimize performance'],
        ];
        
        // Determine category based on program title
        $category = 'frontend';
        if (strpos(strtolower($programTitle), 'backend') !== false) {
            $category = 'backend';
        } elseif (strpos(strtolower($programTitle), 'full-stack') !== false) {
            $category = 'fullstack';
        }
        
        // Select random note template
        $note = $notes[array_rand($notes)];
        
        // Fill in placeholders
        $note = str_replace('{AREA}', $areas[$category][array_rand($areas[$category])], $note);
        $note = str_replace('{HOMEWORK}', $homework[$category][array_rand($homework[$category])], $note);
        $note = str_replace('{NEXT_STEPS}', $nextSteps[$category][array_rand($nextSteps[$category])], $note);
        
        return $note;
    }
} 