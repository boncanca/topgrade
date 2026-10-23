<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { MapPin, Clock, ArrowRight } from '@lucide/vue';
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
</script>

<template>
    <SeoHead
        title="Bookings | TopGrade London FC"
        description="Book youth football trial sessions and squad training with TopGrade London FC in Tottenham and Hackney."
        path="/bookings"
    />

    <div class="bg-tg-bg text-tg-text min-h-screen">
        <!-- Hero Section: Solid Athletic Presentation -->
        <section
            class="border-tg-border relative flex min-h-[36vh] items-end overflow-hidden border-b pb-12 pt-24 sm:min-h-[40vh] sm:pt-28"
        >
            <div
                class="absolute inset-0 bg-cover bg-center opacity-35"
                style="
                    background-image: url('/images/club/topgrade-london-fc-floodlit-training-pitch.webp');
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
                    Club Bookings
                </span>

                <h1
                    class="text-tg-text-strong text-3xl font-normal uppercase tracking-tight sm:text-5xl"
                    style="font-family: var(--tg-display)"
                >
                    Training Sessions & Bookings
                </h1>
                <p class="text-tg-text max-w-2xl text-sm sm:text-base">
                    Select an introductory trial or training session for your
                    child. Weekly coaching in Tottenham and Saturday league
                    match play at Hackney Marshes.
                </p>
            </div>
        </section>

        <!-- Activities Catalog Grid -->
        <section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div
                v-if="activities.length > 0"
                class="grid gap-6 md:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="activity in activities"
                    :key="activity.id"
                    class="bg-tg-bg-deep/80 border-tg-border hover:border-tg-border-strong flex flex-col justify-between space-y-6 rounded-sm border p-6 transition-all"
                >
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span
                                class="rounded-xs bg-tg-bg text-tg-text-muted border-tg-border flex items-center gap-1.5 border px-2.5 py-1 text-xs font-medium"
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
                            <ArrowRight class="h-3.5 w-3.5" />
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="bg-tg-bg-deep border-tg-border mx-auto max-w-xl space-y-4 rounded-sm border p-8 py-16 text-center"
            >
                <h3
                    class="text-tg-text-strong text-lg font-normal uppercase tracking-tight"
                    style="font-family: var(--tg-display)"
                >
                    No Active Sessions Available
                </h3>
                <p class="text-tg-text-muted text-xs leading-relaxed">
                    Check back soon for upcoming schedule updates or contact the
                    club directly.
                </p>
                <div class="pt-2">
                    <Link href="/contact" class="tg-btn ghost text-xs">
                        Contact the Club
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
