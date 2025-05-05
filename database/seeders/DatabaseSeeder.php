<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            UserSeeder::class,
            CourseSeeder::class,
            CourseVideoSeeder::class,
            DiscussionSeeder::class,
            DiscussionReplySeeder::class,
            EnrollmentSeeder::class,
            EventSeeder::class,
            EventRegistrationSeeder::class,
            JobOpportunitySeeder::class,
            JobApplicationSeeder::class,
            MentorshipProgramSeeder::class,
            MentorshipApplicationSeeder::class,
            MentorshipSessionSeeder::class,
        ]);
    }
}
