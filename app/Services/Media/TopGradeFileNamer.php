<?php

namespace App\Services\Media;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Support\FileNamer\FileNamer;

class TopGradeFileNamer extends FileNamer
{
    /**
     * Standard club suffix for all TopGrade London FC public media assets,
     * ensuring immediate identification in collapsed sidebars and narrow windows.
     */
    public const CLUB_SUFFIX = 'topgrade-london-fc';

    /**
     * Generate a canonical, SEO-friendly file name adhering to the standard:
     * {subject}-{context}-topgrade-london-fc.{extension}
     */
    public function originalFileName(string $fileName): string
    {
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);

        // Normalize to lowercase kebab-case slug
        $slug = Str::slug($baseName);

        if ($slug === '' || $slug === '0') {
            $slug = 'media-asset';
        }

        // If old prefix was present, strip it so name comes first
        if (str_starts_with($slug, self::CLUB_SUFFIX.'-')) {
            $slug = substr($slug, strlen(self::CLUB_SUFFIX.'-'));
        } elseif (str_starts_with($slug, 'topgrade-')) {
            $slug = substr($slug, strlen('topgrade-'));
        }

        // If already ends with -topgrade-london-fc or is topgrade-london-fc
        if (str_ends_with($slug, '-'.self::CLUB_SUFFIX) || $slug === self::CLUB_SUFFIX) {
            return $slug;
        }

        return $slug.'-'.self::CLUB_SUFFIX;
    }

    /**
     * Name generated image conversions.
     */
    public function conversionFileName(string $fileName, Conversion $conversion): string
    {
        $strippedFileName = $this->responsiveFileName($fileName);

        return "{$strippedFileName}-{$conversion->getName()}";
    }

    /**
     * Name responsive image variations.
     */
    public function responsiveFileName(string $fileName): string
    {
        return pathinfo($fileName, PATHINFO_FILENAME);
    }
}
