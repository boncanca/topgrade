<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Primary Club Administrator
        $adminEmail = env('ADMIN_EMAIL', 'info@topgradelondonfc.co.uk');
        $existingAdmin = User::where('email', $adminEmail)->first();

        if (! $existingAdmin) {
            $adminPassword = 'TGFC-'.Str::random(4).'-'.Str::random(4).'-'.Str::random(4);

            User::create([
                'name' => 'TopGrade Club Admin',
                'email' => $adminEmail,
                'is_admin' => true,
                'is_system_account' => false,
                'email_verified_at' => now(),
                'password' => Hash::make($adminPassword),
            ]);

            if ($this->command) {
                $this->command->newLine();
                $this->command->info('========================================================');
                $this->command->info('TopGrade London FC administrator account created.');
                $this->command->line("Email:    <comment>{$adminEmail}</comment>");
                $this->command->line("Password: <comment>{$adminPassword}</comment>");
                $this->command->warn('IMPORTANT: Store this password securely and change it after first login.');
                $this->command->info('========================================================');
                $this->command->newLine();
            }
        }

        // 2. Technical / Break-Glass Super Admin
        $devEmail = 'dev@trupabranding.com';
        $existingDev = User::where('email', $devEmail)->first();

        if (! $existingDev) {
            $devPassword = 'TGFC-'.Str::random(4).'-'.Str::random(4).'-'.Str::random(4);

            User::create([
                'name' => 'Technical Super Admin',
                'email' => $devEmail,
                'is_admin' => true,
                'is_system_account' => true,
                'email_verified_at' => now(),
                'password' => Hash::make($devPassword),
            ]);

            if ($this->command) {
                $this->command->newLine();
                $this->command->info('========================================================');
                $this->command->info('Technical Super Admin (Break-Glass) account created.');
                $this->command->line("Email:    <comment>{$devEmail}</comment>");
                $this->command->line("Password: <comment>{$devPassword}</comment>");
                $this->command->warn('IMPORTANT: Store this password securely and change it after first login.');
                $this->command->info('========================================================');
                $this->command->newLine();
            }
        }
    }
}
