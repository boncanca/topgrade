<?php

namespace Database\Seeders\Site;

use App\Models\Content;
use Illuminate\Database\Seeder;

class SeoSeeder extends Seeder
{
    public function run(): void
    {
        $metaDefaults = [
            'home' => [
                'title' => 'TopGrade London FC | Youth Football Club in London',
                'description' => 'TopGrade London FC provides structured youth football training, coaching, and match opportunities in London for young players aged 4–18.',
            ],
            'about' => [
                'title' => 'About TopGrade London FC | London Youth Football',
                'description' => 'Learn about TopGrade London FC, our grassroots Community Interest Company (CIC) values, dedicated coaching philosophy, and training facilities in Tottenham and Hackney.',
            ],
            'contact' => [
                'title' => 'Contact TopGrade London FC | London Youth Football',
                'description' => 'Contact TopGrade London FC coaching staff and management for training sessions, trial bookings, and youth squad enquiries in Tottenham and Hackney.',
            ],
            'privacy' => [
                'title' => 'Privacy Policy | TopGrade London FC',
                'description' => 'Privacy policy and data protection guidelines for TopGrade London FC under UK GDPR.',
            ],
            'terms' => [
                'title' => 'Terms & Conditions | TopGrade London FC',
                'description' => 'Terms and conditions for participation, trials, and sessions at TopGrade London FC.',
            ],
        ];

        foreach ($metaDefaults as $slug => $meta) {
            $content = Content::where('slug', $slug)->first();

            if ($content && ! $content->seo) {
                $content->seo()->create([
                    'title' => $meta['title'],
                    'description' => $meta['description'],
                    'canonical_url' => url('/'.($slug === 'home' ? '' : $slug)),
                ]);
            }
        }
    }
}
