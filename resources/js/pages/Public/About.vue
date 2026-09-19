<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { ArrowRight, Trophy, Users, Target, MapPin, Award } from '@lucide/vue';

defineOptions({
    layout: PublicLayout,
});

const values = [
    {
        title: 'Player-First Development',
        description: 'Every drill, session plan, and coaching instruction focuses on technical proficiency and long-term player progression.',
        icon: Target,
    },
    {
        title: 'Disciplined Coaching Standards',
        description: 'Dedicated coaches committed to age-appropriate training, disciplined player habits, positive reinforcement, and tactical understanding.',
        icon: Award,
    },
    {
        title: 'London Community Roots',
        description: 'Operating as a non-profit Community Interest Company (CIC) dedicated to structured youth football in North and East London.',
        icon: Users,
    },
    {
        title: 'Competitive League Matches',
        description: 'Sanctioned London youth league participation, tournament showcases, and competitive weekend fixtures.',
        icon: Trophy,
    },
];

const facilities = [
    {
        name: 'Frederick Knight Sports Centre',
        subtext: 'Tottenham Powerleague',
        venue: 'Willoughby Lane, Tottenham, London N17 0RT',
        surface: 'All-Weather 3G Floodlit Pitches',
        details: 'Midweek squad training sessions (Tuesdays & Thursdays) and competitive home match fixtures.',
    },
    {
        name: 'Tottenham Community Sports Centre',
        subtext: 'High Road Sports Facility',
        venue: '701–703 High Road, London N17 8AD',
        surface: 'Sports Centre Training Facility',
        details: 'Wednesday evening squad training sessions for junior age groups.',
    },
    {
        name: 'Mabley Green Park',
        subtext: 'Homerton, Hackney',
        venue: 'Lee Conservancy Road, London E9 5HW',
        surface: 'Grass & 3G Matchday Pitches',
        details: 'Weekend home league fixtures and squad match play.',
    },
    {
        name: 'Hackney Marshes',
        subtext: 'Homerton Road, Hackney',
        venue: 'Homerton Road, London E9 5PF',
        surface: 'Natural Grass Matchday Pitches',
        details: 'Saturday youth league matches, tournament fixtures, and inter-club friendlies.',
    },
];
</script>

