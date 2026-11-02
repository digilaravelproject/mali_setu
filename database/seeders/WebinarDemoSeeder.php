<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Webinar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WebinarDemoSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('user_type', 'admin')->value('id');
        $groups = [
            'upcoming' => [
                ['Business Growth Strategies for Entrepreneurs', 'business', now()->addDays(3)->setTime(16, 0), 'Learn practical growth, marketing and networking strategies for modern businesses.'],
                ['Matrimony Meet & Greet', 'matrimony', now()->addDays(6)->setTime(18, 0), 'A guided community session for verified matrimony members and their families.'],
                ['Community Leadership Talk', 'other', now()->addDays(9)->setTime(17, 0), 'Explore community leadership, collaboration and meaningful social impact.'],
            ],
            'live' => [
                ['Digital Marketing Live Workshop', 'business', now()->subMinutes(15), 'A live workshop covering social media, local SEO and digital customer acquisition.'],
                ['Live Relationship Guidance Session', 'matrimony', now()->subMinutes(20), 'An interactive live conversation with an experienced relationship counsellor.'],
                ['Mali Setu Community Town Hall', 'other', now()->subMinutes(10), 'Join the live community town hall and share questions with the Mali Setu team.'],
            ],
            'completed' => [
                ['Investment Planning Webinar', 'business', now()->subDays(4)->setTime(16, 0), 'A completed session about financial discipline and investment planning for entrepreneurs.'],
                ['Healthy Family Relationships', 'matrimony', now()->subDays(7)->setTime(18, 0), 'A completed expert session focused on communication and healthy family relationships.'],
                ['Career & Education Guidance', 'other', now()->subDays(10)->setTime(11, 0), 'A completed guidance session for students, professionals and parents.'],
            ],
        ];

        foreach ($groups as $condition => $items) {
            foreach ($items as $index => [$title, $category, $startsAt, $description]) {
                $webinar = Webinar::updateOrCreate(
                    ['title' => $title],
                    [
                        'created_by' => $adminId,
                        'category' => $category,
                        'description' => $description,
                        'speaker_name' => ['Rahul Patil', 'Priya Deshmukh', 'Amit Shinde'][$index],
                        'agenda' => "Introduction\nExpert presentation\nQuestions and answers",
                        'starts_at' => $startsAt,
                        'duration_minutes' => $condition === 'live' ? 90 : 60,
                        'agora_channel' => 'webinar_'.Str::uuid(),
                        'agora_link' => 'pending',
                        'audience' => 'all',
                        'require_registration' => true,
                        'allow_qa' => true,
                        'allow_recording' => false,
                        'access_type' => 'free',
                        'plan_ids' => null,
                        'status' => 'published',
                    ]
                );
                $webinar->update(['agora_link' => route('webinars.join', $webinar)]);
            }
        }
    }
}
