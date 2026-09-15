<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { MapPin, Clock, ArrowRight, Award, Calendar, CheckCircle } from '@lucide/vue';

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

interface Pagination {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

defineProps<{
    activities: Activity[];
    meta: Pagination;
}>();

defineOptions({
    layout: PublicLayout,
});

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
    <Head title="Club Squads & Training — TopGrade London FC" />

    <div class="min-h-screen bg-slate-950 text-white transition-colors">
        <!-- Hero Section with Background Photography (Light, Clear Overlay + Motion Entrance) -->
        <section class="relative min-h-[50vh] flex items-end overflow-hidden pb-14 pt-32 border-b border-slate-800">
            <div
                class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-80 animate-ken-burns"
                style="background-image: url('/images/club/training_pitch_evening.jpg')"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 to-slate-950/20" />
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950/60 via-transparent to-slate-950/60" />

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-4">
                <div class="animate-fade-in-up [animation-delay:100ms] inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-slate-900/80 border border-purple-500/40 text-purple-200 text-xs font-bold uppercase tracking-wider backdrop-blur-md">
                    <Award class="w-3.5 h-3.5 text-purple-400" />
                    Squad Pathways
                </div>

                <h1 class="animate-fade-in-up [animation-delay:250ms] text-3xl sm:text-5xl font-extrabold text-white tracking-tight drop-shadow-md">
                    Club Squads & Training Sessions
                </h1>
                <p class="animate-fade-in-up [animation-delay:400ms] text-slate-100 text-base sm:text-lg max-w-2xl leading-relaxed drop-shadow-sm font-normal">
                    Find the right age-group squad and session for your child. Weekly training in Tottenham and matchdays at Hackney Marshes.
                </p>
            </div>
        </section>

        <!-- Activities Catalog Grid -->
        <section class="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div v-if="activities.length > 0" class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="activity in activities"
                    :key="activity.id"
                    class="rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-purple-500/40 p-7 flex flex-col justify-between shadow-xl transition-all duration-200"
                >
                    <div class="space-y-4">
                        <div class="flex justify-between items-start">
                            <span class="px-3 py-1 rounded-md bg-slate-950 text-purple-300 border border-slate-800 text-xs font-semibold flex items-center gap-1.5">
                                <Clock class="w-3.5 h-3.5 text-purple-400" />
                                {{ activity.duration_minutes }} mins
                            </span>
                            <span class="text-base font-extrabold text-white">
                                {{ formatPrice(activity.price, activity.currency) }}
                            </span>
                        </div>

                        <h3 class="text-2xl font-bold text-white leading-snug">
                            {{ activity.name }}
                        </h3>

                        <p class="text-slate-400 text-sm leading-relaxed line-clamp-3">
                            {{ activity.description }}
                        </p>
                    </div>

                    <div class="pt-6 border-t border-slate-800/80 mt-6 space-y-4">
                        <div class="flex items-center gap-2 text-xs text-slate-400">
                            <MapPin class="w-4 h-4 text-purple-400 shrink-0" />
                            <span class="truncate">{{ activity.location }}</span>
                        </div>

                        <Link
                            :href="`/bookings/${activity.slug}`"
                            class="w-full py-3.5 px-4 rounded-xl bg-[var(--brand-primary)] hover:bg-purple-700 text-white text-sm font-semibold flex items-center justify-center gap-2 transition-colors shadow-md"
                        >
                            <span>Book a Trial</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20 bg-slate-900 rounded-3xl border border-slate-800 space-y-4 max-w-xl mx-auto">
                <h3 class="text-xl font-bold text-white">No Active Squad Sessions Available</h3>
                <p class="text-slate-400 text-sm">Check back soon for upcoming club schedule updates or contact our coaches directly.</p>
                <div class="pt-2">
                    <Link href="/contact" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-800 text-white text-sm font-semibold hover:bg-slate-700">
                        Contact Coaches
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
