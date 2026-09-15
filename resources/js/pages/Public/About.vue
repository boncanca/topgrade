<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { ArrowRight, Trophy, Users, Target, MapPin, Award, CheckCircle } from '@lucide/vue';

defineOptions({
    layout: PublicLayout,
});

const values = [
    {
        title: 'Player-First Development',
        description: 'Every drill, match instruction, and session plan is focused on the long-term technical and personal development of each child.',
        icon: Target,
    },
    {
        title: 'High Coaching Standards',
        description: 'Coaches committed to age-appropriate training, disciplined player habits, positive reinforcement, and tactical game understanding.',
        icon: Award,
    },
    {
        title: 'London Community Roots',
        description: 'Operating as a non-profit Community Interest Company (CIC) to ensure football opportunities are accessible to youth across London.',
        icon: Users,
    },
    {
        title: 'Genuine Match Opportunity',
        description: 'Active league competition in sanctioned London youth leagues, tournament showcases, and talent ID festival fixtures.',
        icon: Trophy,
    },
];

const facilities = [
    {
        name: 'Tottenham Training Base',
        venue: 'Fredrick Knight Sports Ground / Willoughby Lane, N17',
        surface: '3G Floodlit Astroturf',
        details: 'Midweek technical training sessions, ball mastery clinics, and squad practice.',
    },
    {
        name: 'Hackney Marshes Match Grounds',
        venue: 'Homerton Road, Hackney, London E9',
        surface: 'Natural Grass League Pitches',
        details: 'Saturday morning youth league matches, inter-club friendlies, and festival games.',
    },
];
</script>

