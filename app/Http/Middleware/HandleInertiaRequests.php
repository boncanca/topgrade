<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use App\Settings\ClubSettings;
use App\Settings\SiteSettings;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $siteSettings = app(SiteSettings::class);
        $clubSettings = app(ClubSettings::class);

        return [
            ...parent::share($request),
            'name' => $siteSettings->site_name ?? config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'siteSettings' => [
                'site_name' => $siteSettings->site_name,
                'site_url' => $siteSettings->site_url,
                'tagline' => $siteSettings->tagline,
                'default_og_image' => $siteSettings->default_og_image,
                'locale' => $siteSettings->locale,
                'timezone' => $siteSettings->timezone,
            ],
            'clubSettings' => [
                'club_name' => $clubSettings->club_name,
                'legal_name' => $clubSettings->legal_name,
                'company_number' => $clubSettings->company_number,
                'address' => $clubSettings->address,
                'email' => $clubSettings->email,
                'phone' => $clubSettings->phone,
                'instagram_url' => $clubSettings->instagram_url,
                'instagram_handle' => $clubSettings->instagram_handle,
                'logo_path' => $clubSettings->logo_path,
            ],
            'headerMenu' => Menu::where('slug', 'main-navigation')->with('allItems')->first()
                ?? Menu::whereIn('location', ['main', 'header'])->with('allItems')->first(),
            'footerMenu' => Menu::where('slug', 'footer-navigation')->with('allItems')->first()
                ?? Menu::where('location', 'footer')->with('allItems')->first(),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
