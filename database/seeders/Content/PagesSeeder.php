<?php

namespace Database\Seeders\Content;

use App\Models\Content;
use App\Models\ContentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PagesSeeder extends Seeder
{
    public function run(): void
    {
        $pageType = ContentType::where('slug', 'page')->first();

        if (! $pageType) {
            return;
        }

        // 1. Home Page
        $homePage = Content::firstOrCreate(
            ['slug' => 'home'],
            [
                'content_type_id' => $pageType->id,
                'title' => 'TopGrade London FC — Youth Football Club',
                'excerpt' => 'A youth football club in London helping young players develop through training, teamwork and playing experience.',
                'content' => 'TopGrade London FC provides structured youth football training and development for young players.',
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        // Seed Home ContentBlocks if missing
        if ($homePage->blocks()->count() === 0) {
            $homePage->blocks()->create([
                'uuid' => (string) Str::uuid(),
                'type' => 'hero',
                'sort_order' => 1,
                'payload' => [
                    'eyebrow' => 'MORE THAN FOOTBALL',
                    'headline' => 'DEVELOP YOUR FOOTBALL FUTURE',
                    'description' => 'A youth football club in London helping young players develop through training, teamwork and playing experience.',
                    'primary_cta' => [
                        'label' => 'Book a Trial',
                        'url' => '/bookings/free-trial-session',
                    ],
                    'stats' => [
                        ['label' => 'Age Groups', 'value' => 'Ages 4–18', 'detail' => 'Junior & youth squads'],
                        ['label' => 'Weekly Training', 'value' => '3 Days', 'detail' => 'Tuesdays, Wednesdays & Thursdays'],
                        ['label' => 'Home Grounds', 'value' => '3 Venues', 'detail' => 'Tottenham & Hackney'],
                        ['label' => 'Matchday', 'value' => 'League', 'detail' => 'Sanctioned youth fixtures'],
                    ],
                ],
            ]);

            $homePage->blocks()->create([
                'uuid' => (string) Str::uuid(),
                'type' => 'feature_list',
                'sort_order' => 2,
                'payload' => [
                    'title' => 'DEVELOP YOUR GAME',
                    'subtitle' => 'Our Core Pillars',
                    'items' => [
                        [
                            'num' => '01',
                            'title' => 'Technical Skill',
                            'description' => 'First touch, passing range, striking execution, and 1v1 attacking confidence.',
                        ],
                        [
                            'num' => '02',
                            'title' => 'Game Understanding',
                            'description' => 'Reading game situations, tactical positioning, and rapid decision-making under pressure.',
                        ],
                        [
                            'num' => '03',
                            'title' => 'Teamwork & Discipline',
                            'description' => 'Punctuality, structured preparation, pitch communication, and playing for the team.',
                        ],
                        [
                            'num' => '04',
                            'title' => 'Playing Experience',
                            'description' => 'Competitive minutes in sanctioned London youth leagues, not a season on the bench.',
                        ],
                    ],
                ],
            ]);
        }

        // Attach Home media assets if available
        $videoPath = public_path('topgrade-video.mp4');
        $videoMedia = $homePage->getMedia('videos')->first();
        if (! $videoMedia) {
            if (file_exists($videoPath)) {
                $homePage->addMedia($videoPath)->preservingOriginal()->toMediaCollection('videos');
            }
        } elseif (! file_exists($videoMedia->getPath()) && file_exists($videoPath)) {
            File::ensureDirectoryExists(dirname($videoMedia->getPath()));
            File::copy($videoPath, $videoMedia->getPath());
        }

        $posterPath = public_path('images/club/hero-football.jpg');
        $posterMedia = $homePage->getMedia('images')->first();
        if (! $posterMedia) {
            if (file_exists($posterPath)) {
                $homePage->addMedia($posterPath)->preservingOriginal()->toMediaCollection('images');
            }
        } elseif (! file_exists($posterMedia->getPath()) && file_exists($posterPath)) {
            File::ensureDirectoryExists(dirname($posterMedia->getPath()));
            File::copy($posterPath, $posterMedia->getPath());
        }

        // 2. About Page
        $aboutPage = Content::firstOrCreate(
            ['slug' => 'about'],
            [
                'content_type_id' => $pageType->id,
                'title' => 'About TopGrade London FC',
                'excerpt' => 'TopGrade London FC is a youth football club dedicated to helping young players develop through training, teamwork and playing experience.',
                'content' => 'TopGrade London FC is a grassroots Community Interest Company (CIC) delivering structured coaching, teamwork, and competitive playing pathways across Tottenham and Hackney.',
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        if ($aboutPage->blocks()->count() === 0) {
            $aboutPage->blocks()->create([
                'uuid' => (string) Str::uuid(),
                'type' => 'values',
                'sort_order' => 1,
                'payload' => [
                    'items' => [
                        [
                            'title' => 'Player-First Development',
                            'description' => 'Every drill, session plan, and coaching instruction focuses on technical proficiency and long-term player progression.',
                        ],
                        [
                            'title' => 'Disciplined Coaching Standards',
                            'description' => 'Led by Head Coach Richard Matey Opoku, our dedicated coaching staff focus on age-appropriate training, disciplined player habits, positive reinforcement, and tactical understanding.',
                        ],
                        [
                            'title' => 'London Community Roots',
                            'description' => 'Operating as a non-profit Community Interest Company (CIC) dedicated to structured youth football in North and East London.',
                        ],
                        [
                            'title' => 'Competitive League Matches',
                            'description' => 'Sanctioned London youth league participation, tournament showcases, and competitive weekend fixtures.',
                        ],
                    ],
                ],
            ]);
        }

        // 3. Contact Page
        Content::firstOrCreate(
            ['slug' => 'contact'],
            [
                'content_type_id' => $pageType->id,
                'title' => 'Contact TopGrade London FC',
                'excerpt' => 'Get in touch with TopGrade London FC for questions regarding club teams, trial bookings, and training sessions.',
                'content' => 'Contact our coaching and administrative staff via email or our online enquiry form.',
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        // 4. Privacy Policy
        Content::firstOrCreate(
            ['slug' => 'privacy'],
            [
                'content_type_id' => $pageType->id,
                'title' => 'Privacy Policy',
                'excerpt' => 'Privacy policy and data protection guidelines for TopGrade London FC under UK GDPR.',
                'content' => <<<'HTML'
<section class="space-y-3">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">1. Who We Are</h2>
    <p class="text-tg-text">TopGrade London FC operates as <strong class="text-tg-text-strong">TOPGRADE LONDON FC CIC</strong> (Company No. 14087076), a Community Interest Company registered in England and Wales. Our registered office is 30 Broadwater Road, London, England, N17 6ES.</p>
    <p class="text-tg-text">We act as the Data Controller responsible for personal data collected from parents, guardians, and youth players participating in our weekly football training sessions, introductory trials, and club fixtures across Tottenham and Hackney.</p>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">2. Information We Collect</h2>
    <p class="text-tg-text">We collect only the information necessary to safely organise and deliver football coaching and youth matches:</p>
    <ul class="list-disc pl-5 space-y-2 text-tg-text-muted text-sm">
        <li><strong class="text-tg-text">Parent / Guardian Details:</strong> Full name, email address, contact telephone number, and emergency contact details.</li>
        <li><strong class="text-tg-text">Player (Child) Details:</strong> First name, surname, approximate age / squad phase (e.g. U7–U8, U9–U10, U11–U12, U13–U16), and session attendance.</li>
        <li><strong class="text-tg-text">Safety &amp; Medical Notes:</strong> Relevant medical information (e.g. asthma, allergies) volunteered by parents solely to ensure player welfare during drills.</li>
        <li><strong class="text-tg-text">Enquiry Messages:</strong> Questions submitted through our website contact form.</li>
        <li><strong class="text-tg-text">Photography &amp; Media Preferences:</strong> Opt-in preferences regarding team celebration photos and matchday highlights.</li>
    </ul>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">3. Lawful Basis for Processing</h2>
    <p class="text-tg-text">Under UK GDPR, we process personal information under the following lawful bases:</p>
    <ul class="list-disc pl-5 space-y-2 text-tg-text-muted text-sm">
        <li><strong class="text-tg-text">Contractual Necessity:</strong> To process session bookings, manage squad training timetables, and confirm trial bookings.</li>
        <li><strong class="text-tg-text">Vital Interests:</strong> To ensure immediate medical attention can be provided if a player is injured or taken unwell during a session.</li>
        <li><strong class="text-tg-text">Legitimate Interests:</strong> To coordinate club scheduling, communicate pitch updates to parents, and maintain touchline safety.</li>
        <li><strong class="text-tg-text">Consent:</strong> For squad celebration photography and social media highlights. Consent may be updated or withdrawn at any time.</li>
    </ul>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">4. Children's Data &amp; Safeguarding</h2>
    <p class="text-tg-text">TopGrade London FC takes children's privacy and player welfare with utmost seriousness. We apply strict data minimisation: we do not create online user accounts for youth players, we do not track player browsing behavior, and we never share youth data with commercial advertising networks or third-party marketing entities.</p>
    <p class="text-tg-text">If any parent or guardian has child welfare concerns or questions regarding player data protection, they may contact our club office directly at <a href="mailto:info@topgradelondonfc.co.uk" class="text-tg-accent hover:underline">info@topgradelondonfc.co.uk</a>.</p>
</section>
HTML,
                'status' => 'published',
                'published_at' => now(),
            ]
        );

        // 5. Terms & Conditions
        Content::firstOrCreate(
            ['slug' => 'terms'],
            [
                'content_type_id' => $pageType->id,
                'title' => 'Terms & Conditions',
                'excerpt' => 'Terms and conditions for participation, trials, and sessions at TopGrade London FC.',
                'content' => <<<'HTML'
<section class="space-y-3">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">1. Agreement &amp; Organization</h2>
    <p class="text-tg-text">These Terms and Conditions govern the use of the TopGrade London FC website and participation in youth football training, introductory trials, and match activities organised by <strong class="text-tg-text-strong">TOPGRADE LONDON FC CIC</strong> (Company No. 14087076). By using our website or submitting a booking, parents and guardians agree to these terms on behalf of themselves and participating youth players.</p>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">2. Club Activities, Training &amp; Trials</h2>
    <p class="text-tg-text">TopGrade London FC delivers structured youth football coaching across technical development, game understanding, teamwork, and competitive match play for age groups U7 through U16.</p>
    <p class="text-tg-text">Introductory trial sessions allow prospective players to experience our coaching and squad environment. Trial places are subject to group capacity and coach availability.</p>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">3. Bookings, Capacity &amp; Participant Information</h2>
    <p class="text-tg-text">All session requests must be submitted by a parent or legal guardian with accurate contact information, emergency numbers, and participant details. Because player safety and drill quality require manageable coach-to-player ratios, schedules enforce strict capacity limits.</p>
</section>
HTML,
                'status' => 'published',
                'published_at' => now(),
            ]
        );
    }
}
