<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    /**
     * Default thumbnail path for courses
     */
    private const DEFAULT_THUMBNAIL = '/assets/courses/web.jpg';
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, create instructors if they don't exist
        $instructors = [
            'Jane Smith',
            'David Kim',
            'Mike Johnson',
            'Sarah Williams',
            'Robert Chen',
            'Emily Davis',
            'Alex Thompson',
            'Lisa Chen',
            'Michael Zhang',
        ];

        foreach ($instructors as $instructorName) {
            User::firstOrCreate(
                ['name' => $instructorName],
                [
                    'email' => Str::slug($instructorName) . '@example.com',
                    'password' => bcrypt('password'),
                    'role' => 'instructor'
                ]
            );
        }

        // Now seed the courses
        $courseData = [
            [
                'title' => 'Introduction to Web Development',
                'description' => 'Learn the fundamentals of web development with HTML, CSS, and JavaScript.',
                'long_description' => 'This comprehensive course will guide you through the core technologies of web development. Starting with HTML and CSS, you will learn how to structure and style web pages. Then, you will dive into JavaScript to make your pages interactive. By the end of this course, you will have built several real-world projects and gained a solid foundation in web development.',
                'instructor_name' => 'Jane Smith',
                'thumbnail' => self::DEFAULT_THUMBNAIL,
                'level' => 'beginner',
                'category' => 'web_development',
                'duration' => 8 * 7 * 24 * 60, // 8 weeks in minutes
                'price' => 0.00,
                'status' => 'published',
            ],
            [
                'title' => 'Advanced React Patterns',
                'description' => 'Master advanced React patterns and techniques for building complex, scalable applications.',
                'long_description' => 'Dive deep into React patterns and best practices. Learn about component composition, state management, performance optimization, and testing strategies. This course covers advanced topics like hooks, context API, and custom hooks. You will build several complex applications to solidify your understanding.',
                'instructor_name' => 'David Kim',
                'thumbnail' => self::DEFAULT_THUMBNAIL,
                'level' => 'intermediate',
                'category' => 'web_development',
                'duration' => 6 * 7 * 24 * 60, // 6 weeks in minutes
                'price' => 0.00,
                'status' => 'published',
            ],
            [
                'title' => 'Laravel API Development',
                'description' => 'Learn how to build robust and scalable APIs with Laravel.',
                'long_description' => 'Master the art of building RESTful APIs with Laravel. This course covers authentication, validation, error handling, and API documentation. You will learn about middleware, resource controllers, and API testing. By the end, you will be able to build production-ready APIs.',
                'instructor_name' => 'Mike Johnson',
                'thumbnail' => self::DEFAULT_THUMBNAIL,
                'level' => 'advanced',
                'category' => 'web_development',
                'duration' => 10 * 7 * 24 * 60, // 10 weeks in minutes
                'price' => 0.00,
                'status' => 'published',
            ],
            [
                'title' => 'UI/UX Design Fundamentals',
                'description' => 'Learn the principles of effective UI/UX design and how to create user-centered interfaces.',
                'long_description' => 'This course covers the fundamentals of UI/UX design, including user research, wireframing, prototyping, and usability testing. You will learn about design principles, color theory, typography, and accessibility. By the end, you will be able to create user-centered designs that solve real problems.',
                'instructor_name' => 'Sarah Williams',
                'thumbnail' => self::DEFAULT_THUMBNAIL,
                'level' => 'beginner',
                'category' => 'design',
                'duration' => 5 * 7 * 24 * 60, // 5 weeks in minutes
                'price' => 0.00,
                'status' => 'published',
            ],
            [
                'title' => 'Database Design & MySQL',
                'description' => 'Master database design concepts and MySQL for building efficient database systems.',
                'long_description' => 'Learn the fundamentals of database design and MySQL. This course covers normalization, indexing, transactions, and stored procedures. You will learn how to design efficient database schemas and write optimized queries. By the end, you will be able to build robust database systems.',
                'instructor_name' => 'Robert Chen',
                'thumbnail' => self::DEFAULT_THUMBNAIL,
                'level' => 'intermediate',
                'category' => 'database',
                'duration' => 7 * 7 * 24 * 60, // 7 weeks in minutes
                'price' => 0.00,
                'status' => 'published',
            ],
            [
                'title' => 'Mobile App Development with Flutter',
                'description' => 'Build cross-platform mobile apps with Flutter and Dart.',
                'long_description' => 'Master Flutter and Dart to build beautiful, natively compiled applications for mobile, web, and desktop from a single codebase. This course covers widgets, state management, navigation, and platform-specific features. You will build several apps to practice your skills.',
                'instructor_name' => 'Emily Davis',
                'thumbnail' => self::DEFAULT_THUMBNAIL,
                'level' => 'intermediate',
                'category' => 'mobile_development',
                'duration' => 9 * 7 * 24 * 60, // 9 weeks in minutes
                'price' => 0.00,
                'status' => 'published',
            ],
            // New Mobile Development Courses
            [
                'title' => 'iOS App Development with Swift',
                'description' => 'Learn to build native iOS applications using Swift and Xcode.',
                'long_description' => 'Dive into iOS development with Swift. This course covers UIKit, SwiftUI, Core Data, and networking. You will learn about app architecture, memory management, and debugging. By the end, you will be able to build and publish iOS apps to the App Store.',
                'instructor_name' => 'Alex Thompson',
                'thumbnail' => self::DEFAULT_THUMBNAIL,
                'level' => 'intermediate',
                'category' => 'mobile_development',
                'duration' => 8 * 7 * 24 * 60, // 8 weeks in minutes
                'price' => 0.00,
                'status' => 'published',
            ],
            [
                'title' => 'Advanced UI Design with Figma',
                'description' => 'Master UI design using Figma, from wireframes to high-fidelity prototypes.',
                'long_description' => 'Learn advanced UI design with Figma. This course covers components, styles, auto-layout, and prototyping. You will learn about design systems and collaboration. By the end, you will be able to create professional UI designs and prototypes.',
                'instructor_name' => 'Lisa Chen',
                'thumbnail' => self::DEFAULT_THUMBNAIL,
                'level' => 'intermediate',
                'category' => 'design',
                'duration' => 6 * 7 * 24 * 60, // 6 weeks in minutes
                'price' => 0.00,
                'status' => 'published',
            ],
            [
                'title' => 'Database Performance Optimization',
                'description' => 'Learn techniques for optimizing database performance and query efficiency.',
                'long_description' => 'Master database performance optimization. This course covers query optimization, indexing, and caching. You will learn about monitoring, profiling, and scaling. By the end, you will be able to optimize database performance.',
                'instructor_name' => 'Michael Zhang',
                'thumbnail' => self::DEFAULT_THUMBNAIL,
                'level' => 'advanced',
                'category' => 'database',
                'duration' => 7 * 7 * 24 * 60, // 7 weeks in minutes
                'price' => 0.00,
                'status' => 'published',
            ],
        ];

        foreach ($courseData as $course) {
            $instructor = User::where('name', $course['instructor_name'])->where('role', 'instructor')->first();
            
            if ($instructor) {
                Course::updateOrCreate(
                    ['title' => $course['title']],
                    [
                        'instructor_id' => $instructor->id,
                        'slug' => Str::slug($course['title']),
                        'description' => $course['description'],
                        'long_description' => $course['long_description'],
                        'thumbnail' => $course['thumbnail'],
                        'duration' => $course['duration'],
                        'level' => $course['level'],
                        'category' => $course['category'],
                        'price' => $course['price'],
                        'status' => $course['status'],
                    ]
                );
            }
        }

        // Add the detailed course
        $detailedCourse = Course::where('title', 'Introduction to Web Development')->first();
        if ($detailedCourse) {
            $detailedCourse->description = 'This comprehensive course will guide you through the core technologies of web development. Starting with HTML and CSS, you will learn how to structure and style web pages. Then, you will dive into JavaScript to make your pages interactive. By the end of this course, you will have built several real-world projects and gained a solid foundation in web development.';
            $detailedCourse->save();
        }
    }
} 