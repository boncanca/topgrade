<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { MapPin, Clock, ArrowRight } from '@lucide/vue';

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
        return 'FREE';
    }
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: currencyStr || 'GBP',
    }).format(parseFloat(priceStr));
}
</script>

<template>
    <Head title="Book a Session - TopGrade London FC" />

    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white transition-colors">
        <!-- Hero Section with Background Image -->
        <section class="relative min-h-[40vh] flex items-end overflow-hidden pb-12 pt-28 bg-slate-950 text-white">
            <div
                class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-40 scale-105 transition-transform duration-1000"
                style="background-image: url('/images/hero_action.png')"
            />
            <div class="absolute inset-0 bg-slate-950/80" />

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-4">
                <div class="inline-block px-3 py-1 rounded bg-slate-800 border border-slate-700 text-slate-300 text-xs font-semibold tracking-wider uppercase">
                    Session Catalog
                </div>

                <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                    Academy Training Sessions
                </h1>
                <p class="text-slate-300 text-base max-w-2xl">
                    Select a programme below to view available session slots, pitch locations, and book your player's place.
                </p>
            </div>
        </section>

        <!-- Activities Catalog Grid -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div v-if="activities.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="activity in activities"
                    :key="activity.id"
                    class="rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 flex flex-col justify-between shadow-xs transition-colors"
                >
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <span class="px-3 py-1 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-medium flex items-center gap-1">
                                <Clock class="w-3.5 h-3.5" />
                                <span>{{ activity.duration_minutes }} mins</span>
                            </span>
                            <span class="text-xl font-bold text-purple-600 dark:text-white">
                                {{ formatPrice(activity.price, activity.currency) }}
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
                            <span class="truncate">{{ activity.location }}</span>
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

            <!-- Empty State -->
            <div v-else class="text-center py-16 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 space-y-4">
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">No Active Sessions Available</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm">Check back soon for upcoming academy schedule announcements.</p>
            </div>
        </section>
    </div>
</template>
