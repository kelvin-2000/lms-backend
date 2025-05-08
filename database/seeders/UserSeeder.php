<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Default avatar path for instructors
     */
    private const DEFAULT_INSTRUCTOR_AVATAR = '/assets/instructors/avatar.jpg';
    private const DEFAULT_STUDENT_AVATAR = '/assets/students/avatar.jpg';
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Create instructors
        $instructors = [
            [
                'name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => self::DEFAULT_INSTRUCTOR_AVATAR,
                'bio' => 'Jane has been working as a web developer for over 10 years and has helped thousands of students learn web development. He specializes in frontend technologies and loves making complex concepts easy to understand.'
            ],
            [
                'name' => 'David Kim',
                'email' => 'david.kim@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => self::DEFAULT_INSTRUCTOR_AVATAR,
                'bio' => 'David is a React expert with 8 years of experience building complex web applications. He loves teaching and has authored multiple online courses.'
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike.johnson@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => self::DEFAULT_INSTRUCTOR_AVATAR,
                'bio' => 'Mike is a backend developer specializing in Laravel. He has built numerous APIs and web applications for startups and enterprises alike.'
            ],
            [
                'name' => 'Sarah Williams',
                'email' => 'sarah.williams@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => self::DEFAULT_INSTRUCTOR_AVATAR,
                'bio' => 'Sarah is a UI/UX designer with a passion for creating beautiful, user-friendly interfaces. She has worked with major brands to improve their digital presence.'
            ],
            [
                'name' => 'Robert Chen',
                'email' => 'robert.chen@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => self::DEFAULT_INSTRUCTOR_AVATAR,
                'bio' => 'Robert is a database expert with extensive knowledge of SQL and NoSQL systems. He has designed database architectures for high-traffic applications.'
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => self::DEFAULT_INSTRUCTOR_AVATAR,
                'bio' => 'Emily specializes in mobile development, particularly Flutter and React Native. She has published several apps on both iOS and Android platforms.'
            ],
            [
                'name' => 'Alex Thompson',
                'email' => 'alex.thompson@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => self::DEFAULT_INSTRUCTOR_AVATAR,
                'bio' => 'Alex is an iOS developer with extensive experience in Swift and native app development. He has published multiple successful apps on the App Store.'
            ],
            [
                'name' => 'Lisa Chen',
                'email' => 'lisa.chen@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => self::DEFAULT_INSTRUCTOR_AVATAR,
                'bio' => 'Lisa is a UI/UX designer specializing in Figma and design systems. She has created design systems for major tech companies.'
            ],
            [
                'name' => 'Michael Zhang',
                'email' => 'michael.zhang@example.com',
                'password' => Hash::make('password'),
                'role' => 'instructor',
                'avatar' => self::DEFAULT_INSTRUCTOR_AVATAR,
                'bio' => 'Michael specializes in database performance optimization and has helped numerous companies improve their database efficiency.'
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
                'avatar' => self::DEFAULT_STUDENT_AVATAR,
                'bio' => 'Web development enthusiast looking to expand my skills.',
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'avatar' => self::DEFAULT_STUDENT_AVATAR,
                'bio' => 'Software engineer transitioning from Java to web development.',
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david.wilson@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'avatar' => self::DEFAULT_STUDENT_AVATAR,
                'bio' => 'Computer science student interested in frontend technologies.',
            ],
            [
                'name' => 'Jennifer Lee',
                'email' => 'jennifer.lee@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'avatar' => self::DEFAULT_STUDENT_AVATAR,
                'bio' => 'UX designer looking to enhance coding skills.',
            ],
            [
                'name' => 'Michael Smith',
                'email' => 'michael.smith@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'avatar' => self::DEFAULT_STUDENT_AVATAR,
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
                'avatar' => '/assets/avatar.jpg',
                'bio' => 'System administrator',
            ]
        );
    }
} 