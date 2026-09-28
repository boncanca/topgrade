<?php

namespace Database\Seeders\Site;

use App\Settings\ClubSettings;
use Illuminate\Database\Seeder;

class ClubSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = app(ClubSettings::class);

        // Ensure defaults if not already set
        if (empty($settings->club_name)) {
            $settings->club_name = 'TopGrade London FC';
        }
        if (empty($settings->legal_name)) {
            $settings->legal_name = 'TOPGRADE LONDON FC CIC';
        }
        if (empty($settings->company_number)) {
            $settings->company_number = '14087076';
        }
        if (empty($settings->address)) {
            $settings->address = '30 Broadwater Road, London, England, N17 6ES';
        }
        if (empty($settings->email)) {
            $settings->email = config('topgrade.emails.info', 'info@topgradelondonfc.co.uk');
        }
        if (empty($settings->instagram_url)) {
            $settings->instagram_url = 'https://www.instagram.com/topgradelondonfc/';
        }
        if (empty($settings->instagram_handle)) {
            $settings->instagram_handle = '@topgradelondonfc';
        }
        if (empty($settings->logo_path)) {
            $settings->logo_path = '/logo.png';
        }

        $settings->save();
    }
}
