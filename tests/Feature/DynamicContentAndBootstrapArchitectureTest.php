<?php

use App\Models\Menu;
use App\Models\Schedule;
use App\Models\Team;
use App\Models\TrainingSession;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Settings\ClubSettings;
use App\Settings\SiteSettings;
use Database\Seeders\Booking\SchedulesSeeder;
use Database\Seeders\Club\TeamsSeeder;
use Database\Seeders\Club\TrainingTimetableSeeder;
use Database\Seeders\Club\VenuesSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Site\NavigationSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;

test('database seeder establishes both club admin and protected dev super admin accounts', function () {
    $this->seed(UserSeeder::class);

    $clubAdmin = User::where('email', 'info@topgradelondonfc.co.uk')->first();
    $devAdmin = User::where('email', 'dev@trupabranding.com')->first();

    expect($clubAdmin)->not->toBeNull()
        ->and($clubAdmin->is_admin)->toBeTrue()
        ->and($clubAdmin->is_system_account)->toBeFalse();

    expect($devAdmin)->not->toBeNull()
        ->and($devAdmin->is_admin)->toBeTrue()
        ->and($devAdmin->is_system_account)->toBeTrue();
});

test('user seeder is idempotent and preserves existing passwords on subsequent runs', function () {
    $this->seed(UserSeeder::class);

    $originalDevPassword = User::where('email', 'dev@trupabranding.com')->first()->password;
    $originalAdminPassword = User::where('email', 'info@topgradelondonfc.co.uk')->first()->password;

    // Run again
    $this->seed(UserSeeder::class);

    $devAfter = User::where('email', 'dev@trupabranding.com')->first();
    $adminAfter = User::where('email', 'info@topgradelondonfc.co.uk')->first();

    expect($devAfter->password)->toBe($originalDevPassword)
        ->and($adminAfter->password)->toBe($originalAdminPassword);
});

test('dev super admin is protected server-side and cannot be managed or deleted by club admin', function () {
    $clubAdmin = User::factory()->create([
        'email' => 'club-admin@topgradelondonfc.co.uk',
        'is_admin' => true,
        'is_system_account' => false,
    ]);

    $devAdmin = User::factory()->create([
        'email' => 'dev@trupabranding.com',
        'is_admin' => true,
        'is_system_account' => true,
    ]);

    $policy = new UserPolicy;

    // Club admin cannot view or edit dev account in user management
    expect($policy->view($clubAdmin, $devAdmin))->toBeFalse()
        ->and($policy->update($clubAdmin, $devAdmin))->toBeFalse()
        ->and($policy->delete($clubAdmin, $devAdmin))->toBeFalse()
        ->and($policy->impersonate($clubAdmin, $devAdmin))->toBeFalse()
        ->and($devAdmin->canBeManagedBy($clubAdmin))->toBeFalse();

    // Non-system scope hides system accounts from general listing
    $listedUsers = User::nonSystem()->get();
    expect($listedUsers->pluck('email'))->not->toContain('dev@trupabranding.com')
        ->and($listedUsers->pluck('email'))->toContain('club-admin@topgradelondonfc.co.uk');
});

test('canonical navigation defines main-navigation and footer-navigation and removes legacy main menu', function () {
    $this->seed(NavigationSeeder::class);

    expect(Menu::where('slug', 'main-navigation')->exists())->toBeTrue()
        ->and(Menu::where('slug', 'footer-navigation')->exists())->toBeTrue()
        ->and(Menu::where('slug', 'main')->exists())->toBeFalse();
});

test('spatie site and club settings are configured properly', function () {
    $siteSettings = app(SiteSettings::class);
    $clubSettings = app(ClubSettings::class);

    expect($siteSettings->site_name)->toBe('TopGrade London FC')
        ->and($siteSettings->site_url)->toContain('topgradelondonfc.co.uk')
        ->and($clubSettings->club_name)->toBe('TopGrade London FC')
        ->and($clubSettings->email)->toBe('info@topgradelondonfc.co.uk');
});

