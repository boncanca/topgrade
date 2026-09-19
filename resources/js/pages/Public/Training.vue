<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Clock, MapPin, ArrowRight, CheckCircle2 } from '@lucide/vue';

interface Activity {
    id: number;
    name: string;
    slug: string;
    description: string;
    duration_minutes: number;
    location: string;
    price: string;
    currency: string;
}

defineProps<{
    activities: Activity[];
}>();

defineOptions({
    layout: PublicLayout,
});

const weeklySchedule = [
    {
        days: 'Tuesdays & Thursdays',
        badge: 'Midweek Squad Training',
        sessions: [
            { age: 'U7 – U12', time: '5:00 PM – 7:00 PM' },
            { age: 'U13 – U16', time: '6:30 PM – 8:00 PM' },
        ],
        venue: 'Frederick Knight Sports Centre (Tottenham Powerleague)',
        address: 'Willoughby Lane, Tottenham, London N17 0RT',
        surface: 'All-Weather 3G Floodlit Pitches',
    },
    {
        days: 'Wednesdays',
        badge: 'Midweek Squad Training',
        sessions: [
            { age: 'U7 – U12', time: '5:30 PM – 7:00 PM' },
        ],
        venue: 'Tottenham Community Sports Centre',
        address: '701–703 High Road, London N17 8AD',
        surface: 'Sports Centre Training Facility',
    },
];
</script>

<template>
    <Head title="Training Schedule & Venues — TopGrade London FC" />

    <div class="min-h-screen bg-tg-bg text-tg-text">
        <!-- Hero Section: Clean, Solid Sports Presentation -->
        <section class="relative min-h-[36vh] sm:min-h-[40vh] flex items-end overflow-hidden pb-12 pt-24 sm:pt-28 border-b border-tg-border">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-35"
                style="background-image: url('/images/club/training-agility-drills.jpg')"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-tg-bg via-tg-bg/85 to-transparent" />

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-3">
                <span class="inline-block px-3 py-1 rounded-xs bg-tg-bg-deep border border-tg-border text-tg-accent text-xs font-bold tracking-widest uppercase">
                    Club Schedule
                </span>

                <h1
                    class="text-3xl sm:text-5xl font-normal uppercase tracking-tight text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    Weekly Training Schedule
                </h1>
                <p class="text-tg-text text-sm sm:text-base max-w-2xl">
                    Structured midweek squad training sessions across North and East London for youth players aged U7 to U16.
                </p>
            </div>
        </section>

        <!-- Official Weekly Schedule Breakdown -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto border-b border-tg-border">
            <div class="max-w-2xl mb-10 pb-4 border-b border-tg-border">
                <span class="text-xs font-bold uppercase tracking-widest text-tg-accent block mb-1">
                    Timetable
                </span>
                <h2
                    class="text-2xl sm:text-3xl font-normal uppercase tracking-tight text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    Training Days & Times
                </h2>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div
                    v-for="(sched, idx) in weeklySchedule"
                    :key="idx"
                    class="p-6 sm:p-8 rounded-sm bg-tg-bg-deep/80 border border-tg-border flex flex-col justify-between space-y-6"
                >
                    <div class="space-y-5">
                        <div class="flex items-center justify-between pb-4 border-b border-tg-border">
                            <h3
                                class="text-2xl font-normal uppercase text-tg-text-strong"
                                style="font-family: var(--tg-display);"
                            >
                                {{ sched.days }}
                            </h3>
                            <span class="px-2.5 py-1 bg-tg-bg text-tg-accent border border-tg-border rounded-xs text-[11px] font-bold uppercase tracking-wider">
                                {{ sched.badge }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-tg-text-muted block">Session Hours</span>
                            <div
                                v-for="(session, sIdx) in sched.sessions"
                                :key="sIdx"
                                class="flex items-center justify-between py-2 border-b border-tg-border/60"
                            >
                                <span class="font-bold text-tg-text-strong text-base">{{ session.age }}</span>
                                <span class="text-sm font-semibold text-tg-accent">{{ session.time }}</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-xs bg-tg-bg border border-tg-border space-y-1 text-xs">
                            <div class="font-bold text-tg-text-strong text-sm">{{ sched.venue }}</div>
                            <div class="flex items-start gap-1.5 text-tg-text-muted">
                                <MapPin class="w-3.5 h-3.5 text-tg-accent shrink-0 mt-0.5" />
                                <span>{{ sched.address }}</span>
                            </div>
                            <div class="text-tg-text-muted pt-1">
                                <span class="font-bold text-tg-text">Surface:</span> {{ sched.surface }}
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-tg-border">
                        <Link
                            href="/bookings/free-trial-session"
                            class="tg-btn w-full justify-center text-xs"
                        >
                            <span>Book an Introductory Trial</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>
            </div>

            <div class="mt-8 p-4 rounded-xs bg-tg-bg-deep/80 border border-tg-border flex items-center gap-3 text-xs text-tg-text-muted">
                <CheckCircle2 class="w-4 h-4 text-tg-accent shrink-0" />
                <span>All players must arrive 10 minutes prior to session kick-off. Boots and shin pads are mandatory for all training sessions.</span>
            </div>
        </section>

        <!-- Bookable Squad Sessions -->
        <section v-if="activities && activities.length" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="max-w-2xl mb-10 pb-4 border-b border-tg-border">
                <span class="text-xs font-bold uppercase tracking-widest text-tg-accent block mb-1">
                    Bookings
                </span>
                <h2
                    class="text-2xl sm:text-3xl font-normal uppercase tracking-tight text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    Squad Training Sessions
                </h2>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="activity in activities"
                    :key="activity.id"
                    class="rounded-sm bg-tg-bg-deep/80 border border-tg-border p-6 flex flex-col justify-between space-y-6 transition-all hover:border-tg-border-strong"
                >
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="px-2.5 py-1 rounded-xs bg-tg-bg text-tg-text-muted border border-tg-border text-xs font-medium flex items-center gap-1">
                                <Clock class="w-3.5 h-3.5 text-tg-accent" />
                                <span>{{ activity.duration_minutes }} mins</span>
                            </span>
                            <span
                                v-if="activity.slug.includes('trial')"
                                class="px-2.5 py-1 rounded-xs bg-tg-accent text-tg-text-strong text-[11px] font-bold uppercase tracking-wider"
                            >
                                Free Trial
                            </span>
                            <span
                                v-else
                                class="px-2.5 py-1 rounded-xs bg-tg-bg border border-tg-border text-tg-text-muted text-[11px] font-semibold uppercase tracking-wider"
                            >
                                Club Training
                            </span>
                        </div>

                        <h3
                            class="text-xl font-normal uppercase tracking-tight text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            {{ activity.name }}
                        </h3>

                        <p class="text-tg-text-muted text-xs line-clamp-3 leading-relaxed">
                            {{ activity.description }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-tg-border space-y-4">
                        <div class="flex items-center gap-2 text-xs text-tg-text-muted">
                            <MapPin class="w-3.5 h-3.5 text-tg-accent shrink-0" />
                            <span class="truncate">{{ activity.location }}</span>
                        </div>

                        <Link
                            :href="`/bookings/${activity.slug}`"
                            class="tg-btn w-full justify-center text-xs"
                        >
                            <span>{{ activity.slug.includes('trial') ? 'Book a Trial' : 'Book Session' }}</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
