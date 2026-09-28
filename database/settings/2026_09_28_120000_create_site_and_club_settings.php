<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // Site Settings
        $this->migrator->add('site.site_name', 'TopGrade London FC');
        $this->migrator->add('site.site_url', 'https://topgradelondonfc.co.uk');
        $this->migrator->add('site.tagline', 'Youth Football Club in London');
        $this->migrator->add('site.default_og_image', '/images/og/topgrade-london-fc.jpg');
        $this->migrator->add('site.locale', 'en_GB');
        $this->migrator->add('site.timezone', 'Europe/London');

        // Club Settings
        $this->migrator->add('club.club_name', 'TopGrade London FC');
        $this->migrator->add('club.legal_name', 'TOPGRADE LONDON FC CIC');
        $this->migrator->add('club.company_number', '14087076');
        $this->migrator->add('club.address', '30 Broadwater Road, London, England, N17 6ES');
        $this->migrator->add('club.email', 'info@topgradelondonfc.co.uk');
        $this->migrator->add('club.phone', null);
        $this->migrator->add('club.instagram_url', 'https://www.instagram.com/topgradelondonfc/');
        $this->migrator->add('club.instagram_handle', '@topgradelondonfc');
        $this->migrator->add('club.logo_path', '/logo.png');
    }

    public function down(): void
    {
        // Site Settings
        $this->migrator->delete('site.site_name');
        $this->migrator->delete('site.site_url');
        $this->migrator->delete('site.tagline');
        $this->migrator->delete('site.default_og_image');
        $this->migrator->delete('site.locale');
        $this->migrator->delete('site.timezone');

        // Club Settings
        $this->migrator->delete('club.club_name');
        $this->migrator->delete('club.legal_name');
        $this->migrator->delete('club.company_number');
        $this->migrator->delete('club.address');
        $this->migrator->delete('club.email');
        $this->migrator->delete('club.phone');
        $this->migrator->delete('club.instagram_url');
        $this->migrator->delete('club.instagram_handle');
        $this->migrator->delete('club.logo_path');
    }
};