test('teams table is presentation-independent with semantic visual_variant only', function () {
    $this->seed(TeamsSeeder::class);

    $teams = Team::all();
    expect($teams)->not->toBeEmpty();

    foreach ($teams as $team) {
        expect($team->visual_variant)->toBeIn(['foundation', 'acquisition', 'development', 'progression', 'competition'])
            ->and($team->visual_variant)->not->toContain('md:')
            ->and($team->visual_variant)->not->toContain('col-span')
            ->and($team->visual_variant)->not->toContain('aspect-');
    }
});

test('training sessions represent recurring timetable and do not create fake operational schedules', function () {
    $this->seed(VenuesSeeder::class);
    $this->seed(TrainingTimetableSeeder::class);
    $this->seed(SchedulesSeeder::class);

    $sessions = TrainingSession::with('venue')->get();
    expect($sessions)->not->toBeEmpty();

    foreach ($sessions as $session) {
        expect($session->venue)->not->toBeNull()
            ->and($session->days_label)->toBeString()
            ->and($session->time_label)->toBeString();
    }

    // SchedulesSeeder in production must NOT invent fake future schedules
    expect(Schedule::count())->toBe(0);
});

test('public pages receive dynamic props from database entities and settings', function () {
    $this->seed(DatabaseSeeder::class);

    // Home
    $responseHome = $this->get('/');
    $responseHome->assertStatus(200);
    $responseHome->assertInertia(fn ($page) => $page
        ->component('Public/Home')
        ->has('hero')
        ->has('pillars')
        ->has('quickStats')
        ->has('squads')
        ->has('trainingSchedule')
        ->has('momentsRibbon')
    );

    // About
    $responseAbout = $this->get('/about');
    $responseAbout->assertStatus(200);
    $responseAbout->assertInertia(fn ($page) => $page
        ->component('Public/About')
        ->has('values')
        ->has('facilities')
        ->has('staff')
    );

    // Contact
    $responseContact = $this->get('/contact');
    $responseContact->assertStatus(200);
    $responseContact->assertInertia(fn ($page) => $page
        ->component('Public/Contact')
        ->has('clubSettings')
    );

    // Training
    $responseTraining = $this->get('/training');
    $responseTraining->assertStatus(200);
    $responseTraining->assertInertia(fn ($page) => $page
        ->component('Public/Training')
        ->has('weeklySchedule')
        ->has('activities')
    );
});

test('spatie site and club settings can resolve safely even without database records', function () {
    // Truncate settings table if exists
    DB::table('settings')->truncate();

    $siteSettings = app(SiteSettings::class);
    $clubSettings = app(ClubSettings::class);

    // Verify SiteSettings properties are non-empty strings
    expect($siteSettings->site_name)->toBe('TopGrade London FC')
        ->and($siteSettings->site_url)->toBe('https://topgradelondonfc.co.uk')
        ->and($siteSettings->tagline)->toBe('Youth Football Club in London')
        ->and($siteSettings->default_og_image)->toBe('/images/og/topgrade-london-fc.jpg')
        ->and($siteSettings->locale)->toBe('en_GB')
        ->and($siteSettings->timezone)->toBe('Europe/London');

    // Verify ClubSettings properties are non-empty strings
    expect($clubSettings->club_name)->toBe('TopGrade London FC')
        ->and($clubSettings->legal_name)->toBe('TOPGRADE LONDON FC CIC')
        ->and($clubSettings->company_number)->toBe('14087076')
        ->and($clubSettings->address)->toBe('30 Broadwater Road, London, England, N17 6ES')
        ->and($clubSettings->email)->toBe('info@topgradelondonfc.co.uk')
        ->and($clubSettings->instagram_url)->toBe('https://www.instagram.com/topgradelondonfc/')
        ->and($clubSettings->instagram_handle)->toBe('@topgradelondonfc')
        ->and($clubSettings->logo_path)->toBe('/logo.png');
});

test('schedules migration executes before bookings migration to satisfy foreign key dependency', function () {
    $migrationFiles = scandir(database_path('migrations'));
    $schedulesFile = collect($migrationFiles)->first(fn ($file) => str_contains($file, 'create_schedules_table'));
    $bookingsFile = collect($migrationFiles)->first(fn ($file) => str_contains($file, 'create_bookings_table'));

    expect($schedulesFile)->not->toBeNull()
        ->and($bookingsFile)->not->toBeNull()
        ->and(strcmp($schedulesFile, $bookingsFile))->toBeLessThan(0);
});
