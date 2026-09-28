<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SiteSettings extends Settings
{
    public string $site_name;

    public string $site_url;

    public string $tagline;

    public string $default_og_image;

    public string $locale;

    public string $timezone;

    public static function group(): string
    {
        return 'site';
    }
}
