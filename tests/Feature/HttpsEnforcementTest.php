<?php

use App\Providers\AppServiceProvider;
use Illuminate\Support\Facades\URL;

test('trusts forwarded proto header from proxy to determine https scheme', function () {
    $response = $this->withServerVariables([
        'HTTP_X_FORWARDED_PROTO' => 'https',
        'REMOTE_ADDR' => '192.168.1.1',
    ])->get('/');

    $response->assertOk();
    expect(request()->isSecure())->toBeTrue();
    expect(asset('build/assets/test.woff2'))->toStartWith('https://');
});

test('forces https scheme in production environment', function () {
    $this->app['env'] = 'production';
    (new AppServiceProvider(app()))->boot();

    expect(URL::to('/test'))->toStartWith('https://');
    expect(asset('build/assets/test.woff2'))->toStartWith('https://');
});
