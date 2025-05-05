<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Event::all();
        $users = User::where('role', '!=', 'superAdmin')->get();
        
        foreach ($events as $event) {
            // Register 30-70% of users for each event
            $registrationPercentage = rand(30, 70) / 100;
            $registrationCount = ceil($users->count() * $registrationPercentage);
            $registeredUsers = $users->random($registrationCount);
            
            foreach ($registeredUsers as $user) {
                // Determine status - most are registered, some have attended past events
                $status = 'registered';
                
                // If event is in the past, some users have attended
                if (strtotime($event->end_date) < time()) {
                    $status = rand(1, 10) <= 8 ? 'attended' : 'cancelled'; // 80% attended, 20% cancelled
                } elseif (rand(1, 10) == 1) { // 10% cancellation rate for upcoming events
                    $status = 'cancelled';
                }
                
                EventRegistration::create([
                    'event_id' => $event->id,
                    'user_id' => $user->id,
                    'status' => $status,
                ]);
            }
        }

        // For the detailed Web Development Trends event, ensure we have 312 attendees as in the mock data
        $webDevEvent = Event::where('title', 'Web Development Trends 2023')->first();
        if ($webDevEvent) {
            // Get current registration count
            $currentCount = EventRegistration::where('event_id', $webDevEvent->id)->count();
            
            // If we need more registrations to match the mock data
            $targetCount = 312;
            if ($currentCount < $targetCount) {
                $neededCount = $targetCount - $currentCount;
                
                // Create some fake users and register them
                for ($i = 0; $i < $neededCount; $i++) {
                    $fakeUser = User::create([
                        'name' => "Event Attendee $i",
                        'email' => "event.attendee$i@example.com",
                        'password' => bcrypt('password'),
                        'role' => 'student',
                    ]);
                    
                    EventRegistration::create([
                        'event_id' => $webDevEvent->id,
                        'user_id' => $fakeUser->id,
                        'status' => 'registered',
                    ]);
                }
            }
        }
    }
} 