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
    protected $signature = 'media:sync-storage {--force : Overwrite existing target files even if they already exist} {--prune-missing : Prune database media records whose physical files are missing from storage}';

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
                'source' => 'hero-training-loop-topgrade-london-fc.mp4',
                'file_name' => 'hero-training-loop-topgrade-london-fc.mp4',
            ],
            [
                'model' => Content::class,
                'identifier' => ['slug' => 'home'],
                'collection' => 'images',
                'source' => 'images/club/hero-matchday-topgrade-london-fc.jpg',
                'file_name' => 'hero-matchday-topgrade-london-fc.jpg',
            ],

            // Head Coach / Staff Photo
            [
                'model' => Staff::class,
                'identifier' => ['slug' => 'richard-matey-opoku'],
                'collection' => 'photo',
                'source' => 'images/club/coach-mentoring-session-topgrade-london-fc.jpg',
                'file_name' => 'coach-mentoring-session-topgrade-london-fc.jpg',
            ],

            // Club Squad Teams
            [
                'model' => Team::class,
                'identifier' => ['slug' => 'u7-u8'],
                'collection' => 'image',
                'source' => 'images/club/u7-u8-squad-photo-topgrade-london-fc.jpg',
                'file_name' => 'u7-u8-squad-photo-topgrade-london-fc.jpg',
            ],
            [
                'model' => Team::class,
                'identifier' => ['slug' => 'u9-u10'],
                'collection' => 'image',
                'source' => 'images/club/skill-acquisition-training-topgrade-london-fc.jpg',
                'file_name' => 'skill-acquisition-training-topgrade-london-fc.jpg',
            ],
            [
                'model' => Team::class,
                'identifier' => ['slug' => 'u11-u12'],
                'collection' => 'image',
                'source' => 'images/club/game-development-match-action-topgrade-london-fc.jpg',
                'file_name' => 'game-development-match-action-topgrade-london-fc.jpg',
            ],
            [
                'model' => Team::class,
                'identifier' => ['slug' => 'u13-u14'],
                'collection' => 'image',
                'source' => 'images/club/tactical-coaching-briefing-topgrade-london-fc.jpg',
                'file_name' => 'tactical-coaching-briefing-topgrade-london-fc.jpg',
            ],
            [
                'model' => Team::class,
                'identifier' => ['slug' => 'u15-u16'],
                'collection' => 'image',
                'source' => 'images/club/squad-celebration-trophy-topgrade-london-fc.jpg',
                'file_name' => 'squad-celebration-trophy-topgrade-london-fc.jpg',
            ],

            // Moments Gallery Items
            [
                'model' => Moment::class,
                'identifier' => ['slug' => 'midweek-technical-training'],
                'collection' => 'gallery',
                'source' => 'images/club/youth-squad-team-topgrade-london-fc.jpg',
                'file_name' => 'youth-squad-team-topgrade-london-fc.jpg',
            ],
            [
                'model' => Moment::class,
                'identifier' => ['slug' => 'midweek-technical-training'],
                'collection' => 'gallery',
                'source' => 'images/club/training-agility-drills-topgrade-london-fc.jpg',
                'file_name' => 'training-agility-drills-topgrade-london-fc.jpg',
            ],
            [
                'model' => Moment::class,
                'identifier' => ['slug' => 'matchday-saturday-league-fixture'],
                'collection' => 'gallery',
                'source' => 'images/club/matchday-fixture-action-topgrade-london-fc.jpg',
                'file_name' => 'matchday-fixture-action-topgrade-london-fc.jpg',
            ],
            [
                'model' => Moment::class,
                'identifier' => ['slug' => 'matchday-saturday-league-fixture'],
                'collection' => 'gallery',
                'source' => 'images/club/foundation-phase-match-action-topgrade-london-fc.jpg',
                'file_name' => 'foundation-phase-match-action-topgrade-london-fc.jpg',
            ],
            [
                'model' => Moment::class,
                'identifier' => ['slug' => 'matchday-saturday-league-fixture'],
                'collection' => 'gallery',
                'source' => 'images/club/squad-celebration-trophy-topgrade-london-fc.jpg',
                'file_name' => 'squad-celebration-trophy-topgrade-london-fc.jpg',
            ],
            [
                'model' => Moment::class,
                'identifier' => ['slug' => 'squad-mentoring-tactical-coaching'],
                'collection' => 'gallery',
                'source' => 'images/club/tactical-coaching-briefing-topgrade-london-fc.jpg',
                'file_name' => 'tactical-coaching-briefing-topgrade-london-fc.jpg',
            ],
            [
                'model' => Moment::class,
                'identifier' => ['slug' => 'squad-mentoring-tactical-coaching'],
                'collection' => 'gallery',
                'source' => 'images/club/player-match-action-topgrade-london-fc.jpg',
                'file_name' => 'player-match-action-topgrade-london-fc.jpg',
            ],
        ];
    }

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $pruneMissing = (bool) $this->option('prune-missing');

        if ($pruneMissing) {
            $prunedCount = 0;
            $localMedia = Media::whereIn('disk', ['public', 'local'])->get();

            foreach ($localMedia as $item) {
                if (! file_exists($item->getPath())) {
                    $this->warn("Pruning orphaned media #{$item->id} ({$item->file_name}) missing at {$item->getPath()}");
                    $item->delete();
                    $prunedCount++;
                }
            }

            if ($prunedCount > 0) {
                $this->info("Pruned {$prunedCount} orphaned media record(s) with missing disk files.");
            }
        }

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

            $namer = app(config('media-library.file_namer'));
            $expectedBase = $namer->originalFileName($asset['file_name']);
            $expectedExt = pathinfo($asset['file_name'], PATHINFO_EXTENSION);
            $expectedFileName = $expectedExt ? "{$expectedBase}.{$expectedExt}" : $expectedBase;
            $sourceBaseName = pathinfo($asset['source'], PATHINFO_BASENAME);

            // Find existing Media record matching this collection and filename
            /** @var Media|null $media */
            $media = $model->media()
                ->where('collection_name', $asset['collection'])
                ->where(function ($query) use ($asset, $expectedFileName, $sourceBaseName) {
                    $query->where('file_name', $asset['file_name'])
                        ->orWhere('file_name', $expectedFileName)
                        ->orWhere('file_name', $sourceBaseName);
                })
                ->first();

            $isRemote = ! in_array($media?->disk ?? config('media-library.disk_name'), ['public', 'local'], true)
                || config('filesystems.disks.'.($media?->disk ?? config('media-library.disk_name')).'.driver', 'local') !== 'local';

            if ($media) {
                if ($isRemote) {
                    $this->line(" [OK] Media #{$media->id} ({$media->file_name}) already registered on remote disk '{$media->disk}'");
                    $alreadyPresentCount++;

                    continue;
                }

                $targetPath = $media->getPath();

                if (! $force && file_exists($targetPath)) {
                    $this->line(" [OK] Media #{$media->id} ({$media->file_name}) already exists at {$targetPath}");
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

                $this->info(" [RESTORED] Media #{$media->id} ({$media->file_name}) restored to {$targetPath}");
                $restoredCount++;
            } else {
                // Media record does not exist yet; attach using MediaLibrary
                try {
                    /** @var Media $newMedia */
                    $newMedia = $model->addMedia($sourcePath)
                        ->preservingOriginal()
                        ->usingFileName($asset['file_name'])
                        ->toMediaCollection($asset['collection']);

                    $newMediaIsRemote = ! in_array($newMedia->disk, ['public', 'local'], true)
                        || config("filesystems.disks.{$newMedia->disk}.driver", 'local') !== 'local';

                    if (! $newMediaIsRemote && ! file_exists($newMedia->getPath())) {
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
