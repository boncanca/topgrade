<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Clock, MapPin, ArrowRight, CheckCircle2 } from '@lucide/vue';
import { computed } from 'vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

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

interface WeeklyScheduleItem {
    days: string;
    badge: string;
    sessions: Array<{ age: string; time: string }>;
    venue: string;
    address: string;
    surface: string;
}

const props = defineProps<{
    activities: Activity[];
    weeklySchedule?: WeeklyScheduleItem[];
}>();

defineOptions({
    layout: PublicLayout,
});

const defaultWeeklySchedule: WeeklyScheduleItem[] = [
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
        sessions: [{ age: 'U7 – U12', time: '5:30 PM – 7:00 PM' }],
        venue: 'Tottenham Community Sports Centre',
        address: '701–703 High Road, London N17 8AD',
        surface: 'Sports Centre Training Facility',
    },
];

const weeklySchedule = computed(() => {
    return props.weeklySchedule && props.weeklySchedule.length > 0
        ? props.weeklySchedule
        : defaultWeeklySchedule;
});
</script>

<template>
    <SeoHead
        title="Football Training | TopGrade London FC"
        description="Weekly youth football training timetable for U7 to U16 players at Frederick Knight Sports Centre and Tottenham Community Sports Centre in London."
        path="/training"
    />

    <div class="bg-tg-bg text-tg-text min-h-screen">
        <!-- Hero Section: Clean, Solid Sports Presentation -->
        <section
            class="border-tg-border relative flex min-h-[36vh] items-end overflow-hidden border-b pb-12 pt-24 sm:min-h-[40vh] sm:pt-28"
        >
            <div
                class="absolute inset-0 bg-cover bg-center opacity-35"
                style="
                    background-image: url('/images/club/training-agility-drills.jpg');
                "
            />
            <div
                class="from-tg-bg via-tg-bg/85 absolute inset-0 bg-gradient-to-t to-transparent"
            />

            <div
                class="relative z-10 mx-auto w-full max-w-7xl space-y-3 px-4 sm:px-6 lg:px-8"
            >
                <span
                    class="rounded-xs bg-tg-bg-deep border-tg-border text-tg-accent inline-block border px-3 py-1 text-xs font-bold uppercase tracking-widest"
                >
                    Club Schedule
                </span>

                <h1
                    class="text-tg-text-strong text-3xl font-normal uppercase tracking-tight sm:text-5xl"
                    style="font-family: var(--tg-display)"
                >
                    Weekly Training Schedule
                </h1>
                <p class="text-tg-text max-w-2xl text-sm sm:text-base">
                    Structured midweek squad training sessions across North and
                    East London for youth players aged U7 to U16.
                </p>
            </div>
        </section>

        <!-- Official Weekly Schedule Breakdown -->
        <section
            class="border-tg-border mx-auto max-w-7xl border-b px-4 py-16 sm:px-6 lg:px-8"
        >
            <div class="border-tg-border mb-10 max-w-2xl border-b pb-4">
                <span
                    class="text-tg-accent mb-1 block text-xs font-bold uppercase tracking-widest"
                >
                    Timetable
                </span>
                <h2
                    class="text-tg-text-strong text-2xl font-normal uppercase tracking-tight sm:text-3xl"
                    style="font-family: var(--tg-display)"
                >
                    Training Days & Times
                </h2>
            </div>

            <div class="grid gap-8 md:grid-cols-2">
                <div
                    v-for="(sched, idx) in weeklySchedule"
                    :key="idx"
                    class="bg-tg-bg-deep/80 border-tg-border flex flex-col justify-between space-y-6 rounded-sm border p-6 sm:p-8"
                >
                    <div class="space-y-5">
                        <div
                            class="border-tg-border flex items-center justify-between border-b pb-4"
                        >
                            <h3
                                class="text-tg-text-strong text-2xl font-normal uppercase"
                                style="font-family: var(--tg-display)"
                            >
                                {{ sched.days }}
                            </h3>
                            <span
                                class="bg-tg-bg text-tg-accent border-tg-border rounded-xs border px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider"
                            >
                                {{ sched.badge }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            <span
                                class="text-tg-text-muted block text-xs font-bold uppercase tracking-wider"
                                >Session Hours</span
                            >
                            <div
                                v-for="(session, sIdx) in sched.sessions"
                                :key="sIdx"
                                class="border-tg-border/60 flex items-center justify-between border-b py-2"
                            >
                                <span
                                    class="text-tg-text-strong text-base font-bold"
                                    >{{ session.age }}</span
                                >
                                <span
                                    class="text-tg-accent text-sm font-semibold"
                                    >{{ session.time }}</span
                                >
                            </div>
                        </div>

                        <div
                            class="rounded-xs bg-tg-bg border-tg-border space-y-1 border p-4 text-xs"
                        >
                            <div class="text-tg-text-strong text-sm font-bold">
                                {{ sched.venue }}
                            </div>
                            <div
                                class="text-tg-text-muted flex items-start gap-1.5"
                            >
                                <MapPin
                                    class="text-tg-accent mt-0.5 h-3.5 w-3.5 shrink-0"
                                />
                                <span>{{ sched.address }}</span>
                            </div>
                            <div class="text-tg-text-muted pt-1">
                                <span class="text-tg-text font-bold"
                                    >Surface:</span
                                >
                                {{ sched.surface }}
                            </div>
                        </div>
                    </div>

                    <div class="border-tg-border border-t pt-4">
                        <Link
                            href="/bookings/free-trial-session"
                            class="tg-btn w-full justify-center text-xs"
                        >
                            <span>Book an Introductory Trial</span>
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </div>

            <div
                class="rounded-xs bg-tg-bg-deep/80 border-tg-border text-tg-text-muted mt-8 flex items-center gap-3 border p-4 text-xs"
            >
                <CheckCircle2 class="text-tg-accent h-4 w-4 shrink-0" />
                <span
                    >All players must arrive 10 minutes prior to session
                    kick-off. Boots and shin pads are mandatory for all training
                    sessions.</span
                >
            </div>
        </section>

        <!-- Bookable Squad Sessions -->
        <section
            v-if="activities && activities.length"
            class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8"
        >
            <div class="border-tg-border mb-10 max-w-2xl border-b pb-4">
                <span
                    class="text-tg-accent mb-1 block text-xs font-bold uppercase tracking-widest"
                >
                    Bookings
                </span>
                <h2
                    class="text-tg-text-strong text-2xl font-normal uppercase tracking-tight sm:text-3xl"
                    style="font-family: var(--tg-display)"
                >
                    Squad Training Sessions
                </h2>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="activity in activities"
                    :key="activity.id"
                    class="bg-tg-bg-deep/80 border-tg-border hover:border-tg-border-strong flex flex-col justify-between space-y-6 rounded-sm border p-6 transition-all"
                >
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span
                                class="rounded-xs bg-tg-bg text-tg-text-muted border-tg-border flex items-center gap-1 border px-2.5 py-1 text-xs font-medium"
                            >
                                <Clock class="text-tg-accent h-3.5 w-3.5" />
                                <span
                                    >{{ activity.duration_minutes }} mins</span
                                >
                            </span>
                            <span
                                v-if="activity.slug.includes('trial')"
                                class="rounded-xs bg-tg-accent text-tg-text-strong px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider"
                            >
                                Free Trial
                            </span>
                            <span
                                v-else
                                class="rounded-xs bg-tg-bg border-tg-border text-tg-text-muted border px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wider"
                            >
                                Club Training
                            </span>
                        </div>

                        <h3
                            class="text-tg-text-strong text-xl font-normal uppercase tracking-tight"
                            style="font-family: var(--tg-display)"
                        >
                            {{ activity.name }}
                        </h3>

                        <p
                            class="text-tg-text-muted line-clamp-3 text-xs leading-relaxed"
                        >
                            {{ activity.description }}
                        </p>
                    </div>

                    <div class="border-tg-border space-y-4 border-t pt-4">
                        <div
                            class="text-tg-text-muted flex items-center gap-2 text-xs"
                        >
                            <MapPin
                                class="text-tg-accent h-3.5 w-3.5 shrink-0"
                            />
                            <span class="truncate">{{
                                activity.location
                            }}</span>
                        </div>

                        <Link
                            :href="`/bookings/${activity.slug}`"
                            class="tg-btn w-full justify-center text-xs"
                        >
                            <span>{{
                                activity.slug.includes('trial')
                                    ? 'Book a Trial'
                                    : 'Book Session'
                            }}</span>
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
