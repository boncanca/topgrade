<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import BlockRenderer from '@/components/CMS/BlockRenderer.vue';
import {
    Users,
    CheckCircle2,
    ArrowRight,
    MapPin,
    Star
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

interface PageContent {
    id: number;
    title: string;
    slug: string;
    excerpt?: string;
    content?: string;
    metadata_json?: {
        tagline?: string;
        headline?: string;
        subheadline?: string;
        stats?: Array<{ number: string; label: string }>;
        why_choose_title?: string;
        why_choose_subtitle?: string;
        features?: string[];
    };
}

const props = defineProps<{
    featuredActivities: Activity[];
    page?: PageContent | null;
    blocks?: Block[];
}>();

defineOptions({
    layout: PublicLayout,
});

const pathways = [
    {
        stage: 'Stage 01',
        age: 'Ages 4–6',
        title: 'Mini Kickers',
        desc: 'Fun introduction to ball mastery, body coordination, and social team confidence.',
    },
    {
        stage: 'Stage 02',
        age: 'Ages 7–10',
        title: 'Junior Academy',
        desc: 'Developing technical precision, 1v1 skill, passing rhythm, and small-sided matches.',
    },
    {
        stage: 'Stage 03',
        age: 'Ages 11–14',
        title: 'Development Squads',
        desc: 'Tactical positioning, match intensity, speed & agility, and competitive league play.',
    },
    {
        stage: 'Stage 04',
        age: 'Ages 15–18',
        title: 'Elite Pathway',
        desc: 'Advanced match tactics, showcase trials, athletic conditioning, and senior football prep.',
    },
];

const testimonials = [
    {
        quote: "Topgrade FC has transformed my son's game. The level of coaching and positive discipline is unmatched in London.",
        author: "Sarah M.",
        role: "Parent of U10 Player",
    },
    {
        quote: "The trial session was fantastic! My daughter fell in love with the team environment immediately.",
        author: "David K.",
        role: "Parent of Mini Kickers Player",
    },
];
</script>

<template>
    <Head :title="props.page?.title ?? 'Train Like A Champion - TopGrade London FC'" />

    <!-- Hero Section -->
    <section class="relative min-h-[85vh] flex items-center justify-center overflow-hidden bg-slate-950 text-white">
        <!-- Background Image -->
        <div
            class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30"
            style="background-image: url('/images/hero_action.png')"
        />
        <div class="absolute inset-0 bg-slate-950/80" />

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center flex flex-col items-center">
            <!-- Tagline Badge -->
            <div class="inline-block px-4 py-1.5 rounded-full bg-slate-800 border border-slate-700 text-slate-200 text-xs font-semibold tracking-widest uppercase mb-8">
                {{ props.page?.metadata_json?.tagline ?? 'ALWAYS THE BEST — EST. 2022' }}
            </div>

            <!-- Headline -->
            <h1 class="text-4xl sm:text-6xl md:text-7xl font-extrabold tracking-tight text-white mb-6 leading-tight">
                <template v-if="props.page?.metadata_json?.headline">
                    {{ props.page.metadata_json.headline }}
                </template>
                <template v-else>
                    DEVELOP YOUR FOOTBALL FUTURE
                </template>
            </h1>

            <!-- Subheadline -->
            <p class="text-base sm:text-lg md:text-xl text-slate-300 max-w-3xl mx-auto mb-10 leading-relaxed">
                {{ props.page?.metadata_json?.subheadline ?? 'Youth football excellence in London. Ages 4–18. Professional UEFA-licensed coaching, structured player pathways, and a winning environment.' }}
            </p>

            <!-- CTA Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto justify-center mb-16">
                <Link
                    href="/bookings"
                    class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors shadow-md"
                >
                    <span>Book Free Trial</span>
                </Link>
                <Link
                    href="/training"
                    class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-lg border border-slate-700 transition-colors"
                >
                    <span>View Training Programs</span>
                    <ArrowRight class="w-4 h-4" />
                </Link>
            </div>

            <!-- Stats Bar -->
            <div class="w-full max-w-4xl grid grid-cols-2 md:grid-cols-4 gap-4 p-4 rounded-xl bg-slate-900 border border-slate-800">
                <div class="p-4 text-center border-r border-slate-800 last:border-0">
                    <div class="text-2xl sm:text-4xl font-extrabold text-white">200+</div>
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-1">Active Players</div>
                </div>
                <div class="p-4 text-center border-r border-slate-800 last:border-0">
                    <div class="text-2xl sm:text-4xl font-extrabold text-white">15+</div>
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-1">Academy Squads</div>
                </div>
                <div class="p-4 text-center border-r border-slate-800 last:border-0">
                    <div class="text-2xl sm:text-4xl font-extrabold text-white">4–18</div>
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-1">Age Range</div>
                </div>
                <div class="p-4 text-center">
                    <div class="text-2xl sm:text-4xl font-extrabold text-white">100%</div>
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mt-1">UEFA Coaches</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Training Programmes -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-purple-600 dark:text-purple-400 block mb-2">Training Academy</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">Featured Programmes</h2>
                </div>
                <Link
                    href="/bookings"
                    class="text-sm font-semibold text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 flex items-center gap-1 transition-colors"
                >
                    <span>View All Programs</span>
                    <ArrowRight class="w-4 h-4" />
                </Link>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                <div
                    v-for="activity in featuredActivities"
                    :key="activity.id"
                    class="rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 p-6 flex flex-col justify-between"
                >
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <span class="px-3 py-1 rounded-md bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-medium">
                                {{ activity.duration_minutes }} mins
                            </span>
                            <span class="text-xl font-bold text-purple-600 dark:text-white">
                                {{
                                    activity.price === '0' || activity.price === '0.00'
                                        ? 'FREE'
                                        : new Intl.NumberFormat('en-GB', {
                                              style: 'currency',
                                              currency: activity.currency || 'GBP',
                                          }).format(parseFloat(activity.price))
                                }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
                            {{ activity.name }}
                        </h3>

                        <p class="text-slate-600 dark:text-slate-400 text-sm line-clamp-3 mb-6 leading-relaxed">
                            {{ activity.description }}
                        </p>
                    </div>

                    <div>
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-6">
                            <MapPin class="w-4 h-4 text-purple-600 dark:text-purple-400 shrink-0" />
                            <span>{{ activity.location }}</span>
                        </div>

                        <Link
                            :href="`/bookings/${activity.slug}`"
                            class="w-full py-3 px-4 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold flex items-center justify-center gap-2 transition-colors shadow-sm"
                        >
                            <span>Book Session</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Player Development Pathway Timeline -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white transition-colors">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 block mb-2">Structured Progression</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white mb-4">Player Development Pathway</h2>
                <p class="text-slate-600 dark:text-slate-400 text-base">
                    Every player progresses through a structured curriculum designed by UEFA academy coaches.
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-4">
                <div
                    v-for="step in pathways"
                    :key="step.stage"
                    class="p-6 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex flex-col justify-between"
                >
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xs font-mono font-bold text-slate-400 dark:text-slate-500">{{ step.stage }}</span>
                            <span class="px-2.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-semibold">
                                {{ step.age }}
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ step.title }}</h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">{{ step.desc }}</p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400 font-medium">
                        <CheckCircle2 class="w-4 h-4 text-emerald-500 dark:text-emerald-400" />
                        <span>UEFA Curriculum</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Academy Facility & Philosophy -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
            <div class="rounded-2xl overflow-hidden shadow-lg border border-slate-200 dark:border-slate-800">
                <img
                    src="/images/coaching_facility.png"
                    alt="Topgrade London FC Facility"
                    class="w-full h-auto object-cover"
                />
            </div>

            <div class="space-y-6">
                <span class="text-xs font-bold uppercase tracking-widest text-purple-600 dark:text-purple-400">Why Topgrade FC?</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white leading-tight">
                    Professional Coaching for Every Aspiring Player
                </h2>
                <p class="text-slate-600 dark:text-slate-300 text-base leading-relaxed">
                    At TopGrade London FC, we combine high-level technical training with character building, resilience, and sportsmanship.
                </p>

                <div class="space-y-4 pt-2">
                    <div class="flex items-start gap-3">
                        <CheckCircle2 class="w-5 h-5 text-emerald-500 dark:text-emerald-400 shrink-0 mt-0.5" />
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm">UEFA Licensed Coaches</h4>
                            <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Experienced academy coaches with pro club backgrounds.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <CheckCircle2 class="w-5 h-5 text-emerald-500 dark:text-emerald-400 shrink-0 mt-0.5" />
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm">Competitive Match Pathways</h4>
                            <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">League fixtures, tournament trips, and trial showcases.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <CheckCircle2 class="w-5 h-5 text-emerald-500 dark:text-emerald-400 shrink-0 mt-0.5" />
                        <div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm">Individual Player Analysis</h4>
                            <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Video breakdowns and periodic development reviews.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <Link
                        href="/about"
                        class="inline-flex items-center gap-2 px-6 py-3.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors shadow-sm"
                    >
                        <span>Learn More About TGLFC</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials / Parent Feedback -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white transition-colors">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 block mb-2">Community Reviews</span>
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">Trusted by London Parents & Players</h2>
            </div>

            <div class="grid gap-6 md:grid-cols-2 max-w-4xl mx-auto">
                <div
                    v-for="(item, idx) in testimonials"
                    :key="idx"
                    class="p-6 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-4"
                >
                    <div class="flex text-amber-400 gap-1">
                        <Star v-for="i in 5" :key="i" class="w-4 h-4 fill-amber-400 text-amber-400" />
                    </div>
                    <p class="text-slate-700 dark:text-slate-300 text-sm italic leading-relaxed">"{{ item.quote }}"</p>
                    <div class="pt-2 border-t border-slate-200 dark:border-slate-800 flex justify-between items-center text-xs">
                        <span class="font-bold text-slate-900 dark:text-white">{{ item.author }}</span>
                        <span class="text-slate-500 dark:text-slate-400">{{ item.role }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Dynamic CMS Blocks Container -->
    <section v-if="props.blocks && props.blocks.length" class="py-16 px-4 sm:px-6 lg:px-8 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
        <div class="max-w-6xl mx-auto">
            <BlockRenderer :blocks="props.blocks" />
        </div>
    </section>

    <!-- Final Call To Action Banner -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-900 text-white text-center border-t border-slate-800">
        <div class="max-w-3xl mx-auto space-y-6">
            <h2 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                Ready to Join Topgrade London FC?
            </h2>
            <p class="text-slate-300 text-base">
                Book your child's trial session today and experience London's premier youth academy environment.
            </p>
            <div class="pt-4">
                <Link
                    href="/bookings"
                    class="inline-flex items-center gap-2 px-8 py-4 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-colors shadow-md"
                >
                    <span>Book Your Free Trial Now</span>
                    <ArrowRight class="w-4 h-4" />
                </Link>
            </div>
        </div>
    </section>
</template>


