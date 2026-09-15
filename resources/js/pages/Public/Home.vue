<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import BlockRenderer from '@/components/CMS/BlockRenderer.vue';
import { ArrowRight, Trophy, Shield, Users, Target, MapPin } from '@lucide/vue';

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

const outcomes = [
    {
        title: 'Technical Skill',
        description: 'Better control, passing and ball confidence.',
        icon: Target,
    },
    {
        title: 'Game Understanding',
        description: 'Better decisions, positioning and awareness.',
        icon: Shield,
    },
    {
        title: 'Teamwork & Discipline',
        description: 'Learn to compete, contribute and respect the game.',
        icon: Users,
    },
    {
        title: 'Playing Experience',
        description: 'Take training into real football situations and matches.',
        icon: Trophy,
    },
];
</script>

<template>
    <Head title="TopGrade London FC — Youth Football Club" />

    <div>
        <!-- 01 — TALENT (Hero) -->
        <section class="relative min-h-[85vh] flex items-center justify-center overflow-hidden bg-slate-950 text-white">
            <div
                class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-40"
                style="background-image: url('/images/club/hero-football.jpg')"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/75 to-slate-950/50" />

            <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center flex flex-col items-center">
                <span class="inline-block px-4 py-1 rounded-full bg-slate-800/90 border border-slate-700 text-purple-300 text-xs font-semibold tracking-widest uppercase mb-6">
                    TopGrade London FC
                </span>

                <h1 class="text-4xl sm:text-6xl md:text-7xl font-extrabold tracking-tight text-white mb-6 leading-tight">
                    Talent starts with opportunity.
                </h1>

                <p class="text-lg sm:text-xl text-slate-300 max-w-2xl mx-auto mb-10 leading-relaxed">
                    A youth football club in London helping young players develop through training, teamwork and playing experience.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto justify-center">
                    <Link
                        href="/bookings"
                        class="inline-flex items-center justify-center px-8 py-4 bg-[var(--brand-primary)] hover:opacity-90 text-white font-semibold rounded-lg transition-opacity shadow-lg"
                    >
                        Book a Trial
                    </Link>
                    <Link
                        href="/bookings"
                        class="inline-flex items-center justify-center px-8 py-4 bg-slate-800/80 hover:bg-slate-700 text-white font-semibold rounded-lg border border-slate-700 transition-colors"
                    >
                        Explore Activities
                    </Link>
                </div>
            </div>
        </section>

        <!-- 02 — DEVELOPMENT (Training) -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
                <div class="rounded-2xl overflow-hidden shadow-md border border-slate-200 dark:border-slate-800">
                    <img
                        src="/images/club/training-agility-drills.jpg"
                        alt="TopGrade London FC Squad Training"
                        class="w-full h-80 sm:h-96 object-cover"
                    />
                </div>

                <div class="space-y-6">
                    <span class="text-xs font-bold uppercase tracking-widest text-[var(--brand-secondary)] block">
                        Club Training
                    </span>
                    <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                        Train with purpose.
                    </h2>
                    <p class="text-slate-600 dark:text-slate-300 text-lg leading-relaxed">
                        Structured football training designed to help young players improve their game.
                    </p>
                    <div class="pt-2">
                        <Link
                            href="/bookings"
                            class="inline-flex items-center gap-2 px-6 py-3.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-900 dark:text-white font-semibold rounded-lg transition-colors"
                        >
                            <span>Explore Activities</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- 03 — OUTCOMES (Develop the Player) -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white transition-colors">
            <div class="max-w-7xl mx-auto">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <span class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 block mb-2">
                        Player Development
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                        Develop the player.
                    </h2>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        v-for="item in outcomes"
                        :key="item.title"
                        class="p-6 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-xs flex flex-col justify-between"
                    >
                        <div class="space-y-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-950/60 flex items-center justify-center text-[var(--brand-primary)]">
                                <component :is="item.icon" class="w-5 h-5" />
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ item.title }}</h3>
                            <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">{{ item.description }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Squad Activities (Application hook) -->
        <section v-if="props.featuredActivities && props.featuredActivities.length" class="py-20 px-4 sm:px-6 lg:px-8 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-[var(--brand-secondary)] block mb-2">Club Squads</span>
                        <h2 class="text-3xl sm:text-4xl font-extrabold">Active Squads & Training</h2>
                    </div>
                    <Link
                        href="/bookings"
                        class="text-sm font-semibold text-[var(--brand-primary)] hover:underline flex items-center gap-1"
                    >
                        <span>View All Squads</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <div
                        v-for="activity in props.featuredActivities"
                        :key="activity.id"
                        class="rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 p-6 flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <span class="px-3 py-1 rounded-md bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-medium">
                                    {{ activity.duration_minutes }} mins
                                </span>
                                <span class="text-lg font-bold text-[var(--brand-primary)] dark:text-white">
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

                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
                                {{ activity.name }}
                            </h3>

                            <p class="text-slate-600 dark:text-slate-400 text-sm line-clamp-3 mb-6 leading-relaxed">
                                {{ activity.description }}
                            </p>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-6">
                                <MapPin class="w-4 h-4 text-[var(--brand-primary)] shrink-0" />
                                <span>{{ activity.location }}</span>
                            </div>

                            <Link
                                :href="`/bookings/${activity.slug}`"
                                class="w-full py-3 px-4 rounded-lg bg-[var(--brand-primary)] hover:opacity-90 text-white text-sm font-semibold flex items-center justify-center gap-2 transition-opacity shadow-xs"
                            >
                                <span>Book a Trial</span>
                                <ArrowRight class="w-4 h-4" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 04 — THE CLUB -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white transition-colors">
            <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6 order-2 lg:order-1">
                    <span class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 block">
                        Our Community
                    </span>
                    <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                        A club to grow with.
                    </h2>
                    <p class="text-slate-600 dark:text-slate-300 text-lg leading-relaxed">
                        Train with coaches, play with teammates and develop through the club environment.
                    </p>
                    <div class="pt-2">
                        <Link
                            href="/about"
                            class="inline-flex items-center gap-2 px-6 py-3.5 bg-slate-900 dark:bg-slate-800 text-white hover:bg-slate-800 dark:hover:bg-slate-700 font-semibold rounded-lg transition-colors"
                        >
                            <span>About the Club</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>

                <div class="rounded-2xl overflow-hidden shadow-md border border-slate-200 dark:border-slate-800 order-1 lg:order-2">
                    <img
                        src="/images/club/team-huddle.jpg"
                        alt="TopGrade London FC Team Huddle"
                        class="w-full h-80 sm:h-96 object-cover"
                    />
                </div>
            </div>
        </section>

        <!-- Optional Dynamic CMS Content Blocks -->
        <section v-if="props.blocks && props.blocks.length" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <BlockRenderer :blocks="props.blocks" />
        </section>

        <!-- 05 — OPPORTUNITY (Final Conversion Point) -->
        <section class="relative py-24 px-4 sm:px-6 lg:px-8 bg-slate-950 text-white text-center overflow-hidden">
            <div
                class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-25"
                style="background-image: url('/images/club/player-match-action.jpg')"
            />
            <div class="absolute inset-0 bg-slate-950/80" />

            <div class="relative z-10 max-w-3xl mx-auto space-y-6">
                <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight">
                    Think they have potential?
                </h2>
                <p class="text-slate-300 text-lg max-w-xl mx-auto">
                    Give them the opportunity to experience TopGrade London FC.
                </p>
                <div class="pt-4">
                    <Link
                        href="/bookings"
                        class="inline-flex items-center gap-2 px-8 py-4 bg-[var(--brand-primary)] hover:opacity-90 text-white font-semibold rounded-lg transition-opacity shadow-lg"
                    >
                        <span>Book a Trial</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- 06 — CLUB NEWS (Only rendered when real articles exist) -->
        <section v-if="props.articles && props.articles.length" class="py-20 px-4 sm:px-6 lg:px-8 bg-white dark:bg-slate-900 text-slate-900 dark:text-white transition-colors">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-end mb-12">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-slate-500 block mb-2">Club News</span>
                        <h2 class="text-3xl font-extrabold">What's happening at TopGrade</h2>
                    </div>
                    <Link href="/articles" class="text-sm font-semibold text-[var(--brand-primary)] hover:underline">
                        Read Club News
                    </Link>
                </div>

                <div class="grid gap-6 md:grid-cols-3">
                    <div
                        v-for="article in props.articles"
                        :key="article.id"
                        class="p-6 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800"
                    >
                        <h3 class="text-lg font-bold mb-2">{{ article.title }}</h3>
                        <p class="text-slate-600 dark:text-slate-400 text-sm mb-4">{{ article.excerpt }}</p>
                        <Link :href="`/articles/${article.slug}`" class="text-xs font-semibold text-[var(--brand-primary)] hover:underline">
                            Read More →
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
