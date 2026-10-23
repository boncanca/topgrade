<?php

namespace App\Services\Media;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Support\FileNamer\FileNamer;

class TopGradeFileNamer extends FileNamer
{
    /**
     * Standard prefix for all TopGrade London FC public media assets.
     */
    public const CANONICAL_PREFIX = 'topgrade-london-fc';

    /**
     * Generate a canonical, SEO-friendly file name adhering to the standard:
     * topgrade-london-fc-{subject}-{context}.{extension}
     */
    public function originalFileName(string $fileName): string
    {
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);

        // Normalize to lowercase kebab-case slug
        $slug = Str::slug($baseName);

        if ($slug === '' || $slug === '0') {
            $slug = 'media-asset';
        }

        // Standardize prefix
        if (str_starts_with($slug, self::CANONICAL_PREFIX.'-') || $slug === self::CANONICAL_PREFIX) {
            return $slug;
        }

        if (str_starts_with($slug, 'topgrade-')) {
            $remainder = substr($slug, strlen('topgrade-'));

            return self::CANONICAL_PREFIX.'-'.$remainder;
        }

        return self::CANONICAL_PREFIX.'-'.$slug;
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
