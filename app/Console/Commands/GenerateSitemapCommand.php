<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GenerateSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate {--path= : Custom file path to write sitemap to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the XML sitemap and write to public/sitemap.xml';

    /**
     * Execute the console command.
     */
    public function handle(SitemapService $sitemapService): int
    {
        $this->info('Generating sitemap.xml...');

        $customPath = $this->option('path');
        $targetPath = is_string($customPath) && $customPath !== ''
            ? $customPath
            : public_path('sitemap.xml');

        $writtenPath = $sitemapService->writeToDisk($targetPath);

        // Clear dynamic sitemap cache so any fallback routes re-read fresh data
        Cache::forget('sitemap.xml');

        $this->info("Sitemap successfully generated at: {$writtenPath}");

        return self::SUCCESS;
    }
}
