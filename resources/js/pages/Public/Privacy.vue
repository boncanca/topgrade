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
        title="Privacy Policy | TopGrade London FC"
        description="TopGrade London FC privacy policy detailing how we collect, protect, and process parent, guardian, and youth player information under UK GDPR."
        path="/privacy"
    />

    <div class="min-h-screen bg-tg-bg text-tg-text py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-10">
            <div>
                <span class="inline-block px-3 py-1 rounded-xs bg-tg-bg-deep border border-tg-border text-tg-accent text-xs font-bold tracking-widest uppercase mb-4">
                    Data Protection &amp; Governance
                </span>
                <h1
                    class="text-3xl sm:text-5xl font-normal uppercase tracking-tight text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    {{ page?.title || 'Privacy Policy' }}
                </h1>
                <p class="text-tg-text-muted text-sm mt-2">Last updated: September 2026 · UK GDPR &amp; Data Protection Act 2018</p>
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
                            1. Who We Are
                        </h2>
                        <p class="text-tg-text">
                            TopGrade London FC operates as <strong class="text-tg-text-strong">TOPGRADE LONDON FC CIC</strong> (Company No. 14087076), a Community Interest Company registered in England and Wales. Our registered office is 30 Broadwater Road, London, England, N17 6ES.
                        </p>
                        <p class="text-tg-text">
                            We act as the Data Controller responsible for personal data collected from parents, guardians, and youth players participating in our weekly football training sessions, introductory trials, and club fixtures across Tottenham and Hackney.
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
                            We collect only the information necessary to safely organise and deliver football coaching and youth matches:
                        </p>
                        <ul class="list-disc pl-5 space-y-2 text-tg-text-muted text-sm">
                            <li>
                                <strong class="text-tg-text">Parent / Guardian Details:</strong> Full name, email address, contact telephone number, and emergency contact details.
                            </li>
                            <li>
                                <strong class="text-tg-text">Player (Child) Details:</strong> First name, surname, approximate age / squad phase (e.g. U7–U8, U9–U10, U11–U12, U13–U16), and session attendance.
                            </li>
                            <li>
                                <strong class="text-tg-text">Safety &amp; Medical Notes:</strong> Relevant medical information (e.g. asthma, allergies) volunteered by parents solely to ensure player welfare during drills.
                            </li>
                            <li>
                                <strong class="text-tg-text">Enquiry Messages:</strong> Questions submitted through our website contact form.
                            </li>
                            <li>
                                <strong class="text-tg-text">Photography &amp; Media Preferences:</strong> Opt-in preferences regarding team celebration photos and matchday highlights.
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
                            Under UK GDPR, we process personal information under the following lawful bases:
                        </p>
                        <ul class="list-disc pl-5 space-y-2 text-tg-text-muted text-sm">
                            <li><strong class="text-tg-text">Contractual Necessity:</strong> To process session bookings, manage squad training timetables, and confirm trial bookings.</li>
                            <li><strong class="text-tg-text">Vital Interests:</strong> To ensure immediate medical attention can be provided if a player is injured or taken unwell during a session.</li>
                            <li><strong class="text-tg-text">Legitimate Interests:</strong> To coordinate club scheduling, communicate pitch updates to parents, and maintain touchline safety.</li>
                            <li><strong class="text-tg-text">Consent:</strong> For squad celebration photography and social media highlights. Consent may be updated or withdrawn at any time.</li>
                        </ul>
                    </section>

                    <!-- Section 4 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            4. Children's Data &amp; Safeguarding
                        </h2>
                        <p class="text-tg-text">
                            TopGrade London FC takes children's privacy and player welfare with utmost seriousness. We apply strict data minimisation: we do not create online user accounts for youth players, we do not track player browsing behavior, and we never share youth data with commercial advertising networks or third-party marketing entities.
                        </p>
                        <p class="text-tg-text">
                            If any parent or guardian has child welfare concerns or questions regarding player data protection, they may contact our club office directly at <a href="mailto:info@topgradelondonfc.co.uk" class="text-tg-accent hover:underline">info@topgradelondonfc.co.uk</a>.
                        </p>
                    </section>

                    <!-- Section 5 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            5. Cookies &amp; Tracking Technologies
                        </h2>
                        <p class="text-tg-text">
                            Our website operates with zero third-party advertising cookies, zero marketing trackers, and zero behavioral tracking pixels.
                        </p>
                        <p class="text-tg-text">
                            We use only strictly necessary, first-party technical cookies essential for secure website operation:
                        </p>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border border-tg-border mt-2">
                                <thead class="bg-tg-bg border-b border-tg-border text-tg-text-strong uppercase tracking-wider font-semibold">
                                    <tr>
                                        <th class="p-2.5">Cookie Name</th>
                                        <th class="p-2.5">Provider</th>
                                        <th class="p-2.5">Purpose</th>
                                        <th class="p-2.5">Duration</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-tg-border/60 text-tg-text-muted">
                                    <tr>
                                        <td class="p-2.5 font-mono text-tg-accent">laravel_session</td>
                                        <td class="p-2.5">TopGrade FC</td>
                                        <td class="p-2.5">Maintains user session state during booking forms</td>
                                        <td class="p-2.5">2 Hours</td>
                                    </tr>
                                    <tr>
                                        <td class="p-2.5 font-mono text-tg-accent">XSRF-TOKEN</td>
                                        <td class="p-2.5">TopGrade FC</td>
                                        <td class="p-2.5">Protects forms against Cross-Site Request Forgery (CSRF)</td>
                                        <td class="p-2.5">Session</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Section 6 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            6. Third-Party Services
                        </h2>
                        <p class="text-tg-text">
                            We only use secure, trusted infrastructure providers to operate the club website:
                        </p>
                        <ul class="list-disc pl-5 space-y-1.5 text-tg-text-muted text-sm">
                            <li><strong class="text-tg-text">Cloud &amp; Server Hosting:</strong> Secure UK/EU infrastructure for application hosting and database encryption.</li>
                            <li><strong class="text-tg-text">Transactional Email Delivery:</strong> Secure API email service to send automated booking confirmations and inquiry receipts.</li>
                        </ul>
                    </section>

                    <!-- Section 7 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            7. Data Retention &amp; Security
                        </h2>
                        <p class="text-tg-text">
                            Personal data is retained only for as long as needed to administer your child's training sessions and club activities, plus statutory retention periods for insurance and emergency safety records. All stored records are protected using industry-standard TLS encryption in transit and secure database access controls.
                        </p>
                    </section>

                    <!-- Section 8 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            8. Your Rights &amp; Accessibility Assistance
                        </h2>
                        <p class="text-tg-text">
                            Under UK GDPR, you have the right to request access to the personal data we hold, request correction of inaccurate records, request deletion of data where retention is no longer necessary, and withdraw photography consent.
                        </p>
                        <p class="text-tg-text">
                            We are committed to making our information accessible to all users. If you require this policy or club communications in an alternative format, or need assistance accessing club services, please let us know.
                        </p>
                        <p class="text-tg-text">
                            You also have the right to lodge a complaint with the UK Information Commissioner's Office (<a href="https://ico.org.uk" target="_blank" rel="noopener noreferrer" class="text-tg-accent hover:underline">ico.org.uk</a>).
                        </p>
                    </section>

                    <!-- Section 9 -->
                    <section class="space-y-3 border-t border-tg-border pt-6">
                        <h2
                            class="text-xl font-normal uppercase tracking-wide text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            9. Contact Information
                        </h2>
                        <p class="text-tg-text">
                            For any privacy inquiries, data subject requests, or welfare questions, contact us:
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
                <Link href="/terms" class="tg-link inline-flex items-center gap-1.5 text-xs">
                    Terms &amp; Conditions →
                </Link>
            </div>
        </div>
    </div>
</template>
