<?php

namespace App\Console\Commands;

use App\Models\Content;
use App\Models\Moment;
use App\Models\Staff;
use App\Models\Team;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Throwable;

class SyncMediaStorageCommand extends Command
{
    protected $signature = 'media:sync-storage {--force : Overwrite existing target files even if they already exist}';

    protected $description = 'Restore and ensure canonical seed-owned MediaLibrary assets exist in storage';

    /**
     * Manifest of canonical repository-owned media assets.
     *
     * @return array<int, array{
     *     model: class-string,
     *     identifier: array<string, mixed>,
     *     collection: string,
     *     source: string,
     *     file_name: string
     * }>
     */
    protected function getCanonicalAssets(): array
    {
        return [
            // Home Page Video & Hero Poster
            [
                'model' => Content::class,
                'identifier' => ['slug' => 'home'],
                'collection' => 'videos',
                'source' => 'topgrade-video.mp4',
                'file_name' => 'topgrade-video.mp4',
            ],
            [
                'model' => Content::class,
                'identifier' => ['slug' => 'home'],
                'collection' => 'images',
                'source' => 'images/club/hero-football.jpg',
                'file_name' => 'hero-football.jpg',
            ],

            // Head Coach / Staff Photo
            [
                'model' => Staff::class,
                'identifier' => ['slug' => 'richard-matey-opoku'],
                'collection' => 'photo',
                'source' => 'images/club/club-coach-mentoring.jpg',
                'file_name' => 'club-coach-mentoring.jpg',
            ],

            // Club Squad Teams
            [
                'model' => Team::class,
                'identifier' => ['slug' => 'u7-u8'],
                'collection' => 'image',
                'source' => 'images/club/youth_match_action.jpg',
                'file_name' => 'youth_match_action.jpg',
            ],
            [
                'model' => Team::class,
                'identifier' => ['slug' => 'u9-u10'],
                'collection' => 'image',
                'source' => '484192970_1108922961247045_3935375872642678849_n.jpg',
                'file_name' => '484192970_1108922961247045_3935375872642678849_n.jpg',
            ],
            [
                'model' => Team::class,
                'identifier' => ['slug' => 'u11-u12'],
                'collection' => 'image',
                'source' => '485087659_1108923127913695_5031817050217357486_n.jpg',
                'file_name' => '485087659_1108923127913695_5031817050217357486_n.jpg',
            ],
            [
                'model' => Team::class,
                'identifier' => ['slug' => 'u13-u14'],
                'collection' => 'image',
                'source' => 'images/club/tactical_coaching.jpg',
                'file_name' => 'tactical_coaching.jpg',
            ],
            [
                'model' => Team::class,
                'identifier' => ['slug' => 'u15-u16'],
                'collection' => 'image',
                'source' => 'images/club/squad_celebration.jpg',
                'file_name' => 'squad_celebration.jpg',
            ],

            // Moments Gallery Items
            [
                'model' => Moment::class,
                'identifier' => ['slug' => 'midweek-technical-training'],
                'collection' => 'gallery',
                'source' => 'images/club/club-training-london.jpg',
                'file_name' => 'club-training-london.jpg',
            ],
            [
                'model' => Moment::class,
                'identifier' => ['slug' => 'midweek-technical-training'],
                'collection' => 'gallery',
                'source' => 'images/club/training-agility-drills.jpg',
                'file_name' => 'training-agility-drills.jpg',
            ],
            [
                'model' => Moment::class,
                'identifier' => ['slug' => 'matchday-saturday-league-fixture'],
                'collection' => 'gallery',
                'source' => '484977737_1109608517845156_6730033439051003629_n.jpg',
                'file_name' => '484977737_1109608517845156_6730033439051003629_n.jpg',
            ],
            [
                'model' => Moment::class,
                'identifier' => ['slug' => 'matchday-saturday-league-fixture'],
                'collection' => 'gallery',
                'source' => 'images/club/youth_match_action.jpg',
                'file_name' => 'youth_match_action.jpg',
            ],
            [
                'model' => Moment::class,
                'identifier' => ['slug' => 'matchday-saturday-league-fixture'],
                'collection' => 'gallery',
                'source' => 'images/club/squad_celebration.jpg',
                'file_name' => 'squad_celebration.jpg',
            ],
            [
                'model' => Moment::class,
                'identifier' => ['slug' => 'squad-mentoring-tactical-coaching'],
                'collection' => 'gallery',
                'source' => 'images/club/tactical_coaching.jpg',
                'file_name' => 'tactical_coaching.jpg',
            ],
            [
                'model' => Moment::class,
                'identifier' => ['slug' => 'squad-mentoring-tactical-coaching'],
                'collection' => 'gallery',
                'source' => '484192970_1108922961247045_3935375872642678849_n.jpg',
                'file_name' => '484192970_1108922961247045_3935375872642678849_n.jpg',
            ],
        ];
    }

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $canonicalAssets = $this->getCanonicalAssets();
        $hasErrors = false;
        $restoredCount = 0;
        $attachedCount = 0;
        $alreadyPresentCount = 0;

