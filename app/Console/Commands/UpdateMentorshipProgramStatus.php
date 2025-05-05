<?php

namespace App\Console\Commands;

use App\Models\MentorshipProgram;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateMentorshipProgramStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mentorship:update-status {--create} {--status=open}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update mentorship program status or create new programs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $shouldCreate = $this->option('create');
        $status = $this->option('status');
        
        // First check if there are any programs
        $count = MentorshipProgram::count();
        
        if ($count === 0) {
            $this->info('No mentorship programs found.');
            
            if ($shouldCreate) {
                $this->createMentorshipPrograms();
                return;
            }
        } else {
            $this->info("Found $count mentorship programs.");
            $this->updateProgramStatus($status);
        }
    }
    
    /**
     * Update all mentorship program statuses.
     */
    private function updateProgramStatus($status)
    {
        $this->info("Updating all mentorship programs to status: $status");
        
        $updated = DB::table('mentorship_programs')
            ->update(['status' => $status]);
            
        $this->info("Updated $updated mentorship programs.");
    }
    
    /**
     * Create sample mentorship programs.
     */
    private function createMentorshipPrograms()
    {
        $this->info('Creating sample mentorship programs...');
        
        // Find mentors (instructors or admins)
        $mentors = User::whereIn('role', ['instructor', 'admin'])
            ->limit(3)
            ->get();
            
        if ($mentors->isEmpty()) {
            $this->error('No mentors found. Please create users with instructor or admin role first.');
            return;
        }
        
        $categories = ['technology', 'business', 'career', 'academic'];
        $statuses = ['open', 'closed', 'completed'];
        $durations = ['12 weeks', '6 months', '3 months', '8 weeks'];
        
        $programsData = [
            [
                'title' => 'Web Development Career Path',
                'description' => 'A comprehensive mentorship program focused on web development career growth.',
                'category' => 'technology',
                'duration' => '6 months',
                'capacity' => 10,
                'status' => 'open'
            ],
            [
                'title' => 'Data Science Fundamentals',
                'description' => 'Learn the fundamentals of data science with personalized guidance.',
                'category' => 'technology',
                'duration' => '3 months',
                'capacity' => 5,
                'status' => 'open'
            ],
            [
                'title' => 'Business Leadership',
                'description' => 'Develop your leadership skills for business management positions.',
                'category' => 'business',
                'duration' => '12 weeks',
                'capacity' => 8,
                'status' => 'open'
            ],
            [
                'title' => 'Career Transition to Tech',
                'description' => 'Get guidance on transitioning your career to the tech industry.',
                'category' => 'career',
                'duration' => '8 weeks',
                'capacity' => 15,
                'status' => 'open'
            ],
            [
                'title' => 'Academic Research Skills',
                'description' => 'Improve your academic research and paper writing skills.',
                'category' => 'academic',
                'duration' => '12 weeks',
                'capacity' => 6,
                'status' => 'open'
            ]
        ];
        
        $created = 0;
        
        foreach ($programsData as $data) {
            // Assign a random mentor
            $mentor = $mentors->random();
            
            MentorshipProgram::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'mentor_id' => $mentor->id,
                'duration' => $data['duration'],
                'capacity' => $data['capacity'],
                'status' => $data['status'],
                'category' => $data['category']
            ]);
            
            $created++;
        }
        
        $this->info("Created $created mentorship programs.");
    }
}