<template>
    <Head title="About the Club — TopGrade London FC" />

    <div class="min-h-screen bg-tg-bg text-tg-text">
        <!-- Hero Section: Clean, Solid Sports Presentation -->
        <section class="relative min-h-[36vh] sm:min-h-[42vh] flex items-end overflow-hidden pb-12 pt-24 sm:pt-28 border-b border-tg-border">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-35"
                style="background-image: url('/images/club/tactical_coaching.jpg')"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-tg-bg via-tg-bg/85 to-transparent" />

            <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-3">
                <span class="inline-block px-3 py-1 rounded-xs bg-tg-bg-deep border border-tg-border text-tg-accent text-xs font-bold tracking-widest uppercase">
                    About the Club
                </span>

                <h1
                    class="text-4xl sm:text-6xl font-normal text-tg-text-strong uppercase tracking-tight leading-tight max-w-3xl"
                    style="font-family: var(--tg-display);"
                >
                    London Youth Football Development
                </h1>
                <p class="text-tg-text text-base sm:text-lg max-w-2xl leading-relaxed">
                    TopGrade London FC is a grassroots Community Interest Company (CIC) delivering structured coaching, teamwork, and competitive playing pathways across Tottenham and Hackney.
                </p>
            </div>
        </section>

        <!-- Club Environment -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-tg-bg-deep/60 border-b border-tg-border">
            <div class="max-w-7xl mx-auto grid gap-12 lg:grid-cols-2 items-center">
                <div class="space-y-5">
                    <span class="text-xs font-bold uppercase tracking-widest text-tg-accent block">
                        Club Environment
                    </span>
                    <h2
                        class="text-3xl sm:text-4xl font-normal text-tg-text-strong uppercase tracking-tight"
                        style="font-family: var(--tg-display);"
                    >
                        Developing Footballers, Building Character
                    </h2>
                    <p class="text-tg-text text-sm leading-relaxed">
                        At TopGrade London FC, youth football is focused on discipline, work ethic, spatial intelligence, and teamwork.
                    </p>
                    <p class="text-tg-text-muted text-sm leading-relaxed">
                        Our squads train weekly under dedicated coaches who emphasize ball control, quick thinking, and competitive bravery. Training takes place at the Frederick Knight Sports Centre and Tottenham Community Sports Centre, with home match play at Frederick Knight, Mabley Green Park, and Hackney Marshes.
                    </p>

                    <div class="pt-2 flex flex-wrap items-center gap-3">
                        <Link
                            href="/bookings/free-trial-session"
                            class="tg-btn text-xs"
                        >
                            <span>Book a Trial</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                        <Link
                            href="/contact"
                            class="tg-btn ghost text-xs"
                        >
                            <span>Contact the Club</span>
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-sm overflow-hidden border border-tg-border aspect-4/3 bg-tg-bg-deep">
                        <img
                            src="/images/club/youth_match_action.jpg"
                            alt="TopGrade London FC match action"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div class="rounded-sm overflow-hidden border border-tg-border aspect-4/3 bg-tg-bg-deep">
                        <img
                            src="/images/club/club-coach-mentoring.jpg"
                            alt="Coach mentoring TopGrade player"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div class="rounded-sm overflow-hidden border border-tg-border aspect-4/3 bg-tg-bg-deep">
                        <img
                            src="/images/club/training_pitch_evening.jpg"
                            alt="Floodlit training drills"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div class="rounded-sm overflow-hidden border border-tg-border aspect-4/3 bg-tg-bg-deep">
                        <img
                            src="/images/club/squad_celebration.jpg"
                            alt="Squad goal celebration"
                            class="w-full h-full object-cover"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- Core Club Values -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-tg-bg border-b border-tg-border">
            <div class="max-w-7xl mx-auto">
                <div class="max-w-2xl mb-12 pb-4 border-b border-tg-border">
                    <span class="text-xs font-bold uppercase tracking-widest text-tg-accent block mb-1">
                        Club Principles
                    </span>
                    <h2
                        class="text-3xl sm:text-4xl font-normal text-tg-text-strong uppercase tracking-tight"
                        style="font-family: var(--tg-display);"
                    >
                        What Defines TopGrade
                    </h2>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="item in values"
                        :key="item.title"
                        class="p-6 rounded-sm border border-tg-border bg-tg-bg-deep/80 space-y-4"
                    >
                        <div class="w-10 h-10 rounded-xs bg-tg-bg border border-tg-border flex items-center justify-center text-tg-accent">
                            <component :is="item.icon" class="w-5 h-5" />
                        </div>
                        <h3
                            class="text-lg font-normal text-tg-text-strong uppercase tracking-tight"
                            style="font-family: var(--tg-display);"
                        >
                            {{ item.title }}
                        </h3>
                        <p class="text-tg-text-muted text-xs leading-relaxed">
                            {{ item.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Official Grounds & Pitch Locations -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-tg-bg-deep/60 border-b border-tg-border">
            <div class="max-w-7xl mx-auto">
                <div class="max-w-2xl mb-12 pb-4 border-b border-tg-border">
                    <span class="text-xs font-bold uppercase tracking-widest text-tg-accent block mb-1">
                        Locations
                    </span>
                    <h2
                        class="text-3xl sm:text-4xl font-normal text-tg-text-strong uppercase tracking-tight"
                        style="font-family: var(--tg-display);"
                    >
                        Where We Train &amp; Play
                    </h2>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div
                        v-for="f in facilities"
                        :key="f.name"
                        class="p-6 rounded-sm bg-tg-bg border border-tg-border space-y-3"
                    >
                        <div>
                            <h3
                                class="text-lg font-normal text-tg-text-strong uppercase tracking-tight"
                                style="font-family: var(--tg-display);"
                            >
                                {{ f.name }}
                            </h3>
                            <span class="text-xs text-tg-accent font-medium">{{ f.subtext }}</span>
                        </div>
                        <div class="flex items-start gap-2 text-xs text-tg-text">
                            <MapPin class="w-4 h-4 text-tg-accent shrink-0 mt-0.5" />
                            <span>{{ f.venue }}</span>
                        </div>
                        <p class="text-xs text-tg-text-muted">
                            <span class="font-bold text-tg-text">Surface:</span> {{ f.surface }}
                        </p>
                        <p class="text-xs text-tg-text-muted leading-relaxed pt-1">
                            {{ f.details }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Banner: Solid, Clean -->
        <section class="relative py-24 px-4 sm:px-6 lg:px-8 bg-tg-bg-deep text-center border-t border-tg-border overflow-hidden">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-25"
                style="background-image: url('/images/club/team-huddle.jpg')"
            />
            <div class="absolute inset-0 bg-black/70" />

            <div class="relative z-10 max-w-3xl mx-auto space-y-5">
                <h2
                    class="text-3xl sm:text-4xl font-normal text-white uppercase tracking-tight"
                    style="font-family: var(--tg-display);"
                >
                    Join TopGrade London FC
                </h2>
                <p class="text-tg-paper text-sm sm:text-base max-w-xl mx-auto leading-relaxed opacity-90">
                    Come see our coaches in action. Book an introductory trial session for your child at our London training ground.
                </p>
                <div class="pt-2">
                    <Link
                        href="/bookings/free-trial-session"
                        class="tg-btn text-xs"
                    >
                        <span>Book an Introductory Trial</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
