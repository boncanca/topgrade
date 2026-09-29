<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ClubSettings extends Settings
{
    public string $club_name = 'TopGrade London FC';

    public string $legal_name = 'TOPGRADE LONDON FC CIC';

    public string $company_number = '14087076';

    public string $address = '30 Broadwater Road, London, England, N17 6ES';

    public string $email = 'info@topgradelondonfc.co.uk';

    public ?string $phone = null;

    public string $instagram_url = 'https://www.instagram.com/topgradelondonfc/';

    public string $instagram_handle = '@topgradelondonfc';

    public string $logo_path = '/logo.png';

    public static function group(): string
    {
        return 'club';
    }
}
