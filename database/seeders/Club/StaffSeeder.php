<?php

namespace Database\Seeders\Club;

use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $staffMembers = [
            [
                'name' => 'Richard Matey Opoku',
                'slug' => 'richard-matey-opoku',
                'role' => 'Head Coach & Founder',
                'bio' => 'Dedicated coaching staff focusing on age-appropriate training, disciplined player habits, positive reinforcement, and tactical game understanding.',
                'qualifications' => 'FA Licensed Youth Coach',
                'sort_order' => 1,
                'is_active' => true,
            ],
        ];

        foreach ($staffMembers as $member) {
            Staff::firstOrCreate(
                ['slug' => $member['slug']],
                $member
            );
        }
    }
}
