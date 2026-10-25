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
        // 1. Technical / Developer Super Admin (User #1)
        $devEmail = (string) config('topgrade.dev_admin.email', 'tomc@trupabranding.com');
        $devName = (string) config('topgrade.dev_admin.name', 'Tom Collins');
        $devPassword = config('topgrade.dev_admin.password');

        if ($devEmail !== 'dev@trupabranding.com') {
            User::where('email', 'dev@trupabranding.com')->delete();
        }

        $existingDev = User::where('email', $devEmail)->first();
        $userOne = User::find(1);

        $devAttributes = [
            'name' => $devName,
            'email' => $devEmail,
            'is_admin' => true,
            'role' => 'dev',
            'is_system_account' => true,
            'email_verified_at' => $existingDev?->email_verified_at ?? $userOne?->email_verified_at ?? now(),
        ];

        $generatedDevPassword = null;
        if (! empty($devPassword)) {
            $devAttributes['password'] = Hash::make($devPassword);
        } elseif (! $existingDev && ! $userOne) {
            $generatedDevPassword = 'TGFC-'.Str::random(4).'-'.Str::random(4).'-'.Str::random(4);
            $devAttributes['password'] = Hash::make($generatedDevPassword);
        }

        if ($userOne && $userOne->email !== $devEmail) {
            if ($existingDev && $existingDev->id !== 1) {
                $existingDev->delete();
            }
            $userOne->update($devAttributes);
            $dev = $userOne;
        } else {
            $dev = User::updateOrCreate(
                ['email' => $devEmail],
                $devAttributes
            );
        }

        if ($this->command && (! $existingDev || ! empty($devPassword))) {
            $this->command->newLine();
            $this->command->info('========================================================');
            $this->command->info('Developer Super Admin (Dev Admin) synchronized as User #1.');
            $this->command->line("Email:    <comment>{$dev->email}</comment>");
            if ($generatedDevPassword) {
                $this->command->line("Password: <comment>{$generatedDevPassword}</comment>");
                $this->command->warn('IMPORTANT: Generated random password. Store this securely or set DEV_ADMIN_PASSWORD in your environment.');
            } else {
                $this->command->line('Password: <comment>[Configured via DEV_ADMIN_PASSWORD environment variable]</comment>');
            }
            $this->command->info('========================================================');
            $this->command->newLine();
        }

        // 2. Primary Club Administrator
        $adminEmail = (string) config('topgrade.admin.email', 'info@topgradelondonfc.co.uk');
        $adminName = (string) config('topgrade.admin.name', 'TopGrade Club Admin');
        $adminPassword = config('topgrade.admin.password');
        $existingAdmin = User::where('email', $adminEmail)->first();

        $adminAttributes = [
            'name' => $adminName,
            'email' => $adminEmail,
            'is_admin' => true,
            'role' => 'admin',
            'is_system_account' => false,
            'email_verified_at' => $existingAdmin?->email_verified_at ?? now(),
        ];

        $generatedAdminPassword = null;
        if (! empty($adminPassword)) {
            $adminAttributes['password'] = Hash::make($adminPassword);
        } elseif (! $existingAdmin) {
            $generatedAdminPassword = 'TGFC-'.Str::random(4).'-'.Str::random(4).'-'.Str::random(4);
            $adminAttributes['password'] = Hash::make($generatedAdminPassword);
        }

        $admin = User::updateOrCreate(
            ['email' => $adminEmail],
            $adminAttributes
        );

        if ($this->command && (! $existingAdmin || ! empty($adminPassword))) {
            $this->command->newLine();
            $this->command->info('========================================================');
            $this->command->info('TopGrade London FC Club Administrator synchronized.');
            $this->command->line("Email:    <comment>{$admin->email}</comment>");
            if ($generatedAdminPassword) {
                $this->command->line("Password: <comment>{$generatedAdminPassword}</comment>");
                $this->command->warn('IMPORTANT: Generated random password. Store this securely or set ADMIN_PASSWORD in your environment.');
            } else {
                $this->command->line('Password: <comment>[Configured via ADMIN_PASSWORD environment variable]</comment>');
            }
            $this->command->info('========================================================');
            $this->command->newLine();
        }
    }
}
