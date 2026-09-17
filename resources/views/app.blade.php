<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <meta name="description" content="TopGrade London FC is a London youth football club providing structured training, technical coaching, and league match play for ages U7 to U16 in Tottenham and Hackney.">
        <meta name="keywords" content="TopGrade London FC, youth football London, football training Tottenham, youth football Hackney, Tottenham Powerleague, London FA youth club, football trials London">
        <meta name="author" content="TopGrade London FC">
        <meta name="theme-color" content="#4c1d95">
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- OpenGraph Meta -->
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="TopGrade London FC">
        <meta property="og:title" content="TopGrade London FC — Official Youth Football Club">
        <meta property="og:description" content="Structured youth football coaching, training, and competitive match play for young players in Tottenham (N17) and Hackney (E9), London.">
        <meta property="og:image" content="{{ asset('images/club/hero-football.jpg') }}">
        <meta property="og:url" content="{{ url()->current() }}">

        <!-- Twitter Card Meta -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="TopGrade London FC — Official Youth Football Club">
        <meta name="twitter:description" content="Structured youth football coaching, training, and competitive match play for young players in Tottenham (N17) and Hackney (E9), London.">
        <meta name="twitter:image" content="{{ asset('images/club/hero-football.jpg') }}">

        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        <x-inertia::head>
            <title>{{ config('app.name', 'TopGrade London FC') }}</title>
        </x-inertia::head>
    </head>
    <body class="font-sans antialiased">
        <x-inertia::app />
    </body>
</html>
