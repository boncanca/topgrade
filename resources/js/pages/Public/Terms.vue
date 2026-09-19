<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';

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
    <Head title="Terms & Conditions — TopGrade London FC" />

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
                <p class="text-tg-text-muted text-sm mt-2">Last updated: September 2026</p>
            </div>

            <div class="rounded-xs border border-tg-border bg-tg-bg-deep/80 p-6 sm:p-10 space-y-8">
                <div v-if="page?.content" class="text-tg-text space-y-6 text-sm sm:text-base leading-relaxed" v-html="page.content" />
                <template v-else>
                    <section class="space-y-3">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            1. Club Participation & Trials
                        </h2>
                        <p class="text-tg-text text-sm sm:text-base leading-relaxed">
                            By booking a trial session or registering with TopGrade London FC, parents and guardians agree to provide accurate participant information. All participants must follow the guidance and safety instructions of club coaches during training.
                        </p>
                    </section>

                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            2. Health & Welfare
                        </h2>
                        <p class="text-tg-text text-sm sm:text-base leading-relaxed">
                            Parents and guardians must notify the club of any relevant medical conditions or physical needs prior to session participation. Suitable footwear (boots/astroturf trainers) and shin pads must be worn for all football sessions.
                        </p>
                    </section>

                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            3. Attendance & Cancellations
                        </h2>
                        <p class="text-tg-text text-sm sm:text-base leading-relaxed">
                            If you are unable to attend a booked trial or training session, please notify the club in advance so the place can be allocated to another young player.
                        </p>
                    </section>

                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            4. Code of Conduct
                        </h2>
                        <p class="text-tg-text text-sm sm:text-base leading-relaxed">
                            TopGrade London FC promotes positive sportsmanship, respect, and discipline. Disrespectful behavior towards coaches, teammates, opponents, or referees will not be tolerated.
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
