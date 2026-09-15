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

    <div class="min-h-screen bg-slate-950 text-white py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto space-y-8">
            <div>
                <span class="inline-block px-3 py-1 rounded-full bg-slate-800 text-purple-300 text-xs font-semibold uppercase tracking-wider mb-4">
                    Club Policy
                </span>
                <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight">
                    {{ page?.title || 'Privacy Policy' }}
                </h1>
                <p class="text-slate-400 text-sm mt-2">Last updated: September 2026</p>
            </div>

            <div class="prose prose-invert prose-slate max-w-none text-slate-300 space-y-6 text-sm sm:text-base leading-relaxed">
                <div v-if="page?.content" v-html="page.content" />
                <template v-else>
                    <section class="space-y-3">
                        <h2 class="text-xl font-bold text-white">1. Information We Collect</h2>
                        <p>
                            TopGrade London FC collects information you provide directly when booking a trial session, submitting a contact enquiry, or participating in club activities. This includes parent/guardian contact details (name, email, phone number) and participant details (player name, age, relevant notes).
                        </p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-bold text-white">2. How We Use Information</h2>
                        <p>
                            We use the collected information exclusively to manage trial sessions, coordinate team schedules, communicate with parents, ensure player welfare during sessions, and respond to inbound enquiries.
                        </p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-bold text-white">3. Data Protection & Security</h2>
                        <p>
                            We take data protection seriously and adhere to UK GDPR and applicable data protection legislation. We do not sell or share personal data with third-party marketers.
                        </p>
                    </section>

                    <section class="space-y-3">
                        <h2 class="text-xl font-bold text-white">4. Contact Us</h2>
                        <p>
                            If you have any questions regarding your data or wish to request deletion, please contact us at
                            <a href="mailto:info@topgradefc.com" class="text-[var(--brand-secondary)] hover:underline">info@topgradefc.com</a>.
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
