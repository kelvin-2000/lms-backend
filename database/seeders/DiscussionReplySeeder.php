<?php

namespace Database\Seeders;

use App\Models\Discussion;
use App\Models\DiscussionReply;
use App\Models\User;
use Illuminate\Database\Seeder;

class DiscussionReplySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $discussions = Discussion::all();
        $instructors = User::where('role', 'instructor')->get();
        $students = User::where('role', 'student')->get();

        $replies = [
            // Instructor responses
            'Thanks for your question! To center elements vertically with flexbox, you can use "align-items: center" on the container. For horizontal centering, use "justify-content: center". Here\'s a simple example: .container { display: flex; align-items: center; justify-content: center; }',
            'Great question! In JavaScript, "var" has function scope, while "let" and "const" have block scope. "const" variables can\'t be reassigned, while "let" can. I recommend using "const" by default, and "let" only when you need to reassign values.',
            'I recommend using the browser\'s developer tools for debugging. The console and the Sources panel are particularly useful. You can set breakpoints, watch variables, and step through your code to identify issues.',
            'There are many great resources for this topic. I personally recommend [specific book/website] as it covers both fundamentals and advanced concepts in a clear, practical way.',
            'I\'d be happy to help with your issue. Could you share a bit more detail about what specific error you\'re encountering? Or perhaps share a code snippet of what you\'ve tried so far?',
            
            // Student responses
            'I had the same question! Thanks for the clear explanation, it helped me understand the concept better.',
            'To add to the previous response, I found this article really helpful: [link]. It has great visual examples that clarified the concept for me.',
            'I was struggling with this too and found that practicing with small examples helped me understand better. Maybe try creating a simple test project just to experiment with different approaches?',
            'I ran into similar issues and solved it by [specific solution]. Hope that helps!',
            'Thanks for asking this! I was wondering the same thing but was too shy to ask.',
        ];

        foreach ($discussions as $discussion) {
            // Determine number of replies (3-7 for the specific discussions, 1-3 for others)
            if (in_array($discussion->title, ['Question about CSS Flexbox', 'JavaScript Function Scope'])) {
                $replyCount = rand(3, 7);
            } else {
                $replyCount = rand(1, 3);
            }
            
            // Always have an instructor reply
            $instructor = $instructors->random();
            DiscussionReply::create([
                'discussion_id' => $discussion->id,
                'user_id' => $instructor->id,
                'content' => $replies[rand(0, 4)], // First 5 are instructor responses
            ]);
            
            // Add student replies
            for ($i = 1; $i < $replyCount; $i++) {
                $student = $students->random();
                // Avoid having the discussion creator reply to their own post
                while ($student->id === $discussion->user_id) {
                    $student = $students->random();
                }
                
                DiscussionReply::create([
                    'discussion_id' => $discussion->id,
                    'user_id' => $student->id,
                    'content' => $replies[rand(5, 9)], // Last 5 are student responses
                ]);
            }
        }
    }
} 