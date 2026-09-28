<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ClubSettings extends Settings
{
    public string $club_name;

    public string $legal_name;

    public string $company_number;

    public string $address;

    public string $email;

    public ?string $phone;

    public string $instagram_url;

    public string $instagram_handle;

    public string $logo_path;

    public static function group(): string
    {
        return 'club';
    }
}
