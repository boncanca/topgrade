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
    Award,
    Compass,
    Clock,
    Sparkles,
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

const stats = [
    { label: 'Youth Squads', value: '8+', detail: 'Ages 4 to 17 years' },
    { label: 'London Bases', value: '2', detail: 'Tottenham & Hackney' },
    { label: 'Matchday Leagues', value: 'FA', detail: 'Sanctioned Youth Leagues' },
    { label: 'Player Progression', value: '100%', detail: 'Individual development' },
];

const gallery = [
    {
        title: 'Matchday League Action',
        subtitle: 'Hackney Marshes Saturday Youth League',
        tag: 'League Match',
        image: '/images/club/youth_match_action.jpg',
        alt: 'TopGrade London FC player dribbling during league match',
        colSpan: 'md:col-span-2 lg:col-span-2',
    },
    {
        title: 'Evening Floodlit Drills',
        subtitle: 'Tottenham 3G Astroturf Training Ground',
        tag: 'Squad Training',
        image: '/images/club/training_pitch_evening.jpg',
        alt: 'TopGrade London FC squad training under floodlights',
        colSpan: 'md:col-span-1 lg:col-span-1',
    },
    {
        title: 'Team Spirit & Camaraderie',
        subtitle: 'Shared victories and collective growth',
        tag: 'Club Culture',
        image: '/images/club/squad_celebration.jpg',
        alt: 'TopGrade London FC players celebrating a match goal together',
        colSpan: 'md:col-span-1 lg:col-span-1',
    },
    {
        title: 'Tactical Game Understanding',
        subtitle: 'Coach pitchside analysis & decision-making',
        tag: 'Development',
        image: '/images/club/tactical_coaching.jpg',
        alt: 'Coach explaining tactical board movement to TopGrade squad',
        colSpan: 'md:col-span-2 lg:col-span-2',
    },
];

const pillars = [
    {
        title: 'Technical Ball Mastery',
        description: 'Close control, passing range, first touch precision, and 1v1 attacking confidence in every session.',
        icon: Target,
        metric: 'Ball Control & 1v1',
    },
    {
        title: 'Tactical Game Awareness',
        description: 'Positioning, pitch vision, decision-making under match pressure, and understanding phase transitions.',
        icon: Compass,
        metric: 'Spatial Vision',
    },
    {
        title: 'Teamwork & Discipline',
        description: 'Communication on the pitch, collective work ethic, respect for referees and teammates, and accountability.',
        icon: Users,
        metric: 'Squad Cohesion',
    },
    {
        title: 'Competitive Match Experience',
        description: 'Applying training drills into real fixtures against London grassroots and academy opposition.',
        icon: Trophy,
        metric: 'Match Preparation',
    },
];

const grounds = [
    {
        name: 'Tottenham Training Facility',
        address: 'Fredrick Knight Sports Ground / Willoughby Lane, Tottenham, London N17 0RT',
        type: '3G All-Weather Astroturf & Floodlit Pitches',
        focus: 'Midweek squad training, technical agility sessions, and trial evaluations',
        badge: 'Home Training Base',
    },
    {
        name: 'Hackney Marshes Match Grounds',
        address: 'Homerton Road / Hackney Marshes, East London, E9 5PF',
        type: 'Natural Grass Matchday Pitches',
        focus: 'Saturday youth league matches, inter-club tournaments, and squad fixtures',
        badge: 'Matchday Venue',
    },
];

const trialSteps = [
    {
        step: '01',
        title: 'Select Age Squad',
        description: 'Find the appropriate session for your child based on their birth year and current playing experience.',
    },
    {
        step: '02',
        title: 'Attend Trial Session',
        description: 'Bring boots, shin pads, and a water bottle to meet the coaches and join the squad training drills.',
    },
    {
        step: '03',
        title: 'Coach Feedback',
        description: 'Receive personalized session feedback, squad recommendations, and next steps for regular club membership.',
    },
];
</script>

