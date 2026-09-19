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
        title="Privacy Policy | TopGrade London FC"
        description="TopGrade London FC privacy policy detailing how we collect, protect, and process parent, guardian, and youth player information under UK GDPR."
        path="/privacy"
    />

    <div class="min-h-screen bg-tg-bg text-tg-text py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-10">
            <div>
                <span class="inline-block px-3 py-1 rounded-xs bg-tg-bg-deep border border-tg-border text-tg-accent text-xs font-bold tracking-widest uppercase mb-4">
                    Data Protection Notice
                </span>
                <h1
                    class="text-3xl sm:text-5xl font-normal uppercase tracking-tight text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    {{ page?.title || 'Privacy Policy' }}
                </h1>
                <p class="text-tg-text-muted text-sm mt-2">Last updated: September 2026 · UK GDPR &amp; DPA 2018 Compliant</p>
            </div>

            <div class="rounded-xs border border-tg-border bg-tg-bg-deep/80 p-6 sm:p-10 space-y-8 text-sm sm:text-base leading-relaxed">
                <div v-if="page?.content" class="text-tg-text space-y-6" v-html="page.content" />
                <template v-else>
                    <!-- Section 1 -->
                    <section class="space-y-3">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            1. Who We Are
                        </h2>
                        <p class="text-tg-text">
                            TopGrade London FC operates as <strong class="text-tg-text-strong">TOPGRADE LONDON FC CIC</strong> (Company No. 14087076), a Community Interest Company registered in England and Wales. Our registered office is 30 Broadwater Road, London, England, N17 6ES.
                        </p>
                        <p class="text-tg-text">
                            We act as the Data Controller responsible for the personal data collected from parents, guardians, and youth participants who engage with our training sessions, trial bookings, and club fixtures.
                        </p>
                    </section>

                    <!-- Section 2 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            2. Information We Collect
                        </h2>
                        <p class="text-tg-text">
                            Because TopGrade is a youth football club handling activities for young players, we take child data protection seriously:
                        </p>
                        <ul class="list-disc pl-5 space-y-2 text-tg-text-muted text-sm">
                            <li>
                                <strong class="text-tg-text">Parent / Guardian Information:</strong> Full name, email address, telephone number, and emergency contact details.
                            </li>
                            <li>
                                <strong class="text-tg-text">Participant (Child) Information:</strong> First name, surname, approximate age / age group (e.g. U9, U12), and training group.
                            </li>
                            <li>
                                <strong class="text-tg-text">Health &amp; Welfare Notes (Data Minimisation):</strong> Only relevant medical notes or physical conditions volunteered by parents to ensure player safety during training drills.
                            </li>
                            <li>
                                <strong class="text-tg-text">Media &amp; Photography Preferences:</strong> Explicit opt-in preferences for team celebrations and pitch matchday photography.
                            </li>
                        </ul>
                    </section>

                    <!-- Section 3 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            3. Lawful Basis for Processing
                        </h2>
                        <p class="text-tg-text">
                            Under UK GDPR, we process personal data under the following lawful bases:
                        </p>
                        <ul class="list-disc pl-5 space-y-2 text-tg-text-muted text-sm">
                            <li><strong class="text-tg-text">Contractual Necessity:</strong> To organize, manage, and deliver booked trial and training sessions.</li>
                            <li><strong class="text-tg-text">Vital Interests:</strong> To respond swiftly to any sudden medical need or emergency during sessions.</li>
                            <li><strong class="text-tg-text">Legitimate Interests:</strong> To coordinate club scheduling, communicate with registered families, and maintain touchline safety.</li>
                            <li><strong class="text-tg-text">Consent:</strong> For matchday photography and social media highlights of squad achievements.</li>
                        </ul>
                    </section>

                    <!-- Section 4 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            4. Data Retention &amp; Security
                        </h2>
                        <p class="text-tg-text">
                            Personal data is retained only for as long as necessary to administer your child's participation in club training and fixtures, plus any period required for child safety or insurance records. We do not sell, rent, or trade your personal data to any commercial third parties.
                        </p>
                    </section>

                    <!-- Section 5 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            5. Your Rights Under UK GDPR
                        </h2>
                        <p class="text-tg-text">
                            Parents and guardians have the right to request access to personal data we hold, request corrections to inaccurate information, request erasure of personal data, or withdraw media consent at any time.
                        </p>
                        <p class="text-tg-text">
                            You also have the right to lodge a complaint with the UK supervisory authority: the Information Commissioner's Office (<a href="https://ico.org.uk" target="_blank" rel="noopener noreferrer" class="text-tg-accent hover:underline">ico.org.uk</a>).
                        </p>
                    </section>

                    <!-- Section 6 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            6. Contacting the Club
                        </h2>
                        <p class="text-tg-text">
                            For any privacy inquiries or to exercise your data rights, please contact us directly:
                        </p>
                        <p class="text-tg-text font-medium">
                            Email: <a href="mailto:topgradelondonfc@hotmail.com" class="text-tg-accent hover:underline">topgradelondonfc@hotmail.com</a><br />
                            Postal: TOPGRADE LONDON FC CIC, 30 Broadwater Road, London, England, N17 6ES
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
