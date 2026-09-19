<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import { ShieldCheck, Info, CheckCircle2 } from '@lucide/vue';

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
        title="Cookies Policy | TopGrade London FC"
        description="TopGrade London FC cookies policy explaining our use of strictly necessary session and security cookies with zero third-party tracking."
        path="/cookies"
    />

    <div class="min-h-screen bg-tg-bg text-tg-text">
        <!-- Hero Section -->
        <section class="relative min-h-[32vh] sm:min-h-[38vh] flex items-end overflow-hidden pb-12 pt-24 sm:pt-28 border-b border-tg-border">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-25"
                style="background-image: url('/images/club/training_pitch_evening.jpg')"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-tg-bg via-tg-bg/85 to-transparent" />

            <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-3">
                <span class="inline-block px-3 py-1 rounded-xs bg-tg-bg-deep border border-tg-border text-tg-accent text-xs font-bold tracking-widest uppercase">
                    Data &amp; Privacy
                </span>

                <h1
                    class="text-3xl sm:text-5xl font-normal uppercase tracking-tight text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    Cookies Policy
                </h1>
                <p class="text-tg-text text-sm sm:text-base max-w-2xl leading-relaxed">
                    How TopGrade London FC uses essential cookies to ensure secure operation and booking functionality on our website.
                </p>
            </div>
        </section>

        <!-- Main Content Area -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-12">
            <!-- Core Statement -->
            <div class="p-6 sm:p-8 rounded-sm bg-tg-bg-deep/80 border border-tg-border space-y-4">
                <div class="flex items-center gap-3">
                    <ShieldCheck class="w-6 h-6 text-tg-accent" />
                    <h2
                        class="text-xl sm:text-2xl font-normal uppercase text-tg-text-strong"
                        style="font-family: var(--tg-display);"
                    >
                        Strictly Necessary Cookies Only
                    </h2>
                </div>
                <p class="text-tg-text text-sm leading-relaxed">
                    TopGrade London FC uses only strictly necessary first-party cookies that are essential for the security, stability, and proper technical function of this website.
                </p>
                <p class="text-tg-text text-sm leading-relaxed">
                    <strong class="text-tg-text-strong">We do not use marketing, behavioral advertising, or third-party tracking cookies.</strong> We do not track your activity across other websites or sell your personal data.
                </p>
            </div>

            <!-- Cookie Table -->
            <div class="p-6 sm:p-8 rounded-sm bg-tg-bg-deep/80 border border-tg-border space-y-6">
                <div class="flex items-center gap-2.5">
                    <Info class="w-5 h-5 text-tg-accent" />
                    <h3
                        class="text-lg font-normal uppercase text-tg-text-strong"
                        style="font-family: var(--tg-display);"
                    >
                        Cookies Used on This Website
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border border-tg-border">
                        <thead>
                            <tr class="bg-tg-bg border-b border-tg-border text-tg-text-strong font-semibold uppercase tracking-wider">
                                <th class="p-3.5">Cookie Name</th>
                                <th class="p-3.5">Type</th>
                                <th class="p-3.5">Duration</th>
                                <th class="p-3.5">Purpose</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-tg-border text-tg-text-muted">
                            <tr>
                                <td class="p-3.5 font-mono text-tg-accent font-bold">laravel_session</td>
                                <td class="p-3.5">First-Party / Essential</td>
                                <td class="p-3.5">2 Hours</td>
                                <td class="p-3.5">Maintains your active session state during booking and navigation.</td>
                            </tr>
                            <tr>
                                <td class="p-3.5 font-mono text-tg-accent font-bold">XSRF-TOKEN</td>
                                <td class="p-3.5">First-Party / Security</td>
                                <td class="p-3.5">Session</td>
                                <td class="p-3.5">Protects forms against cross-site request forgery (CSRF) attacks.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Managing Cookies in Browser -->
            <div class="p-6 sm:p-8 rounded-sm bg-tg-bg-deep/80 border border-tg-border space-y-4">
                <h3
                    class="text-lg font-normal uppercase text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    Managing Cookies in Your Browser
                </h3>
                <p class="text-xs text-tg-text leading-relaxed">
                    You can restrict or block cookies through your web browser settings. Please note that if you disable strictly necessary cookies, certain core functions such as submitting booking forms or contact requests may not operate as intended.
                </p>
                <div class="pt-2">
                    <Link href="/privacy" class="tg-link text-xs">
                        View our full Privacy Policy →
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