<template>
    <Head title="TopGrade London FC — Official Youth Football Club" />

    <div class="bg-slate-950 text-white selection:bg-purple-600 selection:text-white">
        <!-- 00 — MATCHDAY & TRAINING TICKER BAR -->
        <div class="bg-gradient-to-r from-purple-950/90 via-slate-900 to-purple-950/90 border-b border-purple-900/40 text-xs py-2 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        Season Active
                    </span>
                    <span class="text-slate-200 font-medium">
                        Training bases in Tottenham (N17) & Hackney Marshes (E9)
                    </span>
                </div>
                <div class="flex items-center gap-4 text-slate-300 text-[11px]">
                    <span class="flex items-center gap-1">
                        <Award class="w-3.5 h-3.5 text-amber-400" />
                        London FA Sanctioned
                    </span>
                    <span class="hidden md:inline text-slate-600">•</span>
                    <span class="hidden md:inline">Community Interest Company (CIC)</span>
                </div>
            </div>
        </div>

        <!-- 01 — HERO BANNER (Light, Clear Overlay + Motion Entrance) -->
        <section class="relative min-h-[92vh] flex items-center justify-center overflow-hidden">
            <!-- Background Photography with Gentle, Balanced Gradient Overlay -->
            <div
                class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-80 animate-ken-burns"
                style="background-image: url('/images/club/hero-football.jpg')"
            />
            <!-- Refined, lighter overlay: clear at top and center, smooth dark vignette toward bottom for contrast -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 to-slate-950/20" />
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/60 via-transparent to-slate-950/60" />
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-purple-900/15 via-transparent to-transparent" />

            <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-24 sm:py-32 text-center flex flex-col items-center">
                <!-- Crest & Badge Pill (Float Animation) -->
                <div class="animate-fade-in-up [animation-delay:100ms] inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-900/80 border border-purple-500/40 text-purple-200 text-xs font-bold tracking-widest uppercase mb-8 shadow-xl backdrop-blur-md hover:border-purple-400 transition-colors cursor-default">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" />
                    TopGrade London FC
                    <span class="text-slate-500">•</span>
                    <span class="text-slate-300 font-normal">Youth Football Club</span>
                </div>

                <!-- Main Club Headline (Staggered Fade-in-Up) -->
                <h1 class="animate-fade-in-up [animation-delay:250ms] text-4xl sm:text-6xl md:text-7xl font-extrabold tracking-tight text-white mb-6 leading-[1.08] max-w-4xl drop-shadow-md">
                    Talent starts with <span class="bg-gradient-to-r from-purple-300 via-pink-300 to-amber-200 bg-clip-text text-transparent">opportunity.</span>
                </h1>

                <!-- Subheadline -->
                <p class="animate-fade-in-up [animation-delay:400ms] text-lg sm:text-xl text-slate-100 max-w-2xl mx-auto mb-10 leading-relaxed font-normal drop-shadow-sm">
                    London grassroots football club dedicated to helping young footballers develop technical skill, game understanding, and match experience.
                </p>

                <!-- Action CTA Buttons with Hover Lift & Glow -->
                <div class="animate-fade-in-up [animation-delay:550ms] flex flex-col sm:flex-row gap-4 w-full sm:w-auto justify-center mb-16">
                    <Link
                        href="/bookings"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-[var(--brand-primary)] hover:bg-purple-700 text-white font-semibold text-base rounded-xl transition-all duration-300 shadow-xl shadow-purple-950/50 hover:shadow-purple-600/40 hover:-translate-y-1 active:translate-y-0 active:scale-98"
                    >
                        <span>Book a Trial</span>
                        <ArrowRight class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                    </Link>
                    <Link
                        href="/bookings"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-slate-900/80 hover:bg-slate-800/90 text-white font-semibold text-base rounded-xl border border-slate-700 hover:border-slate-500 transition-all duration-300 backdrop-blur-md hover:-translate-y-1"
                    >
                        <span>Explore Squads</span>
                    </Link>
                </div>

                <!-- Quick Club Statistics Bar with Staggered Entrance -->
                <div class="animate-fade-in-up [animation-delay:700ms] w-full max-w-4xl grid grid-cols-2 md:grid-cols-4 gap-4 pt-8 border-t border-slate-800/80">
                    <div
                        v-for="stat in stats"
                        :key="stat.label"
                        class="p-4 rounded-xl bg-slate-900/60 hover:bg-slate-900/80 border border-slate-800/80 hover:border-purple-500/40 backdrop-blur-md text-center transition-all duration-300 hover:-translate-y-1 shadow-md"
                    >
                        <div class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                            {{ stat.value }}
                        </div>
                        <div class="text-xs font-semibold uppercase tracking-wider text-purple-300 mt-1">
                            {{ stat.label }}
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5">
                            {{ stat.detail }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 02 — CLUB ACTION & PHOTOGRAPHY GALLERY (Vibrant, Clear Photos with Rich Motion) -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900/60 border-y border-slate-800/80">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-[var(--brand-secondary)] block mb-2">
                            Club Gallery
                        </span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                            Life at TopGrade London FC
                        </h2>
                    </div>
                    <p class="text-slate-300 text-sm max-w-md">
                        From Saturday league fixtures on grass to evening technical sessions on 3G turf, see our young players in action across London.
                    </p>
                </div>

                <!-- Image Mosaic Grid with Hover Scale & Elevation -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        v-for="(item, idx) in gallery"
                        :key="idx"
                        :class="['group relative rounded-2xl overflow-hidden border border-slate-800 bg-slate-950 shadow-xl transition-all duration-500 hover:border-purple-500/70 hover:shadow-2xl hover:shadow-purple-900/30 hover:-translate-y-1.5', item.colSpan]"
                    >
                        <div class="aspect-16/10 sm:aspect-16/9 w-full overflow-hidden relative">
                            <!-- Full clarity image with smooth zoom on hover -->
                            <img
                                :src="item.image"
                                :alt="item.alt"
                                class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-108"
                                loading="lazy"
                            />
                            <!-- Light bottom-only gradient for readable caption without dimming the photo -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/25 to-transparent transition-opacity duration-300 group-hover:opacity-90" />

                            <!-- Badge in top corner -->
                            <div class="absolute top-4 left-4 transition-transform duration-300 group-hover:translate-x-1">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-slate-900/85 text-purple-200 border border-purple-500/40 backdrop-blur-md shadow-md">
                                    {{ item.tag }}
                                </span>
                            </div>

                            <!-- Caption at bottom -->
                            <div class="absolute bottom-4 left-4 right-4 text-left transition-transform duration-300 group-hover:translate-y--0.5">
                                <h3 class="text-lg sm:text-xl font-bold text-white group-hover:text-purple-300 transition-colors">
                                    {{ item.title }}
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-200 mt-1 line-clamp-1">
                                    {{ item.subtitle }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 03 — TRAINING PHILOSOPHY (The 4 Pillars with Interactive Motion) -->
        <section class="py-24 px-4 sm:px-6 lg:px-8 bg-slate-950">
            <div class="max-w-7xl mx-auto">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="text-xs font-bold uppercase tracking-widest text-[var(--brand-secondary)] block mb-2">
                        Development Philosophy
                    </span>
                    <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white">
                        Train with purpose.
                    </h2>
                    <p class="text-slate-400 text-base sm:text-lg mt-4 leading-relaxed">
                        Our coaching syllabus builds confident, intelligent, and disciplined footballers through structured progression.
                    </p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="pillar in pillars"
                        :key="pillar.title"
                        class="p-7 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-purple-500/50 hover:bg-slate-900 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl hover:shadow-purple-950/50 flex flex-col justify-between group cursor-default"
                    >
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-purple-900/40 border border-purple-500/30 flex items-center justify-center text-purple-300 mb-6 group-hover:bg-purple-600 group-hover:text-white group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300 shadow-md">
                                <component :is="pillar.icon" class="w-6 h-6" />
                            </div>
                            <h3 class="text-xl font-bold text-white mb-3 group-hover:text-purple-300 transition-colors">
                                {{ pillar.title }}
                            </h3>
                            <p class="text-slate-400 text-sm leading-relaxed mb-6">
                                {{ pillar.description }}
                            </p>
                        </div>

                        <div class="pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs text-purple-300 font-semibold">
                            <span>Focus Area</span>
                            <span class="text-slate-300">{{ pillar.metric }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 04 — ACTIVE SQUADS & SESSIONS (Catalog Hook) -->
        <section v-if="props.featuredActivities && props.featuredActivities.length" class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900/50 border-t border-slate-800">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-[var(--brand-secondary)] block mb-2">Club Squads</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold text-white">Active Squads & Training</h2>
                    </div>
                    <Link
                        href="/bookings"
                        class="text-sm font-semibold text-purple-400 hover:text-purple-300 flex items-center gap-1.5 transition-colors group"
                    >
                        <span>View All Squads</span>
                        <ArrowRight class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                    </Link>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <div
                        v-for="activity in props.featuredActivities"
                        :key="activity.id"
                        class="rounded-2xl bg-slate-950 border border-slate-800 hover:border-purple-500/60 p-6 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:shadow-purple-950/40 shadow-lg group"
                    >
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <span class="px-3 py-1 rounded-md bg-slate-900 text-purple-300 border border-slate-800 text-xs font-medium flex items-center gap-1.5">
                                    <Clock class="w-3.5 h-3.5 text-purple-400" />
                                    {{ activity.duration_minutes }} mins
                                </span>
                                <span class="text-lg font-bold text-white">
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

                            <h3 class="text-xl font-bold text-white mb-2 group-hover:text-purple-300 transition-colors">
                                {{ activity.name }}
                            </h3>

                            <p class="text-slate-400 text-sm line-clamp-3 mb-6 leading-relaxed">
                                {{ activity.description }}
                            </p>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 text-xs text-slate-400 mb-6">
                                <MapPin class="w-4 h-4 text-purple-400 shrink-0" />
                                <span class="truncate">{{ activity.location }}</span>
                            </div>

                            <Link
                                :href="`/bookings/${activity.slug}`"
                                class="w-full py-3.5 px-4 rounded-xl bg-[var(--brand-primary)] hover:bg-purple-700 text-white text-sm font-semibold flex items-center justify-center gap-2 transition-all duration-200 shadow-md hover:shadow-purple-600/30"
                            >
                                <span>Book a Trial</span>
                                <ArrowRight class="w-4 h-4" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 05 — TRAINING BASES & FACILITIES -->
        <section class="py-24 px-4 sm:px-6 lg:px-8 bg-slate-950 border-t border-slate-800">
            <div class="max-w-7xl mx-auto">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="text-xs font-bold uppercase tracking-widest text-[var(--brand-secondary)] block mb-2">
                        London Grounds
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                        Our Training Grounds
                    </h2>
                    <p class="text-slate-400 text-base mt-3">
                        TopGrade London FC operates out of two primary London sports venues, offering both all-weather 3G turf and natural matchday pitches.
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div
                        v-for="ground in grounds"
                        :key="ground.name"
                        class="p-8 rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-purple-500/50 flex flex-col justify-between space-y-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl shadow-lg group"
                    >
                        <div class="space-y-4">
                            <div class="flex justify-between items-start">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-purple-900/40 text-purple-300 border border-purple-500/30">
                                    {{ ground.badge }}
                                </span>
                            </div>
                            <h3 class="text-2xl font-bold text-white group-hover:text-purple-300 transition-colors">
                                {{ ground.name }}
                            </h3>
                            <div class="flex items-start gap-2.5 text-sm text-slate-300">
                                <MapPin class="w-4 h-4 text-purple-400 shrink-0 mt-0.5" />
                                <span>{{ ground.address }}</span>
                            </div>
                            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800/80 space-y-2 text-xs">
                                <div>
                                    <span class="font-bold text-white uppercase tracking-wider text-[10px]">Surface:</span>
                                    <span class="text-slate-300 ml-2">{{ ground.type }}</span>
                                </div>
                                <div>
                                    <span class="font-bold text-white uppercase tracking-wider text-[10px]">Usage:</span>
                                    <span class="text-slate-400 ml-2">{{ ground.focus }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-800">
                            <Link
                                href="/bookings"
                                class="inline-flex items-center gap-2 text-sm font-semibold text-purple-400 hover:text-purple-300 transition-colors group/link"
                            >
                                <span>View Sessions at this Venue</span>
                                <ArrowRight class="w-4 h-4 transition-transform group-hover/link:translate-x-1" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 06 — HOW TO JOIN / TRIAL JOURNEY (Parent Guidance) -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900/70 border-t border-slate-800">
            <div class="max-w-6xl mx-auto">
                <div class="text-center max-w-xl mx-auto mb-16">
                    <span class="text-xs font-bold uppercase tracking-widest text-[var(--brand-secondary)] block mb-2">
                        Get Started
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-white">
                        Joining TopGrade London FC
                    </h2>
                    <p class="text-slate-400 text-sm mt-2">
                        A straightforward process for parents and young players to experience the club.
                    </p>
                </div>

                <div class="grid md:grid-cols-3 gap-8 relative">
                    <div
                        v-for="step in trialSteps"
                        :key="step.step"
                        class="p-7 rounded-2xl bg-slate-950 border border-slate-800 hover:border-purple-500/40 relative space-y-4 transition-all duration-300 hover:-translate-y-1 shadow-md"
                    >
                        <div class="text-3xl font-black text-purple-500/40">
                            {{ step.step }}
                        </div>
                        <h3 class="text-lg font-bold text-white">
                            {{ step.title }}
                        </h3>
                        <p class="text-slate-400 text-sm leading-relaxed">
                            {{ step.description }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Optional Dynamic CMS Content Blocks -->
        <section v-if="props.blocks && props.blocks.length" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <BlockRenderer :blocks="props.blocks" />
        </section>

        <!-- 07 — CLUB NEWS (Editorial) -->
        <section v-if="props.articles && props.articles.length" class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-950 border-t border-slate-800">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-end mb-12">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-[var(--brand-secondary)] block mb-2">Club News</span>
                        <h2 class="text-3xl font-extrabold text-white">What's happening at TopGrade</h2>
                    </div>
                    <Link href="/articles" class="text-sm font-semibold text-purple-400 hover:text-purple-300">
                        Read All News →
                    </Link>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <div
                        v-for="article in props.articles"
                        :key="article.id"
                        class="p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-purple-500/50 transition-all duration-300 hover:-translate-y-1 shadow-md"
                    >
                        <h3 class="text-lg font-bold text-white mb-2">{{ article.title }}</h3>
                        <p class="text-slate-400 text-sm mb-4 line-clamp-3 leading-relaxed">{{ article.excerpt }}</p>
                        <Link :href="`/articles/${article.slug}`" class="text-xs font-semibold text-purple-400 hover:text-purple-300">
                            Read More →
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- 08 — FINAL STADIUM CALL TO ACTION (Lighter Overlay: Clear Celebrating Players) -->
        <section class="relative py-28 px-4 sm:px-6 lg:px-8 bg-slate-950 text-white text-center overflow-hidden border-t border-slate-800">
            <div
                class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-65 animate-ken-burns"
                style="background-image: url('/images/club/squad_celebration.jpg')"
            />
            <!-- Balanced gradient: allows team celebration and purple kits to be clearly seen, while providing crisp text contrast -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-slate-950/30" />
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/50 via-transparent to-slate-950/50" />

            <div class="relative z-10 max-w-3xl mx-auto space-y-6">
                <span class="inline-block px-3.5 py-1 rounded-full bg-purple-900/60 border border-purple-400/40 text-purple-200 text-xs font-bold tracking-widest uppercase shadow-md backdrop-blur-sm">
                    Take the Next Step
                </span>
                <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white drop-shadow-md">
                    Think they have potential?
                </h2>
                <p class="text-slate-100 text-lg max-w-xl mx-auto leading-relaxed drop-shadow-sm font-normal">
                    Give them the opportunity to train with TopGrade London FC coaches, play alongside teammates, and develop their game.
                </p>
                <div class="pt-4 flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        href="/bookings"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-[var(--brand-primary)] hover:bg-purple-700 text-white font-semibold text-base rounded-xl transition-all duration-300 shadow-xl shadow-purple-950/60 hover:shadow-purple-600/40 hover:-translate-y-1"
                    >
                        <span>Book a Trial</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                    <Link
                        href="/about"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-slate-900/90 hover:bg-slate-800 text-white font-semibold text-base rounded-xl border border-slate-700 hover:border-slate-500 transition-all duration-300 backdrop-blur-md hover:-translate-y-1"
                    >
                        <span>About the Club</span>
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
