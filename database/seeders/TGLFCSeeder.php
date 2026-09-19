<?php

namespace Database\Seeders;

use App\Models\BookableItem;
use App\Models\Content;
use App\Models\ContentType;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class TGLFCSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Confirmed Football Club Activities
        BookableItem::updateOrCreate(
            ['slug' => 'mini-kickers-ages-4-6'],
            [
                'name' => 'Mini Kickers (Ages 4–6)',
                'description' => 'Introduction to football fundamentals. Focus on coordination, basic ball control, and having fun with teammates.',
                'duration_minutes' => 45,
                'location' => 'London Training Ground',
                'price' => 15.00,
                'currency' => 'GBP',
                'capacity' => 12,
                'booking_label' => 'Book a Trial',
                'is_active' => true,
                'requires_payment' => true,
            ]
        );

        BookableItem::updateOrCreate(
            ['slug' => 'u8-squad-training'],
            [
                'name' => 'U8 Squad Training',
                'description' => 'Weekly training for under 8 club members. Develops technical skills, decision-making, and team play in a positive environment.',
                'duration_minutes' => 60,
                'location' => 'London Training Ground',
                'price' => 20.00,
                'currency' => 'GBP',
                'capacity' => 16,
                'booking_label' => 'Book a Trial',
                'is_active' => true,
                'requires_payment' => true,
            ]
        );

        BookableItem::updateOrCreate(
            ['slug' => 'u10-squad-training'],
            [
                'name' => 'U10 Squad Training',
                'description' => 'Structured football training for under 10 club players. Emphasis on technical ball mastery, tactical awareness, and match preparation.',
                'duration_minutes' => 75,
                'location' => 'London Training Ground',
                'price' => 25.00,
                'currency' => 'GBP',
                'capacity' => 18,
                'booking_label' => 'Book a Trial',
                'is_active' => true,
                'requires_payment' => true,
            ]
        );

        BookableItem::updateOrCreate(
            ['slug' => 'u12-youth-development'],
            [
                'name' => 'U12 Youth Development',
                'description' => 'Focused football development for under 12 players. Technical drills, tactical positioning, and competitive match experience.',
                'duration_minutes' => 90,
                'location' => 'London Training Ground',
                'price' => 30.00,
                'currency' => 'GBP',
                'capacity' => 20,
                'booking_label' => 'Book a Trial',
                'is_active' => true,
                'requires_payment' => true,
            ]
        );

        BookableItem::updateOrCreate(
            ['slug' => 'free-trial-session'],
            [
                'name' => 'Introductory Trial Session',
                'description' => 'Experience TopGrade London FC first-hand. Join a session to discover our coaching, team environment, and football values.',
                'duration_minutes' => 45,
                'location' => 'London Training Ground',
                'price' => 0.00,
                'currency' => 'GBP',
                'capacity' => 20,
                'booking_label' => 'Book a Trial',
                'is_active' => true,
                'requires_payment' => false,
            ]
        );

        // 2. Ensure Page & Article Content Types exist
        $pageType = ContentType::firstOrCreate(
            ['slug' => 'page'],
            [
                'name' => 'Page',
                'kind' => 'collection',
                'template' => 'default',
                'is_system' => true,
                'is_active' => true,
            ]
        );

        ContentType::firstOrCreate(
            ['slug' => 'article'],
            [
                'name' => 'Article',
                'kind' => 'collection',
                'template' => 'article',
                'is_system' => true,
                'is_active' => true,
            ]
        );

        // 3. Seed 5 Core Club Pages
        $pages = [
            [
                'slug' => 'home',
                'title' => 'TopGrade London FC — Youth Football Club',
                'excerpt' => 'A youth football club in London helping young players develop through training, teamwork and playing experience.',
                'content' => 'TopGrade London FC provides structured youth football training and development for young players.',
            ],
            [
                'slug' => 'about',
                'title' => 'About TopGrade London FC',
                'excerpt' => 'TopGrade London FC is a youth football club dedicated to helping young players develop through training, teamwork and playing experience.',
                'content' => 'We provide structured youth football training, coaching, and match opportunities in London.',
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact TopGrade London FC',
                'excerpt' => 'Get in touch with TopGrade London FC for questions regarding club teams, trial bookings, and training sessions.',
                'content' => 'Contact the club via email or our contact message form.',
            ],
            [
                'slug' => 'privacy',
                'title' => 'Privacy Policy',
                'excerpt' => 'Privacy policy and data protection guidelines for TopGrade London FC under UK GDPR.',
                'content' => <<<HTML
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

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">5. Cookies &amp; Tracking Technologies</h2>
    <p class="text-tg-text">Our website operates with zero third-party advertising cookies, zero marketing trackers, and zero behavioral tracking pixels.</p>
    <p class="text-tg-text">We use only strictly necessary, first-party technical cookies essential for secure website operation:</p>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border border-tg-border mt-2">
            <thead class="bg-tg-bg border-b border-tg-border text-tg-text-strong uppercase tracking-wider font-semibold">
                <tr>
                    <th class="p-2.5">Cookie Name</th>
                    <th class="p-2.5">Provider</th>
                    <th class="p-2.5">Purpose</th>
                    <th class="p-2.5">Duration</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-tg-border/60 text-tg-text-muted">
                <tr>
                    <td class="p-2.5 font-mono text-tg-accent">laravel_session</td>
                    <td class="p-2.5">TopGrade FC</td>
                    <td class="p-2.5">Maintains user session state during booking forms</td>
                    <td class="p-2.5">2 Hours</td>
                </tr>
                <tr>
                    <td class="p-2.5 font-mono text-tg-accent">XSRF-TOKEN</td>
                    <td class="p-2.5">TopGrade FC</td>
                    <td class="p-2.5">Protects forms against Cross-Site Request Forgery (CSRF)</td>
                    <td class="p-2.5">Session</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">6. Third-Party Services</h2>
    <p class="text-tg-text">We only use secure, trusted infrastructure providers to operate the club website:</p>
    <ul class="list-disc pl-5 space-y-1.5 text-tg-text-muted text-sm">
        <li><strong class="text-tg-text">Cloud &amp; Server Hosting:</strong> Secure UK/EU infrastructure for application hosting and database encryption.</li>
        <li><strong class="text-tg-text">Transactional Email Delivery:</strong> Secure API email service to send automated booking confirmations and inquiry receipts.</li>
    </ul>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">7. Data Retention &amp; Security</h2>
    <p class="text-tg-text">Personal data is retained only for as long as needed to administer your child's training sessions and club activities, plus statutory retention periods for insurance and emergency safety records. All stored records are protected using industry-standard TLS encryption in transit and secure database access controls.</p>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">8. Your Rights &amp; Accessibility Assistance</h2>
    <p class="text-tg-text">Under UK GDPR, you have the right to request access to the personal data we hold, request correction of inaccurate records, request deletion of data where retention is no longer necessary, and withdraw photography consent.</p>
    <p class="text-tg-text">We are committed to making our information accessible to all users. If you require this policy or club communications in an alternative format, or need assistance accessing club services, please let us know.</p>
    <p class="text-tg-text">You also have the right to lodge a complaint with the UK Information Commissioner's Office (<a href="https://ico.org.uk" target="_blank" rel="noopener noreferrer" class="text-tg-accent hover:underline">ico.org.uk</a>).</p>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">9. Contact Information</h2>
    <p class="text-tg-text">For any privacy inquiries, data subject requests, or welfare questions, contact us:</p>
    <p class="text-tg-text font-medium">
        Email: <a href="mailto:info@topgradelondonfc.co.uk" class="text-tg-accent hover:underline">info@topgradelondonfc.co.uk</a><br />
        Postal: TOPGRADE LONDON FC CIC, 30 Broadwater Road, London, England, N17 6ES
    </p>
</section>
HTML,
            ],
            [
                'slug' => 'terms',
                'title' => 'Terms & Conditions',
                'excerpt' => 'Terms and conditions for participation, trials, and sessions at TopGrade London FC.',
                'content' => <<<HTML
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
    <p class="text-tg-text">All session requests must be submitted by a parent or legal guardian with accurate contact information, emergency numbers, and participant details. Because player safety and drill quality require manageable coach-to-player ratios, schedules enforce strict capacity limits. Duplicate bookings for the same participant on the same schedule may be cancelled.</p>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">4. Health, Mandatory Kit &amp; Player Welfare</h2>
    <p class="text-tg-text">Player safety is paramount. Parents and guardians must disclose any medical conditions or physical needs prior to a player's first session.</p>
    <ul class="list-disc pl-5 space-y-1.5 text-tg-text-muted text-sm">
        <li><strong class="text-tg-text">Mandatory Shin Guards:</strong> Shin pads must be worn under football socks during all contact drills and match play. Players without shin guards cannot participate in contact drills.</li>
        <li><strong class="text-tg-text">Footwear:</strong> Suitable football boots or astroturf trainers suited to 3G all-weather floodlit pitches or indoor sports hall surfaces.</li>
        <li><strong class="text-tg-text">Hydration:</strong> Every player must bring a filled water bottle to every session.</li>
    </ul>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">5. Attendance, Cancellations &amp; Weather Updates</h2>
    <p class="text-tg-text">If a player cannot attend a booked session, please inform the club in advance at <a href="mailto:info@topgradelondonfc.co.uk" class="text-tg-accent hover:underline">info@topgradelondonfc.co.uk</a> so the space can be offered to another youth player.</p>
    <p class="text-tg-text">In the event of severe weather, pitch closures, or venue unavailability, the club will notify registered parents via email or SMS before the session begins.</p>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">6. Parent &amp; Player Code of Conduct</h2>
    <p class="text-tg-text">TopGrade London FC upholds a positive and respectful football environment. Players must listen to coaches, encourage teammates, and play fairly. Spectators and parents must support encouragingly from designated touchline areas without encroaching on the pitch or dissenting against match officials.</p>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">7. Photography &amp; Media</h2>
    <p class="text-tg-text">From time to time, official club photography captures training drills and matchday celebrations for editorial reporting and website promotion. We respect parent preferences; any parent wishing to opt out of public media may notify the club at any time.</p>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">8. Intellectual Property &amp; Website Use</h2>
    <p class="text-tg-text">All text, trademarks, club crests, photography, and digital materials on this website are the property of TOPGRADE LONDON FC CIC or its licensors and are protected under UK intellectual property laws.</p>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">9. Limitation of Liability &amp; Governing Law</h2>
    <p class="text-tg-text">Football is an active, physical sport with inherent risks of accidental bumps, bruises, and sports injuries. While TopGrade coaches exercise reasonable care and safety management, participation is undertaken acknowledging standard sporting risks.</p>
    <p class="text-tg-text">These terms are governed by and construed in accordance with the laws of England and Wales, subject to the exclusive jurisdiction of the English courts.</p>
</section>

<section class="space-y-3 border-t border-tg-border pt-6">
    <h2 class="text-xl font-normal uppercase tracking-wide text-tg-text-strong" style="font-family: var(--tg-display);">10. Contact Us</h2>
    <p class="text-tg-text">For any questions regarding these terms, club policies, or sessions:</p>
    <p class="text-tg-text font-medium">
        Email: <a href="mailto:info@topgradelondonfc.co.uk" class="text-tg-accent hover:underline">info@topgradelondonfc.co.uk</a><br />
        Postal: TOPGRADE LONDON FC CIC, 30 Broadwater Road, London, England, N17 6ES
    </p>
</section>
HTML,
            ],
        ];

        foreach ($pages as $p) {
            $content = Content::updateOrCreate(
                ['slug' => $p['slug']],
                [
                    'content_type_id' => $pageType->id,
                    'title' => $p['title'],
                    'excerpt' => $p['excerpt'],
                    'content' => $p['content'],
                    'status' => 'published',
                    'published_at' => now(),
                ]
            );

            if ($p['slug'] === 'home') {
                $content->blocks()->delete();
            }
        }

        // Backward compatibility for existing Menu relationships if present
        $headerMenu = Menu::firstOrCreate(
            ['slug' => 'main-navigation'],
            ['name' => 'Main Navigation', 'location' => 'main']
        );
        if ($headerMenu->allItems()->count() === 0) {
            $headerMenu->allItems()->createMany([
                ['label' => 'Home', 'url' => '/', 'sort_order' => 1],
                ['label' => 'Training', 'url' => '/training', 'sort_order' => 2],
                ['label' => 'Bookings', 'url' => '/bookings', 'sort_order' => 3],
                ['label' => 'About', 'url' => '/about', 'sort_order' => 4],
                ['label' => 'Articles', 'url' => '/articles', 'sort_order' => 5],
                ['label' => 'Contact', 'url' => '/contact', 'sort_order' => 6],
            ]);
        }
    }
}
