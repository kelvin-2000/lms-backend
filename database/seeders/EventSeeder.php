<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Default thumbnail path for events
     */
    private const DEFAULT_EVENT_THUMBNAIL = '/assets/events/event.jpg';
    
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'title' => 'Web Development Trends 2023',
                'description' => 'Join us for an insightful webinar on the latest trends in web development. Learn about new frameworks, tools, and best practices.',
                'start_date' => '2025-12-15 18:00:00',
                'end_date' => '2025-12-15 20:00:00',
                'location' => 'Online',
                'thumbnail' => self::DEFAULT_EVENT_THUMBNAIL,
                'capacity' => 500,
                'type' => 'webinar',
                'status' => 'upcoming',
            ],
            [
                'title' => 'Hands-on React Workshop',
                'description' => 'A practical workshop where you will build a complete React application from scratch. Perfect for intermediate developers looking to level up.',
                'start_date' => '2025-12-20 09:00:00',
                'end_date' => '2025-12-21 17:00:00',
                'location' => 'Tech Hub, San Francisco',
                'thumbnail' => self::DEFAULT_EVENT_THUMBNAIL,
                'capacity' => 50,
                'type' => 'workshop',
                'status' => 'upcoming',
            ],
            [
                'title' => 'Laravel Conference 2023',
                'description' => 'The biggest Laravel event of the year featuring speakers from the core team and community. Networking opportunities and hands-on sessions.',
                'start_date' => '2025-06-10 08:00:00',
                'end_date' => '2025-06-12 18:00:00',
                'location' => 'Convention Center, New York',
                'thumbnail' => self::DEFAULT_EVENT_THUMBNAIL,
                'capacity' => 1000,
                'type' => 'conference',
                'status' => 'upcoming',
            ],
            [
                'title' => 'UI/UX Design Masterclass',
                'description' => 'Learn the principles of effective UI/UX design and how to create user-centered interfaces that convert and engage.',
                'start_date' => '2025-07-25 14:00:00',
                'end_date' => '2024-07-25 17:00:00',
                'location' => 'Online',
                'thumbnail' => self::DEFAULT_EVENT_THUMBNAIL,
                'capacity' => 300,
                'type' => 'webinar',
                'status' => 'upcoming',
            ],
            [
                'title' => 'Mobile App Development Bootcamp',
                'description' => 'An intensive bootcamp focused on mobile app development using React Native. Learn to build cross-platform apps that work on both iOS and Android.',
                'start_date' => '2025-08-15 09:00:00',
                'end_date' => '2025-08-19 17:00:00',
                'location' => 'Tech Campus, Austin',
                'thumbnail' => self::DEFAULT_EVENT_THUMBNAIL,
                'capacity' => 75,
                'type' => 'bootcamp',
                'status' => 'upcoming',
            ],
            [
                'title' => 'Database Optimization Summit',
                'description' => 'Join database experts as they share advanced techniques for optimizing database performance, scaling, and security in modern applications.',
                'start_date' => '2025-09-22 10:00:00',
                'end_date' => '2025-09-23 16:00:00',
                'location' => 'Online',
                'thumbnail' => self::DEFAULT_EVENT_THUMBNAIL,
                'capacity' => 400,
                'type' => 'conference',
                'status' => 'upcoming',
            ],
        ];

        foreach ($events as $event) {
            Event::create($event);
        }

        // Add detailed info to the Web Development Trends event
        $webDevEvent = Event::where('title', 'Web Development Trends 2023')->first();
        if ($webDevEvent) {
            $webDevEvent->description = 'In this webinar, our expert speakers will discuss the most important web development trends of 2023. Learn about new frameworks, tools, and best practices that are shaping the industry. Whether you\'re a beginner or an experienced developer, you\'ll gain valuable insights that you can apply to your projects right away. We\'ll also have a Q&A session where you can ask our speakers your most pressing questions.';
            $webDevEvent->save();
        }
    }
} 