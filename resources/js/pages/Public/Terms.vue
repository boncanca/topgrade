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

    <div class="min-h-screen bg-slate-950 text-white py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-8">
            <div>
                <span class="inline-block px-3 py-1 rounded-full bg-slate-800 text-purple-300 text-xs font-semibold uppercase tracking-wider mb-4">
                    Club Terms
                </span>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                    {{ page?.title || 'Terms & Conditions' }}
                </h1>
                <p class="text-slate-400 text-sm mt-2">Last updated: September 2026</p>
            </div>

            <div class="prose prose-invert prose-slate max-w-none text-slate-300 space-y-6 text-sm sm:text-base leading-relaxed">
                <div v-if="page?.content" v-html="page.content" />
                <template v-else>
                    <section class="space-y-3">
                        <h2 class="text-xl font-bold text-white">1. Club Participation & Trials</h2>
                        <p>
                            By booking a trial session or registering with TopGrade London FC, parents and guardians agree to provide accurate participant information. All participants must follow the guidance and safety instructions of club coaches during training.
                        </p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-bold text-white">2. Health & Welfare</h2>
                        <p>
                            Parents and guardians must notify the club of any relevant medical conditions or physical needs prior to session participation. Suitable footwear (boots/astroturf trainers) and shin pads must be worn for all football sessions.
                        </p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-bold text-white">3. Attendance & Cancellations</h2>
                        <p>
                            If you are unable to attend a booked trial or training session, please notify the club in advance so the place can be allocated to another young player.
                        </p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-bold text-white">4. Code of Conduct</h2>
                        <p>
                            TopGrade London FC promotes positive sportsmanship, respect, and discipline. Disrespectful behavior towards coaches, teammates, opponents, or referees will not be tolerated.
                        </p>
                    </section>
                </template>
            </div>

            <div class="pt-8 border-t border-slate-800">
                <Link href="/" class="text-sm text-slate-400 hover:text-white transition-colors">
                    ← Return to Home
                </Link>
            </div>
        </div>
    </div>
</template>
