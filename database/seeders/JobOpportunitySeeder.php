<?php

namespace Database\Seeders;

use App\Models\JobOpportunity;
use Illuminate\Database\Seeder;

class JobOpportunitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = [
            [
                'title' => 'Frontend Developer',
                'company' => 'TechCorp Inc.',
                'description' => 'We are looking for a skilled Frontend Developer to join our team. The ideal candidate will have strong experience with React, TypeScript, and modern frontend frameworks.',
                'requirements' => 'Bachelor\'s degree in Computer Science or related field, 3+ years of experience with React, Strong knowledge of HTML5, CSS3, and responsive design principles',
                'location' => 'New York, NY',
                'salary_range' => '$80,000 - $100,000',
                'job_type' => 'full-time',
                'work_location_type' => 'on-site',
                'experience_level' => 'mid-level',
                'application_url' => 'https://techcorp.example.com/careers',
                'deadline' => '2025-05-15',
                'status' => 'open',
            ],
            [
                'title' => 'Laravel Backend Developer',
                'company' => 'WebSolutions Co.',
                'description' => 'Join our team as a Laravel Backend Developer to build and maintain scalable web applications.',
                'requirements' => 'Strong knowledge of PHP and Laravel framework, Experience with RESTful APIs, Understanding of database design principles',
                'location' => 'Remote',
                'salary_range' => '$70,000 - $90,000',
                'job_type' => 'full-time',
                'work_location_type' => 'remote',
                'experience_level' => 'mid-level',
                'application_url' => 'https://websolutions.example.com/careers',
                'deadline' => '2025-05-20',
                'status' => 'open',
            ],
            [
                'title' => 'UI/UX Designer',
                'company' => 'Creative Studio',
                'description' => 'Creative Studio is looking for a talented UI/UX Designer to create stunning user interfaces for our clients.',
                'requirements' => 'Portfolio demonstrating UI/UX design skills, Proficiency with design tools like Figma or Adobe XD, Understanding of user-centered design principles',
                'location' => 'San Francisco, CA',
                'salary_range' => '$60,000 - $80,000',
                'job_type' => 'full-time',
                'work_location_type' => 'hybrid',
                'experience_level' => 'mid-level',
                'application_url' => 'https://creativestudio.example.com/jobs',
                'deadline' => '2025-05-10',
                'status' => 'open',
            ],
            [
                'title' => 'DevOps Engineer',
                'company' => 'Cloud Systems Ltd.',
                'description' => 'We need a DevOps Engineer to streamline our deployment processes and maintain our cloud infrastructure.',
                'requirements' => 'Experience with AWS or Azure, Knowledge of CI/CD pipelines, Proficiency with Docker and Kubernetes, Scripting skills in Python or Bash',
                'location' => 'Remote',
                'salary_range' => '$90,000 - $120,000',
                'job_type' => 'full-time',
                'work_location_type' => 'remote',
                'experience_level' => 'senior-level',
                'application_url' => 'https://cloudsystems.example.com/apply',
                'deadline' => '2025-05-30',
                'status' => 'open',
            ],
            [
                'title' => 'Web Development Intern',
                'company' => 'TechCorp Inc.',
                'description' => 'Learn and grow as a Web Development Intern at TechCorp, working on real projects with our development team.',
                'requirements' => 'Currently pursuing a degree in Computer Science or related field, Basic knowledge of HTML, CSS, and JavaScript, Eagerness to learn and collaborate',
                'location' => 'New York, NY',
                'salary_range' => '$20 - $25 per hour',
                'job_type' => 'internship',
                'work_location_type' => 'on-site',
                'experience_level' => 'entry-level',
                'application_url' => 'https://techcorp.example.com/internships',
                'deadline' => '2025-06-05',
                'status' => 'open',
            ],
            [
                'title' => 'Mobile App Developer',
                'company' => 'AppWorks',
                'description' => 'AppWorks is seeking a Mobile App Developer to create innovative mobile solutions for our clients.',
                'requirements' => 'Experience developing native iOS or Android apps, Knowledge of React Native or Flutter, Understanding of mobile UI/UX design principles',
                'location' => 'Austin, TX',
                'salary_range' => '$75,000 - $95,000',
                'job_type' => 'full-time',
                'work_location_type' => 'hybrid',
                'experience_level' => 'mid-level',
                'application_url' => 'https://appworks.example.com/careers',
                'deadline' => '2025-06-15',
                'status' => 'open',
            ],
            [
                'title' => 'Technical Writer',
                'company' => 'DocuTech',
                'description' => 'Join DocuTech as a Technical Writer to create clear, concise documentation for technical products.',
                'requirements' => 'Excellent writing and editing skills, Ability to explain complex technical concepts clearly, Experience with documentation tools',
                'location' => 'Remote',
                'salary_range' => '$40 - $50 per hour',
                'job_type' => 'contract',
                'work_location_type' => 'remote',
                'experience_level' => 'mid-level',
                'application_url' => 'https://docutech.example.com/openings',
                'deadline' => '2025-06-25',
                'status' => 'open',
            ],
            [
                'title' => 'Database Administrator',
                'company' => 'DataSystems Co.',
                'description' => 'DataSystems is looking for a Database Administrator to ensure the performance and security of our database systems.',
                'requirements' => 'Experience with SQL and database management, Knowledge of database security best practices, Ability to optimize database performance',
                'location' => 'Chicago, IL',
                'salary_range' => '$85,000 - $110,000',
                'job_type' => 'full-time',
                'work_location_type' => 'on-site',
                'experience_level' => 'senior-level',
                'application_url' => 'https://datasystems.example.com/jobs',
                'deadline' => '2025-07-10',
                'status' => 'open',
            ],
            [
                'title' => 'AI/ML Engineer',
                'company' => 'NextGen AI Solutions',
                'description' => 'Join our AI team to develop machine learning models and intelligent systems for various industries. You will work on cutting-edge projects using the latest AI technologies.',
                'requirements' => 'MS or PhD in Computer Science, Machine Learning, or related field, Experience with TensorFlow or PyTorch, Strong Python programming skills, Knowledge of NLP, computer vision, or reinforcement learning',
                'location' => 'Seattle, WA',
                'salary_range' => '$120,000 - $150,000',
                'job_type' => 'full-time',
                'work_location_type' => 'hybrid',
                'experience_level' => 'senior-level',
                'application_url' => 'https://nextgenai.example.com/careers',
                'deadline' => '2025-08-15',
                'status' => 'open',
            ],
        ];

        foreach ($jobs as $job) {
            JobOpportunity::create($job);
        }

        // Add detailed description to the Frontend Developer job
        $frontendJob = JobOpportunity::where('title', 'Frontend Developer')->first();
        if ($frontendJob) {
            $frontendJob->description = 'We are looking for a skilled Frontend Developer to join our team. The ideal candidate will have strong experience with React, TypeScript, and modern frontend frameworks. You will be responsible for developing user interfaces for our web applications, collaborating with backend developers, and ensuring high-quality, responsive design implementation.';
            $frontendJob->requirements = 'Bachelor\'s degree in Computer Science or related field (or equivalent practical experience), At least 3 years of experience with React and modern JavaScript, Strong knowledge of HTML5, CSS3, and responsive design principles, Experience with TypeScript and state management libraries (Redux, Context API), Familiarity with version control systems, particularly Git, Understanding of CI/CD pipelines and automated testing, Excellent problem-solving skills and attention to detail, Good communication skills and ability to work in a team environment';
            $frontendJob->save();
        }
    }
} 