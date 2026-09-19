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
    <Head title="Privacy Policy — TopGrade London FC" />

    <div class="min-h-screen bg-tg-bg text-tg-text py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-10">
            <div>
                <span class="inline-block px-3 py-1 rounded-xs bg-tg-bg-deep border border-tg-border text-tg-accent text-xs font-bold tracking-widest uppercase mb-4">
                    Club Policy
                </span>
                <h1
                    class="text-3xl sm:text-5xl font-normal uppercase tracking-tight text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    {{ page?.title || 'Privacy Policy' }}
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
                            1. Information We Collect
                        </h2>
                        <p class="text-tg-text text-sm sm:text-base leading-relaxed">
                            TopGrade London FC collects information you provide directly when booking a trial session, submitting a contact enquiry, or participating in club activities. This includes parent/guardian contact details (name, email, phone number) and participant details (player name, age, relevant notes).
                        </p>
                    </section>

                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            2. How We Use Information
                        </h2>
                        <p class="text-tg-text text-sm sm:text-base leading-relaxed">
                            We use the collected information exclusively to manage trial sessions, coordinate team schedules, communicate with parents, ensure player welfare during sessions, and respond to inbound enquiries.
                        </p>
                    </section>

                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            3. Data Protection & Security
                        </h2>
                        <p class="text-tg-text text-sm sm:text-base leading-relaxed">
                            We take data protection seriously and adhere to UK GDPR and applicable data protection legislation. We do not sell or share personal data with third-party marketers.
                        </p>
                    </section>

                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            4. Contact Us
                        </h2>
                        <p class="text-tg-text text-sm sm:text-base leading-relaxed">
                            If you have any questions regarding your data or wish to request deletion, please contact us at
                            <a href="mailto:topgradelondonfc@hotmail.com" class="text-tg-accent hover:text-tg-accent-hover underline">topgradelondonfc@hotmail.com</a>.
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
