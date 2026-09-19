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

    <div class="min-h-screen bg-tg-bg text-tg-text">
        <!-- Hero Section: Solid Athletic Presentation -->
        <section class="relative min-h-[36vh] sm:min-h-[40vh] flex items-end overflow-hidden pb-12 pt-24 sm:pt-28 border-b border-tg-border">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-35"
                style="background-image: url('/images/club/training_pitch_evening.jpg')"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-tg-bg via-tg-bg/85 to-transparent" />

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-3">
                <span class="inline-block px-3 py-1 rounded-xs bg-tg-bg-deep border border-tg-border text-tg-accent text-xs font-bold tracking-widest uppercase">
                    Club Bookings
                </span>

                <h1
                    class="text-3xl sm:text-5xl font-normal uppercase tracking-tight text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    Training Sessions & Bookings
                </h1>
                <p class="text-tg-text text-sm sm:text-base max-w-2xl">
                    Select an introductory trial or training session for your child. Weekly coaching in Tottenham and Saturday league match play at Hackney Marshes.
                </p>
            </div>
        </section>

        <!-- Activities Catalog Grid -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div v-if="activities.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="activity in activities"
                    :key="activity.id"
                    class="rounded-sm bg-tg-bg-deep/80 border border-tg-border p-6 flex flex-col justify-between space-y-6 transition-all hover:border-tg-border-strong"
                >
                    <div class="space-y-4">
                        <div class="flex justify-between items-start">
                            <span class="px-2.5 py-1 rounded-xs bg-tg-bg text-tg-text-muted border border-tg-border text-xs font-medium flex items-center gap-1.5">
                                <Clock class="w-3.5 h-3.5 text-tg-accent" />
                                <span>{{ activity.duration_minutes }} mins</span>
                            </span>
                            <span class="text-base font-bold text-tg-text-strong">
                                {{ formatPrice(activity.price, activity.currency) }}
                            </span>
                        </div>

                        <h3
                            class="text-xl font-normal text-tg-text-strong uppercase tracking-tight"
                            style="font-family: var(--tg-display);"
                        >
                            {{ activity.name }}
                        </h3>

                        <p class="text-tg-text-muted text-xs leading-relaxed line-clamp-3">
                            {{ activity.description }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-tg-border space-y-4">
                        <div class="flex items-center gap-2 text-xs text-tg-text-muted">
                            <MapPin class="w-3.5 h-3.5 text-tg-accent shrink-0" />
                            <span class="truncate">{{ activity.location }}</span>
                        </div>

                        <Link
                            :href="`/bookings/${activity.slug}`"
                            class="tg-btn w-full justify-center text-xs"
                        >
                            <span>{{ activity.slug.includes('trial') ? 'Book a Trial' : 'Book Session' }}</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-16 bg-tg-bg-deep rounded-sm border border-tg-border space-y-4 max-w-xl mx-auto p-8">
                <h3
                    class="text-lg font-normal text-tg-text-strong uppercase tracking-tight"
                    style="font-family: var(--tg-display);"
                >
                    No Active Sessions Available
                </h3>
                <p class="text-tg-text-muted text-xs leading-relaxed">Check back soon for upcoming schedule updates or contact the club directly.</p>
                <div class="pt-2">
                    <Link href="/contact" class="tg-btn ghost text-xs">
                        Contact the Club
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
