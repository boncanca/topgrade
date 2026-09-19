<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import BlockRenderer from '@/components/CMS/BlockRenderer.vue';

interface Block {
    id: number;
    uuid: string;
    type: string;
    payload: any;
    settings?: any;
}

interface PageContent {
    id: number;
    title: string;
    slug: string;
    content?: string;
    blocks?: Block[];
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
                    Club Governance
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
                <!-- CMS Blocks if present -->
                <div v-if="page?.blocks && page.blocks.length > 0">
                    <BlockRenderer :blocks="page.blocks" />
                </div>

                <!-- Custom Full Rich Text Content if extensive CMS content is present -->
                <div v-else-if="page?.content && page.content.length > 300" class="text-tg-text space-y-6" v-html="page.content" />

                <!-- Standard Consolidated Editorial Content -->
                <div v-else class="space-y-8">
                    <p v-if="page?.content" class="text-tg-text-muted italic border-l-2 border-tg-accent pl-4">
                        {{ page.content }}
                    </p>
                    <!-- Section 1 -->
                    <section class="space-y-3">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            1. Agreement &amp; Organization
                        </h2>
                        <p class="text-tg-text">
                            These Terms and Conditions govern the use of the TopGrade London FC website and participation in youth football training, introductory trials, and match activities organised by <strong class="text-tg-text-strong">TOPGRADE LONDON FC CIC</strong> (Company No. 14087076). By using our website or submitting a booking, parents and guardians agree to these terms on behalf of themselves and participating youth players.
                        </p>
                    </section>

                    <!-- Section 2 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            2. Club Activities, Training &amp; Trials
                        </h2>
                        <p class="text-tg-text">
                            TopGrade London FC delivers structured youth football coaching across technical development, game understanding, teamwork, and competitive match play for age groups U7 through U16.
                        </p>
                        <p class="text-tg-text">
                            Introductory trial sessions allow prospective players to experience our coaching and squad environment. Trial places are subject to group capacity and coach availability.
                        </p>
                    </section>

                    <!-- Section 3 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            3. Bookings, Capacity &amp; Participant Information
                        </h2>
                        <p class="text-tg-text">
                            All session requests must be submitted by a parent or legal guardian with accurate contact information, emergency numbers, and participant details. Because player safety and drill quality require manageable coach-to-player ratios, schedules enforce strict capacity limits. Duplicate bookings for the same participant on the same schedule may be cancelled.
                        </p>
                    </section>

                    <!-- Section 4 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            4. Health, Mandatory Kit &amp; Player Welfare
                        </h2>
                        <p class="text-tg-text">
                            Player safety is paramount. Parents and guardians must disclose any medical conditions or physical needs prior to a player's first session.
                        </p>
                        <ul class="list-disc pl-5 space-y-1.5 text-tg-text-muted text-sm">
                            <li><strong class="text-tg-text">Mandatory Shin Guards:</strong> Shin pads must be worn under football socks during all contact drills and match play. Players without shin guards cannot participate in contact drills.</li>
                            <li><strong class="text-tg-text">Footwear:</strong> Suitable football boots or astroturf trainers suited to 3G all-weather floodlit pitches or indoor sports hall surfaces.</li>
                            <li><strong class="text-tg-text">Hydration:</strong> Every player must bring a filled water bottle to every session.</li>
                        </ul>
                    </section>

                    <!-- Section 5 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            5. Attendance, Cancellations &amp; Weather Updates
                        </h2>
                        <p class="text-tg-text">
                            If a player cannot attend a booked session, please inform the club in advance at <a href="mailto:info@topgradelondonfc.co.uk" class="text-tg-accent hover:underline">info@topgradelondonfc.co.uk</a> so the space can be offered to another youth player.
                        </p>
                        <p class="text-tg-text">
                            In the event of severe weather, pitch closures, or venue unavailability, the club will notify registered parents via email or SMS before the session begins.
                        </p>
                    </section>

                    <!-- Section 6 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            6. Parent &amp; Player Code of Conduct
                        </h2>
                        <p class="text-tg-text">
                            TopGrade London FC upholds a positive and respectful football environment. Players must listen to coaches, encourage teammates, and play fairly. Spectators and parents must support encouragingly from designated touchline areas without encroaching on the pitch or dissenting against match officials.
                        </p>
                    </section>

                    <!-- Section 7 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            7. Photography &amp; Media
                        </h2>
                        <p class="text-tg-text">
                            From time to time, official club photography captures training drills and matchday celebrations for editorial reporting and website promotion. We respect parent preferences; any parent wishing to opt out of public media may notify the club at any time.
                        </p>
                    </section>

                    <!-- Section 8 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            8. Intellectual Property &amp; Website Use
                        </h2>
                        <p class="text-tg-text">
                            All text, trademarks, club crests, photography, and digital materials on this website are the property of TOPGRADE LONDON FC CIC or its licensors and are protected under UK intellectual property laws.
                        </p>
                    </section>

                    <!-- Section 9 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            9. Limitation of Liability &amp; Governing Law
                        </h2>
                        <p class="text-tg-text">
                            Football is an active, physical sport with inherent risks of accidental bumps, bruises, and sports injuries. While TopGrade coaches exercise reasonable care and safety management, participation is undertaken acknowledging standard sporting risks.
                        </p>
                        <p class="text-tg-text">
                            These terms are governed by and construed in accordance with the laws of England and Wales, subject to the exclusive jurisdiction of the English courts.
                        </p>
                    </section>

                    <!-- Section 10 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            10. Contact Us
                        </h2>
                        <p class="text-tg-text">
                            For any questions regarding these terms, club policies, or sessions:
                        </p>
                        <p class="text-tg-text font-medium">
                            Email: <a href="mailto:info@topgradelondonfc.co.uk" class="text-tg-accent hover:underline">info@topgradelondonfc.co.uk</a><br />
                            Postal: TOPGRADE LONDON FC CIC, 30 Broadwater Road, London, England, N17 6ES
                        </p>
                    </section>
                </div>
            </div>

            <div class="pt-4 border-t border-tg-border flex items-center justify-between">
                <Link href="/" class="tg-link inline-flex items-center gap-1.5 text-xs">
                    ← Return to Home
                </Link>
                <Link href="/privacy" class="tg-link inline-flex items-center gap-1.5 text-xs">
                    Privacy Policy →
                </Link>
            </div>
        </div>
    </div>
</template>
