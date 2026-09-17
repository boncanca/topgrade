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

    <div class="min-h-screen bg-slate-950 text-white">
        <!-- Hero Section: Clean, Solid Sports Presentation -->
        <section class="relative min-h-[45vh] flex items-end overflow-hidden pb-14 pt-28 border-b border-slate-800">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-50"
                style="background-image: url('/images/club/tactical_coaching.jpg')"
            />
            <div class="absolute inset-0 bg-black/70" />

            <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-3">
                <span class="inline-block px-3 py-1 rounded bg-purple-900/60 border border-purple-700/60 text-purple-200 text-xs font-bold tracking-widest uppercase">
                    About the Club
                </span>

                <h1 class="text-4xl sm:text-6xl font-extrabold text-white uppercase tracking-tight leading-tight max-w-3xl">
                    London Youth Football Development
                </h1>
                <p class="text-slate-200 text-base sm:text-lg max-w-2xl leading-relaxed">
                    TopGrade London FC is a grassroots Community Interest Company (CIC) delivering structured coaching, teamwork, and competitive playing pathways across Tottenham and Hackney.
                </p>
            </div>
        </section>

        <!-- Club Environment -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900 border-b border-slate-800">
            <div class="max-w-7xl mx-auto grid gap-12 lg:grid-cols-2 items-center">
                <div class="space-y-5">
                    <span class="text-xs font-bold uppercase tracking-widest text-purple-400 block">
                        Club Environment
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white uppercase tracking-tight">
                        Developing Footballers, Building Character
                    </h2>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        At TopGrade London FC, youth football is focused on discipline, work ethic, spatial intelligence, and teamwork.
                    </p>
                    <p class="text-slate-300 text-sm leading-relaxed">
                        Our squads train weekly under dedicated coaches who emphasize ball control, quick thinking, and competitive bravery. Training takes place at the Frederick Knight Sports Centre and Tottenham Community Sports Centre, with home match play at Frederick Knight, Mabley Green Park, and Hackney Marshes.
                    </p>

                    <div class="pt-2 flex items-center gap-3">
                        <Link
                            href="/bookings"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-purple-800 hover:bg-purple-900 text-white font-bold uppercase tracking-wider text-xs rounded transition-colors"
                        >
                            <span>Book a Trial</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                        <Link
                            href="/contact"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold uppercase tracking-wider text-xs rounded border border-slate-700 transition-colors"
                        >
                            <span>Contact the Club</span>
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded overflow-hidden border border-slate-800 aspect-4/3 bg-slate-900">
                        <img
                            src="/images/club/youth_match_action.jpg"
                            alt="TopGrade London FC match action"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div class="rounded overflow-hidden border border-slate-800 aspect-4/3 bg-slate-900">
                        <img
                            src="/images/club/club-coach-mentoring.jpg"
                            alt="Coach mentoring TopGrade player"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div class="rounded overflow-hidden border border-slate-800 aspect-4/3 bg-slate-900">
                        <img
                            src="/images/club/training_pitch_evening.jpg"
                            alt="Floodlit training drills"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div class="rounded overflow-hidden border border-slate-800 aspect-4/3 bg-slate-900">
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
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-950 border-b border-slate-800">
            <div class="max-w-7xl mx-auto">
                <div class="max-w-2xl mb-12 pb-4 border-b border-slate-800">
                    <span class="text-xs font-bold uppercase tracking-widest text-purple-400 block mb-1">
                        Club Principles
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white uppercase tracking-tight">
                        What Defines TopGrade
                    </h2>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="item in values"
                        :key="item.title"
                        class="p-6 rounded border border-slate-800 bg-slate-900 space-y-4"
                    >
                        <div class="w-10 h-10 rounded bg-purple-950 border border-purple-800 flex items-center justify-center text-purple-300">
                            <component :is="item.icon" class="w-5 h-5" />
                        </div>
                        <h3 class="text-lg font-bold text-white uppercase tracking-tight">
                            {{ item.title }}
                        </h3>
                        <p class="text-slate-400 text-xs leading-relaxed">
                            {{ item.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Official Grounds & Pitch Locations -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900 border-b border-slate-800">
            <div class="max-w-7xl mx-auto">
                <div class="max-w-2xl mb-12 pb-4 border-b border-slate-800">
                    <span class="text-xs font-bold uppercase tracking-widest text-purple-400 block mb-1">
                        Locations
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white uppercase tracking-tight">
                        Where We Train & Play
                    </h2>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div
                        v-for="f in facilities"
                        :key="f.name"
                        class="p-6 rounded bg-slate-950 border border-slate-800 space-y-3"
                    >
                        <div>
                            <h3 class="text-lg font-bold text-white uppercase tracking-tight">{{ f.name }}</h3>
                            <span class="text-xs text-purple-300 font-medium">{{ f.subtext }}</span>
                        </div>
                        <div class="flex items-start gap-2 text-xs text-slate-300">
                            <MapPin class="w-4 h-4 text-purple-400 shrink-0 mt-0.5" />
                            <span>{{ f.venue }}</span>
                        </div>
                        <p class="text-xs text-slate-400">
                            <span class="font-bold text-slate-300">Surface:</span> {{ f.surface }}
                        </p>
                        <p class="text-xs text-slate-400 leading-relaxed pt-1">
                            {{ f.details }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Final CTA Banner: Solid, Clean -->
        <section class="relative py-24 px-4 sm:px-6 lg:px-8 bg-black text-center border-t border-slate-800 overflow-hidden">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-40"
                style="background-image: url('/images/club/team-huddle.jpg')"
            />
            <div class="absolute inset-0 bg-black/75" />

            <div class="relative z-10 max-w-3xl mx-auto space-y-5">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white uppercase tracking-tight">
                    Join TopGrade London FC
                </h2>
                <p class="text-slate-200 text-sm sm:text-base max-w-xl mx-auto leading-relaxed">
                    Come see our coaches in action. Book an introductory trial session for your child at our London training ground.
                </p>
                <div class="pt-2">
                    <Link
                        href="/bookings"
                        class="inline-flex items-center gap-2 px-8 py-3.5 bg-purple-800 hover:bg-purple-900 text-white font-bold uppercase tracking-wider text-xs rounded transition-colors"
                    >
                        <span>Book a Trial</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
