<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';

interface PageContent {
    id: number;
    title: string;
    slug: string;
    content?: string;
}

defineProps<{
    page?: PageContent | null;
}>();

defineOptions({
    layout: PublicLayout,
});
</script>

<template>
    <SeoHead
        title="Terms &amp; Conditions | TopGrade London FC"
        description="TopGrade London FC club terms and conditions, trial session policies, health guidelines, and player codes of conduct."
        path="/terms"
    />

    <div class="min-h-screen bg-tg-bg text-tg-text py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-10">
            <div>
                <span class="inline-block px-3 py-1 rounded-xs bg-tg-bg-deep border border-tg-border text-tg-accent text-xs font-bold tracking-widest uppercase mb-4">
                    Club Terms
                </span>
                <h1
                    class="text-3xl sm:text-5xl font-normal uppercase tracking-tight text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    {{ page?.title || 'Terms & Conditions' }}
                </h1>
                <p class="text-tg-text-muted text-sm mt-2">Last updated: September 2026 · TOPGRADE LONDON FC CIC</p>
            </div>

            <div class="rounded-xs border border-tg-border bg-tg-bg-deep/80 p-6 sm:p-10 space-y-8 text-sm sm:text-base leading-relaxed">
                <div v-if="page?.content" class="text-tg-text space-y-6" v-html="page.content" />
                <template v-else>
                    <section class="space-y-3">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            1. Club Participation &amp; Trials
                        </h2>
                        <p class="text-tg-text">
                            By booking a trial session or registering a child with TopGrade London FC, parents and guardians agree to provide accurate participant details. All young players must follow the guidance and safety directions of club coaches during training drills and matches.
                        </p>
                    </section>

                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            2. Health, Kit &amp; Safety
                        </h2>
                        <p class="text-tg-text">
                            Parents and guardians must notify the club of any relevant medical conditions, allergies, or physical needs prior to participation. Suitable footwear (moulded football boots or astroturf trainers) and shin pads are mandatory for all training sessions and match fixtures. Players without shin guards may be excluded from full-contact play for their own protection.
                        </p>
                    </section>

                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            3. Session Attendance &amp; Cancellations
                        </h2>
                        <p class="text-tg-text">
                            If you are unable to attend a booked trial or training session, please notify the club in advance so that training coaches can balance group numbers and allocate the place to another young player.
                        </p>
                    </section>

                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            4. Code of Conduct &amp; Respect
                        </h2>
                        <p class="text-tg-text">
                            TopGrade London FC promotes positive sportsmanship, respect, and discipline. Disrespectful behaviour toward coaches, teammates, opponents, or match referees from either players or spectators will not be tolerated.
                        </p>
                    </section>
                </template>
            </div>

            <div class="pt-4 border-t border-tg-border">
                <Link href="/" class="tg-link inline-flex items-center gap-1.5 text-xs">
                    ← Return to Home
                </Link>
            </div>
        </div>
    </div>
</template>