<template>
    <Head title="About the Club — TopGrade London FC" />

    <div class="min-h-screen bg-slate-950 text-white">
        <!-- Hero Section (Light, Vibrant Overlay + Motion Entrance) -->
        <section class="relative min-h-[55vh] flex items-end overflow-hidden pb-16 pt-32 border-b border-slate-800">
            <div
                class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-75 animate-ken-burns"
                style="background-image: url('/images/club/tactical_coaching.jpg')"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 to-slate-950/20" />
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/60 via-transparent to-slate-950/60" />

            <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-4">
                <div class="animate-fade-in-up [animation-delay:100ms] inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-slate-900/80 border border-purple-500/40 text-purple-200 text-xs font-bold uppercase tracking-wider backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse" />
                    Our Story & Mission
                </div>

                <h1 class="animate-fade-in-up [animation-delay:250ms] text-4xl sm:text-6xl font-extrabold text-white tracking-tight leading-tight max-w-3xl drop-shadow-md">
                    A London football club built for development.
                </h1>
                <p class="animate-fade-in-up [animation-delay:400ms] text-slate-100 text-lg sm:text-xl max-w-2xl leading-relaxed drop-shadow-sm font-normal">
                    TopGrade London FC is a grassroots Community Interest Company (CIC) providing young players with structured coaching, teamwork, and competitive playing pathways.
                </p>
            </div>
        </section>

        <!-- Club Overview & Philosophy -->
        <section class="py-24 px-4 sm:px-6 lg:px-8 bg-slate-900/60 border-b border-slate-800">
            <div class="max-w-7xl mx-auto grid gap-14 lg:grid-cols-2 items-center">
                <div class="space-y-6">
                    <span class="text-xs font-bold uppercase tracking-widest text-[var(--brand-secondary)] block">
                        Our Environment
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                        Developing footballers, building character.
                    </h2>
                    <p class="text-slate-300 text-base leading-relaxed">
                        At TopGrade London FC, we believe youth football is more than just kicking a ball. It is a classroom for discipline, resilience, spatial intelligence, and lifelong friendship.
                    </p>
                    <p class="text-slate-300 text-base leading-relaxed">
                        Our squads train weekly under dedicated coaches who emphasize ball control, quick thinking, and competitive bravery. Whether competing on the grass pitches of Hackney Marshes or training under the floodlights in Tottenham, every player receives the attention they need to progress.
                    </p>

                    <div class="pt-4 flex items-center gap-4">
                        <Link
                            href="/bookings"
                            class="inline-flex items-center gap-2 px-6 py-3.5 bg-[var(--brand-primary)] hover:bg-purple-700 text-white font-semibold rounded-xl transition-all shadow-md"
                        >
                            <span>Book a Trial Session</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                        <Link
                            href="/contact"
                            class="inline-flex items-center gap-2 px-6 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl border border-slate-700 transition-colors text-sm"
                        >
                            <span>Contact Coaches</span>
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="rounded-2xl overflow-hidden border border-slate-800 shadow-lg aspect-4/3">
                            <img
                                src="/images/club/youth_match_action.jpg"
                                alt="TopGrade London FC match action"
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <div class="rounded-2xl overflow-hidden border border-slate-800 shadow-lg aspect-square">
                            <img
                                src="/images/club/club-coach-mentoring.jpg"
                                alt="Coach mentoring TopGrade player"
                                class="w-full h-full object-cover"
                            />
                        </div>
                    </div>
                    <div class="space-y-4 pt-8">
                        <div class="rounded-2xl overflow-hidden border border-slate-800 shadow-lg aspect-square">
                            <img
                                src="/images/club/training_pitch_evening.jpg"
                                alt="Floodlit training drills"
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <div class="rounded-2xl overflow-hidden border border-slate-800 shadow-lg aspect-4/3">
                            <img
                                src="/images/club/squad_celebration.jpg"
                                alt="Squad goal celebration"
                                class="w-full h-full object-cover"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Core Club Values -->
        <section class="py-24 px-4 sm:px-6 lg:px-8 bg-slate-950">
            <div class="max-w-7xl mx-auto">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="text-xs font-bold uppercase tracking-widest text-[var(--brand-secondary)] block mb-2">
                        Club Standards
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white">
                        What Defines TopGrade London FC
                    </h2>
                    <p class="text-slate-400 text-sm mt-2">
                        The core principles that guide our training sessions, matchdays, and squad culture.
                    </p>
                </div>

                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="item in values"
                        :key="item.title"
                        class="p-7 rounded-2xl border border-slate-800 bg-slate-900/80 flex flex-col justify-between space-y-4 hover:border-purple-500/40 transition-colors"
                    >
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-purple-900/40 border border-purple-500/30 flex items-center justify-center text-purple-300 mb-5">
                                <component :is="item.icon" class="w-6 h-6" />
                            </div>
                            <h3 class="text-xl font-bold text-white mb-2">
                                {{ item.title }}
                            </h3>
                            <p class="text-slate-400 text-sm leading-relaxed">
                                {{ item.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Facilities & London Locations -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900/70 border-t border-slate-800">
            <div class="max-w-6xl mx-auto">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="text-xs font-bold uppercase tracking-widest text-[var(--brand-secondary)] block mb-2">
                        Pitch Locations
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white">
                        Where We Train & Play
                    </h2>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div
                        v-for="f in facilities"
                        :key="f.name"
                        class="p-8 rounded-2xl bg-slate-950 border border-slate-800 space-y-4"
                    >
                        <h3 class="text-xl font-bold text-white">{{ f.name }}</h3>
                        <div class="flex items-start gap-2 text-sm text-slate-300">
                            <MapPin class="w-4 h-4 text-purple-400 shrink-0 mt-0.5" />
                            <span>{{ f.venue }}</span>
                        </div>
                        <p class="text-xs text-purple-300 font-semibold uppercase tracking-wider">
                            Surface: {{ f.surface }}
                        </p>
                        <p class="text-sm text-slate-400 leading-relaxed">
                            {{ f.details }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action Banner (Clear, Visible Team Huddle Image) -->
        <section class="relative py-24 px-4 sm:px-6 lg:px-8 bg-slate-950 text-center border-t border-slate-800 overflow-hidden">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-65 animate-ken-burns"
                style="background-image: url('/images/club/team-huddle.jpg')"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-slate-950/30" />
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/50 via-transparent to-slate-950/50" />

            <div class="relative z-10 max-w-3xl mx-auto space-y-6">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white drop-shadow-md">
                    Experience TopGrade London FC
                </h2>
                <p class="text-slate-100 text-base max-w-xl mx-auto leading-relaxed drop-shadow-sm font-normal">
                    Come see our coaches in action. Book an introductory trial session for your child at our London training ground.
                </p>
                <div class="pt-4">
                    <Link
                        href="/bookings"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-[var(--brand-primary)] hover:bg-purple-700 text-white font-semibold rounded-xl transition-all duration-300 shadow-xl shadow-purple-950/60 hover:shadow-purple-600/40 hover:-translate-y-1"
                    >
                        <span>Book a Trial</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
