<?php

use App\Services\Media\TopGradeFileNamer;
use Spatie\MediaLibrary\Conversions\Conversion;
use Tests\TestCase;

uses(TestCase::class);

test('it formats original file names with descriptive slug first and club suffix last', function () {
    $namer = new TopGradeFileNamer;

    expect($namer->originalFileName('player-match-action.jpg'))
        ->toBe('player-match-action-topgrade-london-fc');

    expect($namer->originalFileName('u7-u8-squad-photo.png'))
        ->toBe('u7-u8-squad-photo-topgrade-london-fc');

    expect($namer->originalFileName('coach mentoring session.webp'))
        ->toBe('coach-mentoring-session-topgrade-london-fc');
});

test('it migrates legacy topgrade prefixed filenames to suffix standard', function () {
    $namer = new TopGradeFileNamer;

    expect($namer->originalFileName('topgrade-london-fc-player-match-action.webp'))
        ->toBe('player-match-action-topgrade-london-fc');

    expect($namer->originalFileName('topgrade-squad-huddle.jpg'))
        ->toBe('squad-huddle-topgrade-london-fc');
});

test('it avoids duplicate club suffix if already properly suffixed', function () {
    $namer = new TopGradeFileNamer;

    expect($namer->originalFileName('player-match-action-topgrade-london-fc.jpg'))
        ->toBe('player-match-action-topgrade-london-fc');
});

test('it handles fallback for empty or non-alphanumeric names', function () {
    $namer = new TopGradeFileNamer;

    expect($namer->originalFileName('---'))
        ->toBe('media-asset-topgrade-london-fc');
});

test('it formats conversion file names with conversion identifier', function () {
    $namer = new TopGradeFileNamer;
    $conversion = new Conversion('thumb');

    expect($namer->conversionFileName('player-match-action-topgrade-london-fc.jpg', $conversion))
        ->toBe('player-match-action-topgrade-london-fc-thumb');
});
