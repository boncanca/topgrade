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
        return 'FREE TRIAL';
    }
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: currencyStr || 'GBP',
    }).format(parseFloat(priceStr));
}
</script>

<template>
    <Head title="Bookings & Sessions — TopGrade London FC" />

    <div class="min-h-screen bg-slate-950 text-white">
        <!-- Hero Section: Solid Athletic Presentation -->
        <section class="relative min-h-[40vh] flex items-end overflow-hidden pb-12 pt-28 border-b border-slate-800">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-50"
                style="background-image: url('/images/club/training_pitch_evening.jpg')"
            />
            <div class="absolute inset-0 bg-black/70" />

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-3">
                <span class="inline-block px-3 py-1 rounded bg-purple-900/60 border border-purple-700/60 text-purple-200 text-xs font-bold tracking-widest uppercase">
                    Squad Bookings
                </span>

                <h1 class="text-3xl sm:text-5xl font-extrabold text-white uppercase tracking-tight">
                    Club Squad Bookings & Sessions
                </h1>
                <p class="text-slate-300 text-base max-w-2xl">
                    Select a squad session for your child. Weekly training in Tottenham and Saturday league match play at Hackney Marshes.
                </p>
            </div>
        </section>

        <!-- Activities Catalog Grid -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div v-if="activities.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="activity in activities"
                    :key="activity.id"
                    class="rounded bg-slate-900 border border-slate-800 p-6 flex flex-col justify-between space-y-6"
                >
                    <div class="space-y-4">
                        <div class="flex justify-between items-start">
                            <span class="px-2.5 py-1 rounded bg-slate-950 text-slate-300 border border-slate-800 text-xs font-medium flex items-center gap-1.5">
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

                        <p class="text-slate-400 text-xs leading-relaxed line-clamp-3">
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

            <!-- Empty State -->
            <div v-else class="text-center py-16 bg-slate-900 rounded border border-slate-800 space-y-4 max-w-xl mx-auto p-8">
                <h3 class="text-lg font-bold text-white uppercase tracking-tight">No Active Sessions Available</h3>
                <p class="text-slate-400 text-xs leading-relaxed">Check back soon for upcoming schedule updates or contact the club directly.</p>
                <div class="pt-2">
                    <Link href="/contact" class="inline-block px-5 py-2.5 rounded bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold uppercase tracking-wider transition-colors">
                        Contact the Club
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
