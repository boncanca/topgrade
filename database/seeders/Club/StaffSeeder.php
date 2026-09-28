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
            $staff = Staff::firstOrCreate(
                ['slug' => $member['slug']],
                $member
            );

            // Attach photo if not already attached and file exists
            if ($staff->getMedia('photo')->isEmpty()) {
                $photoPath = public_path('images/club/club-coach-mentoring.jpg');
                if (file_exists($photoPath)) {
                    $staff->addMedia($photoPath)
                        ->preservingOriginal()
                        ->toMediaCollection('photo');
                }
            }
        }
    }
}
