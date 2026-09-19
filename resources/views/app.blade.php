<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Canonical dark theme background --}}
        <style>
            html {
                background-color: #08040f;
                color-scheme: dark;
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
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="manifest" href="/site.webmanifest">

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
