<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/layouts/PublicLayout.vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import { Eye, CheckCircle2, Sliders, Mail } from '@lucide/vue';

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
        title="Accessibility Statement | TopGrade London FC"
        description="TopGrade London FC website accessibility statement, WCAG compliance standards, keyboard navigation, and contact information."
        path="/accessibility"
    />

    <div class="min-h-screen bg-tg-bg text-tg-text">
        <!-- Hero Section -->
        <section class="relative min-h-[32vh] sm:min-h-[38vh] flex items-end overflow-hidden pb-12 pt-24 sm:pt-28 border-b border-tg-border">
            <div
                class="absolute inset-0 bg-cover bg-center opacity-25"
                style="background-image: url('/images/club/tactical_coaching.jpg')"
            />
            <div class="absolute inset-0 bg-gradient-to-t from-tg-bg via-tg-bg/85 to-transparent" />

            <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 w-full space-y-3">
                <span class="inline-block px-3 py-1 rounded-xs bg-tg-bg-deep border border-tg-border text-tg-accent text-xs font-bold tracking-widest uppercase">
                    Standards &amp; Inclusion
                </span>

                <h1
                    class="text-3xl sm:text-5xl font-normal uppercase tracking-tight text-tg-text-strong"
                    style="font-family: var(--tg-display);"
                >
                    Accessibility Statement
                </h1>
                <p class="text-tg-text text-sm sm:text-base max-w-2xl leading-relaxed">
                    TopGrade London FC is committed to making its digital services accessible to everyone, including parents, players, and supporters with disabilities.
                </p>
            </div>
        </section>

        <!-- Main Content Area -->
        <section class="py-16 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-12">
            <!-- Conformance Status -->
            <div class="p-6 sm:p-8 rounded-sm bg-tg-bg-deep/80 border border-tg-border space-y-4">
                <div class="flex items-center gap-3">
                    <Eye class="w-6 h-6 text-tg-accent" />
                    <h2
                        class="text-xl sm:text-2xl font-normal uppercase text-tg-text-strong"
                        style="font-family: var(--tg-display);"
                    >
                        Compliance Status
                    </h2>
                </div>
                <p class="text-tg-text text-sm leading-relaxed">
                    We aim to adhere to the Web Content Accessibility Guidelines (WCAG) 2.2 at Level AA. These guidelines explain how to make web content more accessible for people with visual, auditory, motor, and cognitive needs.
                </p>
            </div>

            <!-- Accessibility Features Implemented -->
            <div class="p-6 sm:p-8 rounded-sm bg-tg-bg-deep/80 border border-tg-border space-y-6">
                <div class="flex items-center gap-2.5">
                    <Sliders class="w-5 h-5 text-tg-accent" />
                    <h3
                        class="text-lg font-normal uppercase text-tg-text-strong"
                        style="font-family: var(--tg-display);"
                    >
                        Measures Taken to Support Accessibility
                    </h3>
                </div>

                <div class="grid sm:grid-cols-2 gap-5 text-xs text-tg-text-muted leading-relaxed">
                    <div class="p-4 rounded-xs bg-tg-bg border border-tg-border space-y-2">
                        <div class="flex items-center gap-2 font-bold text-tg-text-strong">
                            <CheckCircle2 class="w-4 h-4 text-tg-accent" />
                            <span>Reduced Motion Support</span>
                        </div>
                        <p>
                            All animated elements, including the moving football, background pitch drift, and image hover transforms, strictly respect the <code class="text-tg-accent">prefers-reduced-motion</code> operating system setting and are automatically disabled when active.
                        </p>
                    </div>

                    <div class="p-4 rounded-xs bg-tg-bg border border-tg-border space-y-2">
                        <div class="flex items-center gap-2 font-bold text-tg-text-strong">
                            <CheckCircle2 class="w-4 h-4 text-tg-accent" />
                            <span>Keyboard Navigation &amp; Focus</span>
                        </div>
                        <p>
                            All interactive elements, buttons, form controls, and navigation links include high-visibility focus indicators (<code class="text-tg-accent">outline: 2px solid var(--tg-accent)</code>) to facilitate keyboard-only usage.
                        </p>
                    </div>

                    <div class="p-4 rounded-xs bg-tg-bg border border-tg-border space-y-2">
                        <div class="flex items-center gap-2 font-bold text-tg-text-strong">
                            <CheckCircle2 class="w-4 h-4 text-tg-accent" />
                            <span>High Contrast &amp; Clean Typography</span>
                        </div>
                        <p>
                            The site uses a curated high-contrast palette with light text on deep purple/black surfaces, exceeding WCAG AA minimum contrast ratios for readable typography.
                        </p>
                    </div>

                    <div class="p-4 rounded-xs bg-tg-bg border border-tg-border space-y-2">
                        <div class="flex items-center gap-2 font-bold text-tg-text-strong">
                            <CheckCircle2 class="w-4 h-4 text-tg-accent" />
                            <span>Responsive Scaling</span>
                        </div>
                        <p>
                            Layouts adapt smoothly to zoom levels up to 200% on desktop without loss of information or overlapping elements, and mobile viewports down to 375px are fully supported.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Feedback & Contact -->
            <div class="p-6 sm:p-8 rounded-sm bg-tg-bg-deep/80 border border-tg-border space-y-4">
                <div class="flex items-center gap-2.5">
                    <Mail class="w-5 h-5 text-tg-accent" />
                    <h3
                        class="text-lg font-normal uppercase text-tg-text-strong"
                        style="font-family: var(--tg-display);"
                    >
                        Feedback &amp; Reporting Difficulties
                    </h3>
                </div>
                <p class="text-xs text-tg-text leading-relaxed">
                    We welcome your feedback on the accessibility of the TopGrade London FC website. If you encounter accessibility barriers or need information in an alternative format, please let us know:
                </p>
                <div class="text-xs text-tg-text-muted space-y-1">
                    <p>Email: <a href="mailto:topgradelondonfc@hotmail.com" class="text-tg-accent hover:underline font-medium">topgradelondonfc@hotmail.com</a></p>
                    <p>We try to respond to accessibility feedback within 3 to 5 business days.</p>
                </div>
                <div class="pt-2">
                    <Link href="/contact" class="tg-btn ghost text-xs">
                        Contact Club Management
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>
