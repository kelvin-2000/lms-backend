<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create instructors
        $instructors = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/instructors/john-doe.jpg',
                'bio' => 'John has been working as a web developer for over 10 years and has helped thousands of students learn web development. He specializes in frontend technologies and loves making complex concepts easy to understand.',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/instructors/jane-smith.jpg',
                'bio' => 'Jane is a React expert with 8 years of experience building complex web applications. She loves teaching and has authored multiple online courses.'
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike.johnson@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/instructors/mike-johnson.jpg',
                'bio' => 'Mike is a backend developer specializing in Laravel. He has built numerous APIs and web applications for startups and enterprises alike.'
            ],
            [
                'name' => 'Sarah Williams',
                'email' => 'sarah.williams@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/instructors/sarah-williams.jpg',
                'bio' => 'Sarah is a UI/UX designer with a passion for creating beautiful, user-friendly interfaces. She has worked with major brands to improve their digital presence.'
            ],
            [
                'name' => 'Robert Chen',
                'email' => 'robert.chen@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/instructors/robert-chen.jpg',
                'bio' => 'Robert is a database expert with extensive knowledge of SQL and NoSQL systems. He has designed database architectures for high-traffic applications.'
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/instructors/emily-davis.jpg',
                'bio' => 'Emily specializes in mobile development, particularly Flutter and React Native. She has published several apps on both iOS and Android platforms.'
            ],
            [
                'name' => 'Alex Thompson',
                'email' => 'alex.thompson@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/instructors/alex-thompson.jpg',
                'bio' => 'Alex is an iOS developer with extensive experience in Swift and native app development. He has published multiple successful apps on the App Store.'
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/instructors/maria-garcia.jpg',
                'bio' => 'Maria is an Android developer specializing in Kotlin and modern Android development practices. She has worked with major tech companies.'
            ],
            [
                'name' => 'David Kim',
                'email' => 'david.kim@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/instructors/david-kim.jpg',
                'bio' => 'David is a React Native expert with experience in building cross-platform mobile applications for various industries.'
            ],
            [
                'name' => 'Lisa Chen',
                'email' => 'lisa.chen@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/instructors/lisa-chen.jpg',
                'bio' => 'Lisa is a UI/UX designer specializing in Figma and design systems. She has created design systems for major tech companies.'
            ],
            [
                'name' => 'James Wilson',
                'email' => 'james.wilson@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/instructors/james-wilson.jpg',
                'bio' => 'James is a UX researcher and strategist with experience in user-centered design and product strategy.'
            ],
            [
                'name' => 'Sophia Lee',
                'email' => 'sophia.lee@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/instructors/sophia-lee.jpg',
                'bio' => 'Sophia specializes in design systems and component libraries, helping teams create scalable and consistent design solutions.'
            ],
            [
                'name' => 'Kevin Patel',
                'email' => 'kevin.patel@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/instructors/kevin-patel.jpg',
                'bio' => 'Kevin is a database expert specializing in NoSQL databases, particularly MongoDB, with experience in high-performance applications.'
            ],
            [
                'name' => 'Rachel Brown',
                'email' => 'rachel.brown@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/instructors/rachel-brown.jpg',
                'bio' => 'Rachel is a PostgreSQL expert with extensive experience in database design and optimization for enterprise applications.'
            ],
            [
                'name' => 'Michael Zhang',
                'email' => 'michael.zhang@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/instructors/michael-zhang.jpg',
                'bio' => 'Michael specializes in database performance optimization and has helped numerous companies improve their database efficiency.'
            ],
            [
                'name' => 'Jennifer Adams',
                'email' => 'jennifer.adams@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/mentors/jennifer.jpg',
                'bio' => 'Jennifer is a senior frontend developer with over 10 years of experience. She specializes in React and modern JavaScript frameworks.'
            ],
            [
                'name' => 'Michael Johnson',
                'email' => 'michael.johnson@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/mentors/michael.jpg',
                'bio' => 'Michael is a full-stack developer and technical lead with expertise in scalable web applications and cloud infrastructure.'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => '/assets/mentors/sarah-johnson.jpg',
                'bio' => 'I\'m a Senior Frontend Engineer at Google with over 10 years of experience in building complex web applications. I specialize in React, TypeScript, and modern JavaScript frameworks. I\'m passionate about helping aspiring developers grow their skills and advance their careers.'
            ]
        ];

        foreach ($instructors as $instructor) {
            User::firstOrCreate(
                ['email' => $instructor['email']],
                $instructor
            );
        }

        // Create students
        $students = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.j@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'avatar' => '/assets/avatars/sarah.jpg',
                'bio' => 'Web development enthusiast looking to expand my skills.',
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'avatar' => '/assets/avatars/michael.jpg',
                'bio' => 'Software engineer transitioning from Java to web development.',
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david.wilson@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'avatar' => '/assets/users/david.jpg',
                'bio' => 'Computer science student interested in frontend technologies.',
            ],
            [
                'name' => 'Jennifer Lee',
                'email' => 'jennifer.lee@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'avatar' => '/assets/users/jennifer.jpg',
                'bio' => 'UX designer looking to enhance coding skills.',
            ],
            [
                'name' => 'Michael Smith',
                'email' => 'michael.smith@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'avatar' => '/assets/users/michael.jpg',
                'bio' => 'Self-taught programmer looking to formalize knowledge.',
            ]
        ];

        foreach ($students as $student) {
            User::firstOrCreate(
                ['email' => $student['email']],
                $student
            );
        }

        // Create admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'avatar' => '/assets/avatars/admin.jpg',
                'bio' => 'System administrator',
            ]
        );
    }
} 