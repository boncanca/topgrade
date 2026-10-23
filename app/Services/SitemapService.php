<?php

namespace App\Services;

use App\Models\BookableItem;
use App\Models\Content;
use App\Models\Moment;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapService
{
    /**
     * Build the sitemap instance with all public static and dynamic URLs.
     */
    public function build(): Sitemap
    {
        $sitemap = Sitemap::create()
            ->add(Url::create($this->canonicalUrl('/'))->setPriority(1.0)->setChangeFrequency('daily'))
            ->add(Url::create($this->canonicalUrl('/moments'))->setPriority(0.9)->setChangeFrequency('weekly'))
            ->add(Url::create($this->canonicalUrl('/training'))->setPriority(0.9)->setChangeFrequency('weekly'))
            ->add(Url::create($this->canonicalUrl('/bookings'))->setPriority(0.9)->setChangeFrequency('weekly'))
            ->add(Url::create($this->canonicalUrl('/about'))->setPriority(0.8)->setChangeFrequency('monthly'))
            ->add(Url::create($this->canonicalUrl('/articles'))->setPriority(0.8)->setChangeFrequency('daily'))
            ->add(Url::create($this->canonicalUrl('/contact'))->setPriority(0.7)->setChangeFrequency('monthly'))
            ->add(Url::create($this->canonicalUrl('/privacy-policy'))->setPriority(0.5)->setChangeFrequency('yearly'))
            ->add(Url::create($this->canonicalUrl('/terms-and-conditions'))->setPriority(0.5)->setChangeFrequency('yearly'));

        // Dynamic published moments
        Moment::published()->each(function (Moment $moment) use ($sitemap): void {
            $sitemap->add(
                Url::create($this->canonicalUrl("/moments/{$moment->slug}"))
                    ->setLastModificationDate($moment->updated_at ?? now())
                    ->setPriority(0.8)
                    ->setChangeFrequency('weekly')
            );
        });

        // Dynamic published articles
        Content::published()
            ->whereHas('contentType', fn ($q) => $q->where('slug', 'article'))
            ->each(function (Content $article) use ($sitemap): void {
                $sitemap->add(
                    Url::create($this->canonicalUrl("/articles/{$article->slug}"))
                        ->setLastModificationDate($article->updated_at ?? now())
                        ->setPriority(0.7)
                        ->setChangeFrequency('weekly')
                );
            });

        // Dynamic active bookable items
        BookableItem::where('is_active', true)->each(function (BookableItem $item) use ($sitemap): void {
            $sitemap->add(
                Url::create($this->canonicalUrl("/bookings/{$item->slug}"))
                    ->setLastModificationDate($item->updated_at ?? now())
                    ->setPriority(0.7)
                    ->setChangeFrequency('weekly')
            );
        });

        return $sitemap;
    }

    private function canonicalUrl(string $path): string
    {
        return rtrim((string) config('app.url'), '/').'/'.ltrim($path, '/');
    }

    /**
     * Render the sitemap as an XML string.
     */
    public function render(): string
    {
        return $this->build()->render();
    }

    /**
     * Write the generated sitemap to the disk.
     */
    public function writeToDisk(?string $path = null): string
    {
        $targetPath = $path ?? public_path('sitemap.xml');
        $this->build()->writeToFile($targetPath);

        return $targetPath;
    }
}
