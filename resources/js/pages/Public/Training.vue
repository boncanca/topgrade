<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { Clock, MapPin, ArrowRight } from '@lucide/vue';

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
    <Head title="Training Programmes - TopGrade London FC" />

    <div class="min-h-screen bg-slate-950 text-white">
        <!-- Hero Section -->
        <section class="relative min-h-[35vh] flex items-end overflow-hidden pb-12 pt-28 border-b border-slate-800">
            <div
                class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30"
                style="background-image: url('/images/hero_action.png')"
            />
            <div class="absolute inset-0 bg-slate-950/80" />

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-4">
                <div class="inline-block px-3 py-1 rounded bg-slate-800 border border-slate-700 text-slate-300 text-xs font-semibold tracking-wider uppercase">
                    Academy Programmes
                </div>

                <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                    Comprehensive Football Training
                </h1>
                <p class="text-slate-300 text-base max-w-2xl">
                    Structured player development for all age groups and skill levels across London.
                </p>
            </div>
        </section>

        <!-- Programs Grid -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="activity in activities"
                    :key="activity.id"
                    class="rounded-xl bg-slate-900 border border-slate-800 p-6 flex flex-col justify-between"
                >
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <span class="px-3 py-1 rounded bg-slate-800 text-slate-300 text-xs font-medium flex items-center gap-1">
                                <Clock class="w-3.5 h-3.5" />
                                <span>{{ activity.duration_minutes }} mins</span>
                            </span>
                            <span class="text-xl font-bold text-white">
                                {{ formatPrice(activity.price, activity.currency) }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-white mb-2">
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
                            class="w-full py-3 px-4 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold flex items-center justify-center gap-2 transition-colors"
                        >
                            <span>Book Session</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

