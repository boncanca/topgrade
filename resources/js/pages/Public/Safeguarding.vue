<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import { Shield, AlertTriangle, Phone, Mail, FileText, CheckCircle2 } from '@lucide/vue';

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
        title="Safeguarding | TopGrade London FC"
        description="TopGrade London FC safeguarding policy, child welfare guidance, reporting procedures, and player safety commitments."
        path="/safeguarding"
    />

    <div class="min-h-screen bg-tg-bg text-tg-text">
        <!-- Hero Section -->
        <section class="relative min-h-[32vh] sm:min-h-[38vh] flex items-end overflow-hidden pb-12 pt-24 sm:pt-28 border-b border-tg-border">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-25"
                style="background-image: url('/images/club/club-training-london.jpg')"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-tg-bg via-tg-bg/85 to-transparent" />

            <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-3">
                <span class="inline-block px-3 py-1 rounded-xs bg-tg-bg-deep border border-tg-border text-tg-accent text-xs font-bold tracking-widest uppercase">
                    Child Welfare &amp; Safety
                </span>

                <h1
                    class="text-3xl sm:text-5xl font-normal uppercase tracking-tight text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    Safeguarding &amp; Player Welfare
                </h1>
                <p class="text-tg-text text-sm sm:text-base max-w-2xl leading-relaxed">
                    TopGrade London FC is committed to providing a safe, positive, and supportive youth football environment where every child and young player can participate free from harm.
                </p>
            </div>
        </section>

        <!-- Main Content Area -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-12">
            <!-- Statement of Commitment -->
            <div class="p-6 sm:p-8 rounded-sm bg-tg-bg-deep/80 border border-tg-border space-y-4">
                <div class="flex items-center gap-3">
                    <Shield class="w-6 h-6 text-tg-accent" />
                    <h2
                        class="text-xl sm:text-2xl font-normal uppercase text-tg-text-strong"
                        style="font-family: var(--tg-display);"
                    >
                        Our Commitment to Youth Welfare
                    </h2>
                </div>
                <p class="text-tg-text text-sm leading-relaxed">
                    The welfare of children and young people is paramount. All children, regardless of age, gender, race, religion, background, or ability, have the right to protection from all forms of neglect, abuse, and discrimination.
                </p>
                <p class="text-tg-text text-sm leading-relaxed">
                    TopGrade London FC works to ensure that all training sessions, matchday fixtures, and club gatherings prioritize the emotional, physical, and psychological well-being of every participant.
                </p>
            </div>

            <!-- Club Welfare Contact & Reporting -->
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Club Reporting -->
                <div class="p-6 sm:p-8 rounded-sm bg-tg-bg-deep/80 border border-tg-border space-y-5">
                    <div class="flex items-center gap-2 text-tg-accent">
                        <Mail class="w-5 h-5" />
                        <h3
                            class="text-lg font-normal uppercase text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            Reporting a Concern to the Club
                        </h3>
                    </div>
                    <p class="text-xs text-tg-text-muted leading-relaxed">
                        If you have a concern about a child's welfare or poor practice during any TopGrade training session or match, please contact the club immediately.
                    </p>

                    <div class="p-4 rounded-xs bg-tg-bg border border-tg-border space-y-2 text-xs">
                        <div class="font-bold text-tg-text-strong">Club Safeguarding Lead / Welfare Route:</div>
                        <div class="text-tg-text">
                            Email: <a href="mailto:topgradelondonfc@hotmail.com" class="text-tg-accent hover:underline font-medium">topgradelondonfc@hotmail.com</a>
                        </div>
                        <div class="text-[11px] text-tg-text-muted pt-1">
                            <span class="font-semibold text-tg-text">Notice:</span> Dedicated club welfare officer contact details and official club documentation are updated in liaison with club management.
                        </div>
                    </div>
                </div>

                <!-- External Support & Statutory Services -->
                <div class="p-6 sm:p-8 rounded-sm bg-tg-bg-deep/80 border border-tg-border space-y-5">
                    <div class="flex items-center gap-2 text-tg-accent">
                        <Phone class="w-5 h-5" />
                        <h3
                            class="text-lg font-normal uppercase text-tg-text-strong"
                            style="font-family: var(--tg-display);"
                        >
                            External &amp; Urgent Support
                        </h3>
                    </div>
                    <p class="text-xs text-tg-text-muted leading-relaxed">
                        If a child is in immediate danger or requires urgent medical attention, always call the emergency services first.
                    </p>

                    <div class="space-y-3 text-xs">
                        <div class="p-3 rounded-xs bg-tg-bg border border-tg-border space-y-1">
                            <div class="font-bold text-tg-text-strong">Emergency Services</div>
                            <div class="text-tg-accent font-mono font-bold text-sm">Call 999 immediately</div>
                        </div>

                        <div class="p-3 rounded-xs bg-tg-bg border border-tg-border space-y-1">
                            <div class="font-bold text-tg-text-strong">NSPCC 24/7 Helpline</div>
                            <div class="text-tg-text-muted">For confidential advice on child safety:</div>
                            <div class="text-tg-accent font-mono font-semibold">0808 800 5000 · help@nspcc.org.uk</div>
                        </div>

                        <div class="p-3 rounded-xs bg-tg-bg border border-tg-border space-y-1">
                            <div class="font-bold text-tg-text-strong">Childline (For Children &amp; Young People)</div>
                            <div class="text-tg-accent font-mono font-semibold">0800 1111 (Free 24/7)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Photography & Media Guidance -->
            <div class="p-6 sm:p-8 rounded-sm bg-tg-bg-deep/80 border border-tg-border space-y-4">
                <div class="flex items-center gap-2.5">
                    <FileText class="w-5 h-5 text-tg-accent" />
                    <h3
                        class="text-lg font-normal uppercase text-tg-text-strong"
                        style="font-family: var(--tg-display);"
                    >
                        Photography &amp; Media Guidelines
                    </h3>
                </div>
                <div class="grid sm:grid-cols-2 gap-4 text-xs text-tg-text-muted leading-relaxed pt-2">
                    <div class="flex items-start gap-2.5">
                        <CheckCircle2 class="w-4 h-4 text-tg-accent shrink-0 mt-0.5" />
                        <span>Photographs are captured solely to celebrate player development, teamwork, and matchday achievements.</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <CheckCircle2 class="w-4 h-4 text-tg-accent shrink-0 mt-0.5" />
                        <span>Parents and guardians indicate consent preferences at initial registration and booking.</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <CheckCircle2 class="w-4 h-4 text-tg-accent shrink-0 mt-0.5" />
                        <span>Full names of minors are not published alongside individual photographs without explicit parental agreement.</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <CheckCircle2 class="w-4 h-4 text-tg-accent shrink-0 mt-0.5" />
                        <span>Consent can be updated or withdrawn at any time by contacting club management.</span>
                    </div>
                </div>
            </div>

            <!-- Codes of Conduct Overview -->
            <div class="p-6 sm:p-8 rounded-sm bg-tg-bg-deep/80 border border-tg-border space-y-4">
                <h3
                    class="text-lg font-normal uppercase text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    Codes of Conduct
                </h3>
                <p class="text-xs text-tg-text leading-relaxed">
                    TopGrade London FC expects positive sportsmanship, respect, and discipline from all coaches, players, parents, and spectators. Aggressive behavior, abusive language, or unsporting conduct on the touchline or pitch will not be tolerated.
                </p>
                <div class="pt-2">
                    <Link href="/contact" class="tg-btn ghost text-xs">
                        Contact Club Management
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
