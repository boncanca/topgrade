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
                'photo_asset' => 'images/club/coach-mentoring-session-topgrade-london-fc.webp',
            ],
        ];

        foreach ($staffMembers as $member) {
            $photoAsset = $member['photo_asset'] ?? null;
            unset($member['photo_asset']);

            $staff = Staff::firstOrCreate(
                ['slug' => $member['slug']],
                $member
            );

            // Populate media collection from shipped canonical assets if empty
            if ($photoAsset && $staff->getMedia('photo')->isEmpty()) {
                $fullPath = public_path($photoAsset);
                if (file_exists($fullPath)) {
                    $staff->addMedia($fullPath)
                        ->preservingOriginal()
                        ->toMediaCollection('photo');
                }
            }
        }
    }
}
