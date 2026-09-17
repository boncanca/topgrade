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

function formatPrice(priceStr: string, currencyStr: string): string {
    if (priceStr === '0' || priceStr === '0.00') {
        return 'FREE TRIAL';
    }
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: currencyStr || 'GBP',
    }).format(parseFloat(priceStr));
}
</script>

<template>
    <Head title="Training Schedule & Venues — TopGrade London FC" />

    <div class="min-h-screen bg-slate-950 text-white">
        <!-- Hero Section: Clean, Solid Sports Presentation -->
        <section class="relative min-h-[35vh] flex items-end overflow-hidden pb-12 pt-28 border-b border-slate-800">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-50"
                style="background-image: url('/images/club/training-agility-drills.jpg')"
            />
            <div class="absolute inset-0 bg-black/70" />

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-3">
                <span class="inline-block px-3 py-1 rounded bg-purple-900/60 border border-purple-700/60 text-purple-200 text-xs font-bold tracking-widest uppercase">
                    Club Schedule
                </span>

                <h1 class="text-3xl sm:text-5xl font-extrabold text-white uppercase tracking-tight">
                    Weekly Training Schedule
                </h1>
                <p class="text-slate-300 text-base max-w-2xl">
                    Structured midweek squad training sessions across North and East London for youth players aged U7 to U16.
                </p>
            </div>
        </section>

        <!-- Official Weekly Schedule Breakdown -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto border-b border-slate-800">
            <div class="max-w-2xl mb-10 pb-4 border-b border-slate-800">
                <span class="text-xs font-bold uppercase tracking-widest text-purple-400 block mb-1">
                    Timetable
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white uppercase tracking-tight">
                    Training Days & Times
                </h2>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div
                    v-for="(sched, idx) in weeklySchedule"
                    :key="idx"
                    class="p-8 rounded bg-slate-900 border border-slate-800 flex flex-col justify-between space-y-6"
                >
                    <div class="space-y-5">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                            <h3 class="text-2xl font-extrabold text-white uppercase tracking-tight">
                                {{ sched.days }}
                            </h3>
                            <span class="px-2.5 py-1 bg-purple-950 text-purple-300 border border-purple-800/60 rounded text-[11px] font-bold uppercase tracking-wider">
                                {{ sched.badge }}
                            </span>
                        </div>

                        <div class="space-y-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Session Hours</span>
                            <div
                                v-for="(session, sIdx) in sched.sessions"
                                :key="sIdx"
                                class="flex items-center justify-between p-3.5 rounded bg-slate-950 border border-slate-800"
                            >
                                <span class="font-extrabold text-white text-base">{{ session.age }}</span>
                                <span class="text-sm font-semibold text-purple-300">{{ session.time }}</span>
                            </div>
                        </div>

                        <div class="p-4 rounded bg-slate-950 border border-slate-800 space-y-1 text-xs">
                            <div class="font-bold text-white text-sm">{{ sched.venue }}</div>
                            <div class="flex items-start gap-1.5 text-slate-400">
                                <MapPin class="w-3.5 h-3.5 text-purple-400 shrink-0 mt-0.5" />
                                <span>{{ sched.address }}</span>
                            </div>
                            <div class="text-slate-400 pt-1">
                                <span class="font-bold text-slate-300">Surface:</span> {{ sched.surface }}
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-800">
                        <Link
                            href="/bookings"
                            class="w-full py-3 px-4 bg-purple-800 hover:bg-purple-900 text-white text-xs font-bold uppercase tracking-wider rounded flex items-center justify-center gap-2 transition-colors"
                        >
                            <span>Book a Trial</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>
            </div>

            <div class="mt-8 p-4 rounded bg-slate-900 border border-slate-800 flex items-center gap-3 text-xs text-slate-400">
                <CheckCircle2 class="w-4 h-4 text-purple-400 shrink-0" />
                <span>All players must arrive 10 minutes prior to session kick-off. Boots and shin pads are mandatory for all training sessions.</span>
            </div>
        </section>

        <!-- Bookable Squad Sessions -->
        <section v-if="activities && activities.length" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="max-w-2xl mb-10 pb-4 border-b border-slate-800">
                <span class="text-xs font-bold uppercase tracking-widest text-purple-400 block mb-1">
                    Bookings
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white uppercase tracking-tight">
                    Squad Training Sessions
                </h2>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="activity in activities"
                    :key="activity.id"
                    class="rounded bg-slate-900 border border-slate-800 p-6 flex flex-col justify-between space-y-6"
                >
                    <div class="space-y-4">
                        <div class="flex justify-between items-start">
                            <span class="px-2.5 py-1 rounded bg-slate-950 text-slate-300 border border-slate-800 text-xs font-medium flex items-center gap-1">
                                <Clock class="w-3.5 h-3.5 text-purple-400" />
                                <span>{{ activity.duration_minutes }} mins</span>
                            </span>
                            <span class="text-base font-extrabold text-white">
                                {{ formatPrice(activity.price, activity.currency) }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-white uppercase tracking-tight">
                            {{ activity.name }}
                        </h3>

                        <p class="text-slate-400 text-xs line-clamp-3 leading-relaxed">
                            {{ activity.description }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-800 space-y-4">
                        <div class="flex items-center gap-2 text-xs text-slate-400">
                            <MapPin class="w-3.5 h-3.5 text-purple-400 shrink-0" />
                            <span class="truncate">{{ activity.location }}</span>
                        </div>

                        <Link
                            :href="`/bookings/${activity.slug}`"
                            class="w-full py-3 px-4 rounded bg-purple-800 hover:bg-purple-900 text-white text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 transition-colors"
                        >
                            <span>Book a Trial</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
