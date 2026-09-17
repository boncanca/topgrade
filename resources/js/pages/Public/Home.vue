<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import BlockRenderer from '@/components/CMS/BlockRenderer.vue';
import {
    ArrowRight,
    Trophy,
    Users,
    Target,
    MapPin,
    Calendar,
    Clock,
    Shield,
    CheckCircle2,
} from '@lucide/vue';

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

interface Block {
    id: number;
    uuid: string;
    type: string;
    payload: any;
    settings?: any;
}

interface Article {
    id: number;
    title: string;
    slug: string;
    excerpt?: string;
    published_at?: string;
}

interface PageContent {
    id: number;
    title: string;
    slug: string;
    excerpt?: string;
    content?: string;
}

const props = defineProps<{
    featuredActivities?: Activity[];
    articles?: Article[];
    page?: PageContent | null;
    blocks?: Block[];
}>();

defineOptions({
    layout: PublicLayout,
});

const quickStats = [
    { label: 'Age Groups', value: 'U7 – U16', detail: 'Boys & girls youth squads' },
    { label: 'Weekly Training', value: '3 Days', detail: 'Tuesdays, Wednesdays & Thursdays' },
    { label: 'Home Grounds', value: '3 Venues', detail: 'Tottenham & Hackney' },
    { label: 'Affiliation', value: 'London FA', detail: 'Sanctioned Youth Leagues' },
];

const trainingSchedule = [
    {
        days: 'Tuesdays & Thursdays',
        badge: 'Midweek Training',
        sessions: [
            { age: 'U7 – U12', time: '5:00 PM – 7:00 PM' },
            { age: 'U13 – U16', time: '6:30 PM – 8:00 PM' },
        ],
        venue: {
            name: 'Frederick Knight Sports Centre',
            facility: 'Tottenham Powerleague',
            address: 'Willoughby Lane, Tottenham, London N17 0RT',
            surface: 'All-Weather 3G Floodlit Pitches',
        },
    },
    {
        days: 'Wednesdays',
        badge: 'Midweek Training',
        sessions: [
            { age: 'U7 – U12', time: '5:30 PM – 7:00 PM' },
        ],
        venue: {
            name: 'Tottenham Community Sports Centre',
            facility: 'Community Sports Facility',
            address: '701–703 High Road, London N17 8AD',
            surface: 'Sports Centre Training Facility',
        },
    },
];

const homeVenues = [
    {
        name: 'Frederick Knight Sports Centre',
        subtext: 'Tottenham Powerleague',
        address: 'Willoughby Lane, Tottenham, London N17 0RT',
        role: 'Home Game Venue & Midweek Training Base',
        surface: '3G All-Weather Pitches',
    },
    {
        name: 'Mabley Green Park',
        subtext: 'Homerton, Hackney',
        address: 'Lee Conservancy Road, London E9 5HW',
        role: 'Home Game Venue',
        surface: 'Grass & 3G Matchday Pitches',
    },
    {
        name: 'Hackney Marshes',
        subtext: 'Homerton Road, Hackney',
        address: 'Homerton Road, London E9 5PF',
        role: 'Home Game Venue',
        surface: 'Grass Matchday Pitches',
    },
];

const squads = [
    {
        name: 'U7 – U8 Squad',
        stage: 'Foundation Phase',
        description: 'First touch, basic ball mastery, agility, and introducing team play in a positive environment.',
    },
    {
        name: 'U9 – U10 Squad',
        stage: 'Skill Acquisition',
        description: '1v1 attacking and defending, spatial awareness, passing range, and decision-making.',
    },
    {
        name: 'U11 – U12 Squad',
        stage: 'Game Development',
        description: 'Tactical positioning, transition play, set-pieces, and match tempo management.',
    },
    {
        name: 'U13 – U16 Squads',
        stage: 'Youth Competition',
        description: 'Tactical discipline, physical conditioning, competitive league fixtures, and match analysis.',
    },
];

const pillars = [
    {
        title: 'Technical Ball Mastery',
        description: 'Close control, first touch execution, both-feet passing, and 1v1 confidence developed in every training drill.',
        icon: Target,
    },
    {
        title: 'Tactical Game Understanding',
        description: 'Positional discipline, pitch vision, quick transitions, and decision-making under match tempo.',
        icon: Shield,
    },
    {
        title: 'Teamwork & Discipline',
        description: 'High work ethic, on-pitch communication, respect for referees and teammates, and squad accountability.',
        icon: Users,
    },
    {
        title: 'Competitive Match Play',
        description: 'Putting training drills into practice through sanctioned London youth league fixtures and tournaments.',
        icon: Trophy,
    },
];

