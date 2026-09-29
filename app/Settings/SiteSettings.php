<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SiteSettings extends Settings
{
    public string $site_name = 'TopGrade London FC';

    public string $site_url = 'https://topgradelondonfc.co.uk';

    public string $tagline = 'Youth Football Club in London';

    public string $default_og_image = '/images/og/topgrade-london-fc.jpg';

    public string $locale = 'en_GB';

    public string $timezone = 'Europe/London';

    public static function group(): string
    {
        return 'site';
    }
}
