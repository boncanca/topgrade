<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@topgrade.test'],
            [
                'name' => 'Admin User',
                'is_admin' => true,
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'is_admin' => false,
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );

        $this->call([
            ContentTypeSeeder::class,
            TGLFCSeeder::class,
            BookingSeeder::class,
            ContactSeeder::class,
            InquirySeeder::class,
        ]);
    }
}