const gallery = [
    {
        title: 'Youth League Match Action',
        location: 'Hackney Marshes Match Grounds',
        image: '/images/club/youth_match_action.jpg',
        alt: 'TopGrade London FC players in league match action',
    },
    {
        title: 'Evening Squad Training',
        location: 'Tottenham 3G Floodlit Pitches',
        image: '/images/club/training_pitch_evening.jpg',
        alt: 'TopGrade London FC squad training under floodlights',
    },
    {
        title: 'Tactical Pitchside Coaching',
        location: 'Coaches instructing players',
        image: '/images/club/tactical_coaching.jpg',
        alt: 'TopGrade coach explaining tactics pitchside',
    },
    {
        title: 'Squad Matchday Spirit',
        location: 'Team celebration after fixture',
        image: '/images/club/squad_celebration.jpg',
        alt: 'TopGrade London FC squad celebration',
    },
];
</script>

<template>
    <Head title="TopGrade London FC — Official Youth Football Club" />

    <div class="bg-slate-950 text-white selection:bg-purple-800 selection:text-white">
        <!-- 00 — OFFICIAL CLUB STATUS BAR -->
        <div class="bg-black border-b border-slate-800 text-xs py-2.5 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-3 text-slate-300">
                <div class="flex items-center gap-2.5">
                    <span class="inline-block w-2 h-2 rounded-full bg-purple-500" />
                    <span class="font-bold tracking-wider uppercase text-white">TopGrade London FC</span>
                    <span class="text-slate-600 hidden sm:inline">•</span>
                    <span class="text-slate-400 hidden sm:inline">North & East London Youth Football</span>
                </div>
                <div class="flex items-center gap-4 text-xs">
                    <span class="text-slate-300 font-semibold">London FA Affiliated</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-slate-400">Community Interest Company (CIC)</span>
                </div>
            </div>
        </div>

        <!-- 01 — HERO BANNER: Solid, Disciplined Sports Presentation -->
        <section class="relative min-h-[85vh] flex items-center justify-center overflow-hidden border-b border-slate-800">
            <!-- Background Photography with Solid Contrast Overlay -->
            <div
                class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-60"
                style="background-image: url('/images/club/hero-football.jpg')"
            />
            <div class="absolute inset-0 bg-black/60" />

            <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 text-center flex flex-col items-center">
                <!-- Tagline -->
                <div class="mb-6">
                    <span class="inline-block px-3 py-1 bg-purple-900/60 border border-purple-700/60 text-purple-200 text-xs font-bold uppercase tracking-widest rounded">
                        London Youth Football Club
                    </span>
                </div>

                <!-- Main Club Headline: Confident, Editorial, Non-SaaS -->
                <h1 class="text-4xl sm:text-6xl md:text-7xl font-extrabold tracking-tight text-white mb-6 uppercase leading-tight max-w-4xl">
                    Come and train with us.
                </h1>

                <!-- Subheadline: Simple, direct football-club language -->
                <p class="text-lg sm:text-xl text-slate-200 max-w-2xl mx-auto mb-10 leading-relaxed font-normal">
                    TopGrade London FC provides structured youth football training, technical coaching, and competitive league match play for players aged U7 to U16 in Tottenham and Hackney.
                </p>

                <!-- Action CTA Buttons: Solid, Non-Gradient -->
                <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto justify-center mb-14">
                    <Link
                        href="/bookings"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-purple-800 hover:bg-purple-900 text-white font-bold uppercase tracking-wider text-sm rounded transition-colors shadow-sm"
                    >
                        <span>Book a Trial</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                    <a
                        href="#training"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold uppercase tracking-wider text-sm rounded border border-slate-700 transition-colors"
                    >
                        <span>Training Schedule</span>
                    </a>
                </div>

                <!-- Club Facts Bar: Thin Dividers, Clear Hierarchy -->
                <div class="w-full max-w-4xl grid grid-cols-2 md:grid-cols-4 gap-4 pt-8 border-t border-slate-800">
                    <div
                        v-for="stat in quickStats"
                        :key="stat.label"
                        class="p-4 rounded bg-slate-900/90 border border-slate-800 text-center"
                    >
                        <div class="text-xl sm:text-2xl font-extrabold text-white tracking-tight uppercase">
                            {{ stat.value }}
                        </div>
                        <div class="text-xs font-bold uppercase tracking-wider text-purple-400 mt-1">
                            {{ stat.label }}
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5">
                            {{ stat.detail }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 02 — TRAINING SCHEDULE: Client's Real Timetable & Venues -->
        <section id="training" class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900 border-b border-slate-800">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4 pb-6 border-b border-slate-800">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-purple-400 block mb-1">
                            Club Schedule
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white uppercase tracking-tight">
                            Training Times & Venues
                        </h2>
                    </div>
                    <p class="text-slate-300 text-sm max-w-md">
                        Official weekly training sessions for all age squads. Players train on quality floodlit 3G pitches and sports facilities.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div
                        v-for="(sched, idx) in trainingSchedule"
                        :key="idx"
                        class="p-8 rounded bg-slate-950 border border-slate-800 flex flex-col justify-between space-y-6"
                    >
                        <div class="space-y-6">
                            <!-- Day & Badge -->
                            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                                <h3 class="text-2xl font-extrabold text-white uppercase tracking-tight">
                                    {{ sched.days }}
                                </h3>
                                <span class="px-2.5 py-1 bg-purple-950 text-purple-300 border border-purple-800/60 rounded text-[11px] font-bold uppercase tracking-wider">
                                    {{ sched.badge }}
                                </span>
                            </div>

                            <!-- Sessions & Times -->
                            <div class="space-y-3">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Session Times</span>
                                <div
                                    v-for="(session, sIdx) in sched.sessions"
                                    :key="sIdx"
                                    class="flex items-center justify-between p-3.5 rounded bg-slate-900 border border-slate-800"
                                >
                                    <div class="flex items-center gap-2">
                                        <Clock class="w-4 h-4 text-purple-400 shrink-0" />
                                        <span class="font-extrabold text-white text-base">{{ session.age }}</span>
                                    </div>
                                    <span class="text-sm font-semibold text-purple-300">{{ session.time }}</span>
                                </div>
                            </div>

                            <!-- Venue Info -->
                            <div class="space-y-2 pt-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Training Venue</span>
                                <div class="p-4 rounded bg-slate-900 border border-slate-800 space-y-1.5">
                                    <div class="font-bold text-white text-base">
                                        {{ sched.venue.name }}
                                    </div>
                                    <div class="text-xs text-purple-300 font-medium">
                                        {{ sched.venue.facility }}
                                    </div>
                                    <div class="flex items-start gap-2 text-xs text-slate-400 pt-1">
                                        <MapPin class="w-3.5 h-3.5 text-slate-500 shrink-0 mt-0.5" />
                                        <span>{{ sched.venue.address }}</span>
                                    </div>
                                    <div class="pt-1 text-[11px] text-slate-400">
                                        <span class="font-bold text-slate-300">Surface:</span> {{ sched.venue.surface }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Link -->
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

                <!-- Parental Note -->
                <div class="mt-8 p-4 rounded bg-slate-950 border border-slate-800 flex items-center gap-3 text-xs text-slate-400">
                    <CheckCircle2 class="w-4 h-4 text-purple-400 shrink-0" />
                    <span>Please arrive 10 minutes prior to session start. Players must bring boots, shin guards, and water bottle for all training sessions.</span>
                </div>
            </div>
        </section>

        <!-- 03 — TEAMS & AGE GROUPS -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-950 border-b border-slate-800">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4 pb-6 border-b border-slate-800">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-purple-400 block mb-1">
                            Squad Structure
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white uppercase tracking-tight">
                            Our Youth Teams
                        </h2>
                    </div>
                    <Link
                        href="/bookings"
                        class="text-xs font-bold uppercase tracking-wider text-purple-400 hover:text-white flex items-center gap-1.5 transition-colors"
                    >
                        <span>View Bookings</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                    </Link>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        v-for="squad in squads"
                        :key="squad.name"
                        class="p-6 rounded bg-slate-900 border border-slate-800 flex flex-col justify-between space-y-4"
                    >
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-purple-400 block mb-1">
                                {{ squad.stage }}
                            </span>
                            <h3 class="text-xl font-extrabold text-white uppercase tracking-tight mb-3">
                                {{ squad.name }}
                            </h3>
                            <p class="text-slate-400 text-xs leading-relaxed">
                                {{ squad.description }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-800">
                            <Link
                                href="/bookings"
                                class="text-xs font-bold uppercase tracking-wider text-white hover:text-purple-300 flex items-center gap-1 transition-colors"
                            >
                                <span>Book a Trial</span>
                                <ArrowRight class="w-3 h-3" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 04 — DEVELOP YOUR GAME: The Coaching Syllabus -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900 border-b border-slate-800">
            <div class="max-w-7xl mx-auto">
                <div class="max-w-2xl mb-12 pb-6 border-b border-slate-800">
                    <span class="text-xs font-bold uppercase tracking-widest text-purple-400 block mb-1">
                        Development Philosophy
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white uppercase tracking-tight">
                        Develop Your Game
                    </h2>
                    <p class="text-slate-300 text-sm mt-2">
                        Our football curriculum builds intelligent, confident, and technically disciplined players through structured progression.
                    </p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        v-for="pillar in pillars"
                        :key="pillar.title"
                        class="p-6 rounded bg-slate-950 border border-slate-800 space-y-4"
                    >
                        <div class="w-10 h-10 rounded bg-purple-950 border border-purple-800 flex items-center justify-center text-purple-300">
                            <component :is="pillar.icon" class="w-5 h-5" />
                        </div>
                        <h3 class="text-lg font-bold text-white uppercase tracking-tight">
                            {{ pillar.title }}
                        </h3>
                        <p class="text-slate-400 text-xs leading-relaxed">
                            {{ pillar.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 05 — HOME GAME VENUES & MATCHDAY GROUNDS -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-950 border-b border-slate-800">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4 pb-6 border-b border-slate-800">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-purple-400 block mb-1">
                            Pitch Locations
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white uppercase tracking-tight">
                            Home Game Venues
                        </h2>
                    </div>
                    <p class="text-slate-300 text-sm max-w-md">
                        TopGrade London FC plays competitive home fixtures across three registered London venues.
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    <div
                        v-for="venue in homeVenues"
                        :key="venue.name"
                        class="p-6 rounded bg-slate-900 border border-slate-800 flex flex-col justify-between space-y-4"
                    >
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-0.5 rounded bg-purple-950 text-purple-300 border border-purple-800/60 text-[10px] font-bold uppercase tracking-wider">
                                    Official Ground
                                </span>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white uppercase tracking-tight">
                                    {{ venue.name }}
                                </h3>
                                <span class="text-xs text-purple-300 font-medium block mt-0.5">
                                    {{ venue.subtext }}
                                </span>
                            </div>
                            <div class="flex items-start gap-2 text-xs text-slate-400">
                                <MapPin class="w-4 h-4 text-purple-400 shrink-0 mt-0.5" />
                                <span>{{ venue.address }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-800 text-xs text-slate-400 space-y-1">
                            <div><span class="font-bold text-slate-300">Match Role:</span> {{ venue.role }}</div>
                            <div><span class="font-bold text-slate-300">Pitch:</span> {{ venue.surface }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 06 — SQUAD SESSIONS / BOOKINGS CATALOG HOOK -->
        <section v-if="props.featuredActivities && props.featuredActivities.length" class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900 border-b border-slate-800">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4 pb-6 border-b border-slate-800">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-purple-400 block mb-1">
                            Bookings
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white uppercase tracking-tight">
                            Squad Training & Trial Sessions
                        </h2>
                    </div>
                    <Link
                        href="/bookings"
                        class="text-xs font-bold uppercase tracking-wider text-purple-400 hover:text-white flex items-center gap-1.5 transition-colors"
                    >
                        <span>View All Bookings</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                    </Link>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <div
                        v-for="activity in props.featuredActivities"
                        :key="activity.id"
                        class="rounded bg-slate-950 border border-slate-800 p-6 flex flex-col justify-between space-y-6"
                    >
                        <div class="space-y-4">
                            <div class="flex justify-between items-start">
                                <span class="px-2.5 py-1 rounded bg-slate-900 text-slate-300 border border-slate-800 text-xs font-medium flex items-center gap-1.5">
                                    <Clock class="w-3.5 h-3.5 text-purple-400" />
                                    {{ activity.duration_minutes }} mins
                                </span>
                                <span class="text-base font-extrabold text-white">
                                    {{
                                        activity.price === '0' || activity.price === '0.00'
                                            ? 'FREE TRIAL'
                                            : new Intl.NumberFormat('en-GB', {
                                                  style: 'currency',
                                                  currency: activity.currency || 'GBP',
                                              }).format(parseFloat(activity.price))
                                    }}
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
                                <ArrowRight class="w-3.5 h-3.5" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 07 — CLUB ACTION & PHOTOGRAPHY GALLERY -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-950 border-b border-slate-800">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4 pb-6 border-b border-slate-800">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-purple-400 block mb-1">
                            Club Gallery
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white uppercase tracking-tight">
                            Life at TopGrade London FC
                        </h2>
                    </div>
                    <p class="text-slate-300 text-sm max-w-md">
                        Authentic match action, floodlit training sessions, and squad coaching across North and East London.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        v-for="(item, idx) in gallery"
                        :key="idx"
                        class="rounded overflow-hidden border border-slate-800 bg-slate-900 flex flex-col"
                    >
                        <div class="aspect-4/3 w-full overflow-hidden bg-slate-900">
                            <img
                                :src="item.image"
                                :alt="item.alt"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            />
                        </div>
                        <div class="p-4 border-t border-slate-800">
                            <h3 class="text-sm font-bold text-white uppercase tracking-tight">
                                {{ item.title }}
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ item.location }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Optional Dynamic CMS Content Blocks -->
        <section v-if="props.blocks && props.blocks.length" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <BlockRenderer :blocks="props.blocks" />
        </section>

        <!-- 08 — CLUB NEWS / EDITORIAL -->
        <section v-if="props.articles && props.articles.length" class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900 border-b border-slate-800">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-end mb-12 pb-6 border-b border-slate-800">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-purple-400 block mb-1">Editorial</span>
                        <h2 class="text-3xl font-extrabold text-white uppercase tracking-tight">Club News</h2>
                    </div>
                    <Link href="/articles" class="text-xs font-bold uppercase tracking-wider text-purple-400 hover:text-white transition-colors">
                        All Articles →
                    </Link>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <div
                        v-for="article in props.articles"
                        :key="article.id"
                        class="p-6 rounded bg-slate-950 border border-slate-800 space-y-3"
                    >
                        <h3 class="text-lg font-bold text-white uppercase tracking-tight">{{ article.title }}</h3>
                        <p class="text-slate-400 text-xs line-clamp-3 leading-relaxed">{{ article.excerpt }}</p>
                        <Link :href="`/articles/${article.slug}`" class="text-xs font-bold uppercase tracking-wider text-purple-400 hover:text-white block pt-2 transition-colors">
                            Read Article →
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- 09 — FINAL CLUB CTA: Solid Presentation -->
        <section class="relative py-24 px-4 sm:px-6 lg:px-8 bg-black text-white text-center overflow-hidden border-t border-slate-800">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-40"
                style="background-image: url('/images/club/squad_celebration.jpg')"
            />
            <div class="absolute inset-0 bg-black/75" />

            <div class="relative z-10 max-w-3xl mx-auto space-y-6">
                <span class="inline-block px-3 py-1 rounded bg-purple-900/60 border border-purple-700/60 text-purple-200 text-xs font-bold tracking-widest uppercase">
                    Join TopGrade
                </span>
                <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white uppercase">
                    Train with TopGrade London FC
                </h2>
                <p class="text-slate-200 text-base sm:text-lg max-w-xl mx-auto leading-relaxed font-normal">
                    Book an introductory trial session. Meet our coaches, experience our squad training environment, and develop your game.
                </p>
                <div class="pt-4 flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        href="/bookings"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-purple-800 hover:bg-purple-900 text-white font-bold uppercase tracking-wider text-sm rounded transition-colors"
                    >
                        <span>Book a Trial</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                    <Link
                        href="/contact"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold uppercase tracking-wider text-sm rounded border border-slate-700 transition-colors"
                    >
                        <span>Contact the Club</span>
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
