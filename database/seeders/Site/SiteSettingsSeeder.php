<?php

namespace Database\Seeders\Site;

use App\Settings\SiteSettings;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = app(SiteSettings::class);

        // Ensure defaults if not already customized
        if (empty($settings->site_name)) {
            $settings->site_name = 'TopGrade London FC';
        }
        if (empty($settings->site_url)) {
            $settings->site_url = env('APP_URL', 'https://topgradelondonfc.co.uk');
        }
        if (empty($settings->tagline)) {
            $settings->tagline = 'Youth Football Club in London';
        }
        if (empty($settings->default_og_image)) {
            $settings->default_og_image = '/images/og/topgrade-london-fc.jpg';
        }
        if (empty($settings->locale)) {
            $settings->locale = 'en_GB';
        }
        if (empty($settings->timezone)) {
            $settings->timezone = 'Europe/London';
        }

        $settings->save();
    }
}
