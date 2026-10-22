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
    protected $description = 'Generate and warm the XML sitemap cache';

    /**
     * Execute the console command.
     */
    public function handle(SitemapService $sitemapService): int
    {
        $this->info('Generating and caching sitemap.xml...');

        $xml = $sitemapService->render();
        Cache::put('sitemap.xml', $xml, now()->addHours(24));

        $customPath = $this->option('path');
        if (is_string($customPath) && $customPath !== '') {
            $writtenPath = $sitemapService->writeToDisk($customPath);
            $this->info("Sitemap file written to: {$writtenPath}");
        }

        $this->info('Sitemap cache successfully refreshed.');

        return self::SUCCESS;
    }
}
