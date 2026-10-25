<?php

use App\Models\Inquiry;
use App\Models\Moment;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

test('public /teams page displays active youth squads and rosters', function () {
    $moment = Moment::factory()->create([
        'status' => 'published',
        'title' => 'Saturday League Action',
    ]);

    Team::create([
        'name' => 'U11 – U12 Squad',
        'slug' => 'u11-u12-test',
        'age_group' => 'U11–U12',
        'stage' => 'Game Development',
        'description' => 'Tactical positional awareness.',
        'moment_id' => $moment->id,
        'sort_order' => 1,
        'is_active' => true,
        'players' => [
            ['name' => 'Kobe Mensah', 'number' => 9, 'position' => 'Striker', 'role' => 'Player'],
            ['name' => 'Tariq Sterling', 'number' => 8, 'position' => 'Midfielder', 'role' => 'Captain'],
        ],
    ]);

    $response = $this->get('/teams');

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Teams')
            ->has('teams')
            ->has('moments')
        );
});

test('parents can submit trial application for youth squad', function () {
    $payload = [
        'parent_name' => 'David O\'Connor',
        'email' => 'david.oc@example.com',
        'phone' => '07700900123',
        'player_name' => 'Zack O\'Connor',
        'player_dob' => '2016-04-12',
        'age_group' => 'U9–U10',
        'preferred_position' => 'Midfielder',
        'experience' => 'East London Grassroots League',
        'notes' => 'No medical conditions.',
    ];

    $response = $this->post('/teams/apply', $payload);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('inquiries', [
        'email' => 'david.oc@example.com',
        'name' => 'David O\'Connor',
    ]);

    $inquiry = Inquiry::where('email', 'david.oc@example.com')->first();
    expect($inquiry->subject)->toContain('Zack O\'Connor')
        ->and($inquiry->message)->toContain('Date of Birth: 2016-04-12');
});

test('dev admin can access and manage teams in dashboard', function () {
    $devAdmin = User::factory()->create([
        'name' => 'Dev Admin',
        'email' => 'devadmin@example.com',
        'role' => 'dev',
        'is_admin' => true,
        'is_system_account' => true,
    ]);

    $team = Team::create([
        'name' => 'U7 – U8 Squad',
        'slug' => 'u7-u8-admin-test',
        'age_group' => 'U7–U8',
        'stage' => 'Foundation Phase',
        'sort_order' => 1,
        'is_active' => true,
        'players' => [
            ['name' => 'Leo Mensah', 'number' => 1, 'position' => 'Goalkeeper', 'role' => 'Player'],
        ],
    ]);

    // 1. Index
    $this->actingAs($devAdmin)->get('/dashboard/teams')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Teams/Index')->has('teams.data'));

    // 2. Create form
    $this->actingAs($devAdmin)->get('/dashboard/teams/create')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Teams/Create'));

    // 3. Edit form
    $this->actingAs($devAdmin)->get("/dashboard/teams/{$team->id}/edit")
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Teams/Edit'));

    // 4. Update
    $updateResponse = $this->actingAs($devAdmin)->put("/dashboard/teams/{$team->id}", [
        'name' => 'U7 – U8 Updated Squad',
        'age_group' => 'U7–U8',
        'stage' => 'Foundation Phase',
        'description' => 'Updated coaching briefing.',
        'sort_order' => 2,
        'is_active' => true,
        'players' => [
            ['name' => 'Leo Mensah', 'number' => 1, 'position' => 'Goalkeeper', 'role' => 'Captain'],
        ],
    ]);

    $updateResponse->assertRedirect('/dashboard/teams');
    expect($team->fresh()->name)->toBe('U7 – U8 Updated Squad');

    // 5. Delete
    $deleteResponse = $this->actingAs($devAdmin)->delete("/dashboard/teams/{$team->id}");
    $deleteResponse->assertRedirect('/dashboard/teams');
    expect(Team::find($team->id))->toBeNull();
});

test('regular club admin cannot access dashboard teams', function () {
    $regularAdmin = User::factory()->admin()->create([
        'role' => 'admin',
        'is_system_account' => false,
    ]);

    $this->actingAs($regularAdmin)->get('/dashboard/teams')
        ->assertForbidden();
});

test('regular club admin cannot see dev admin account', function () {
    $devAdmin = User::factory()->create([
        'name' => 'Dev Admin',
        'email' => 'dev@example.com',
        'role' => 'dev',
        'is_system_account' => true,
        'is_admin' => true,
    ]);

    $regularAdmin = User::factory()->admin()->create([
        'name' => 'Club Admin',
        'email' => 'clubadmin@example.com',
        'role' => 'admin',
        'is_system_account' => false,
    ]);

    $response = $this->actingAs($regularAdmin)->get('/dashboard/users');

    $response->assertOk();
    $pageProps = $response->viewData('page')['props'] ?? [];
    $usersData = $pageProps['users']['data'] ?? [];

    $foundDev = collect($usersData)->firstWhere('email', 'dev@example.com');
    expect($foundDev)->toBeNull();
});