        $this->info('Starting canonical media storage sync for '.count($canonicalAssets).' seed-owned asset(s)...');

        foreach ($canonicalAssets as $asset) {
            $sourcePath = public_path($asset['source']);

            if (! file_exists($sourcePath)) {
                $this->error("Canonical source asset missing on disk: {$sourcePath}");
                $hasErrors = true;

                continue;
            }

            /** @var Model|null $model */
            $model = $asset['model']::where($asset['identifier'])->first();

            if (! $model) {
                $this->warn("Model {$asset['model']} matching ".json_encode($asset['identifier']).' does not exist in database. Skipping.');

                continue;
            }

            // Find existing Media record matching this collection and filename
            /** @var Media|null $media */
            $media = $model->media()
                ->where('collection_name', $asset['collection'])
                ->where('file_name', $asset['file_name'])
                ->first();

            if ($media) {
                $targetPath = $media->getPath();

                if (! $force && file_exists($targetPath)) {
                    $this->line(" [OK] Media #{$media->id} ({$asset['file_name']}) already exists at {$targetPath}");
                    $alreadyPresentCount++;

                    continue;
                }

                File::ensureDirectoryExists(dirname($targetPath));
                $copied = File::copy($sourcePath, $targetPath);

                if (! $copied || ! file_exists($targetPath)) {
                    $this->error("Failed to copy canonical asset from {$sourcePath} to {$targetPath}");
                    $hasErrors = true;

                    continue;
                }

                $this->info(" [RESTORED] Media #{$media->id} ({$asset['file_name']}) restored to {$targetPath}");
                $restoredCount++;
            } else {
                // Media record does not exist yet; attach using MediaLibrary
                try {
                    /** @var Media $newMedia */
                    $newMedia = $model->addMedia($sourcePath)
                        ->preservingOriginal()
                        ->toMediaCollection($asset['collection']);

                    if (! file_exists($newMedia->getPath())) {
                        $this->error("Failed to verify newly attached media file at {$newMedia->getPath()}");
                        $hasErrors = true;

                        continue;
                    }

                    $this->info(" [ATTACHED] Attached {$asset['source']} as Media #{$newMedia->id} in collection '{$asset['collection']}'");
                    $attachedCount++;
                } catch (Throwable $e) {
                    $this->error("Exception attaching media for {$asset['model']} (".json_encode($asset['identifier'])."): {$e->getMessage()}");
                    $hasErrors = true;
                }
            }
        }

        if ($hasErrors) {
            $this->error("Canonical media sync finished with errors. Restored: {$restoredCount}, Attached: {$attachedCount}, OK: {$alreadyPresentCount}.");

            return self::FAILURE;
        }

        $this->info("Canonical media sync completed successfully. Restored: {$restoredCount}, Attached: {$attachedCount}, OK: {$alreadyPresentCount}.");

        return self::SUCCESS;
    }
}
