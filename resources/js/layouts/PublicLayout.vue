<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Menu, X, ArrowRight } from '@lucide/vue';
import PitchGrid from '@/components/Public/PitchGrid.vue';

const mobileOpen = ref(false);
const page = usePage();

const headerNav = [
    { label: 'About', href: '/#develop' },
    { label: 'Training', href: '/#train' },
    { label: 'Teams', href: '/#teams' },
    { label: 'Moments', href: '/#gallery' },
    { label: 'Matchday', href: '/#matchday' },
    { label: 'Bookings', href: '/bookings' },
];

const clubLinks = [
    { label: 'About the Club', href: '/about' },
    { label: 'Weekly Training', href: '/training' },
    { label: 'Our Teams', href: '/#teams' },
    { label: 'Matchday Grounds', href: '/#matchday' },
];

const infoLinks = [
    { label: 'Bookings & Trials', href: '/bookings' },
    { label: 'News & Articles', href: '/articles' },
    { label: 'Contact Us', href: '/contact' },
];

const supportLinks = [
    { label: 'Privacy Policy', href: '/privacy' },
    { label: 'Terms & Conditions', href: '/terms' },
    { label: 'Contact Club', href: '/contact' },
];

// Close mobile menu when page changes
watch(() => page.url, () => {
    mobileOpen.value = false;
});
</script>

<template>
    <div class="min-h-screen bg-tg-bg text-tg-text">
        <!-- Persistent Global Pitch Grid (z-index: 2, below ball and translucent surfaces) -->
        <PitchGrid />

        <!-- Header: Solid, Non-frosted header per guidelines (z-index: 30) -->
        <header
            class="fixed top-0 left-0 right-0 z-30 bg-tg-bg border-b border-tg-border"
        >
            <div class="mx-auto max-w-7xl flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-3 tg-focus">
                    <div class="bg-white rounded p-1 shadow-xs">
                        <img src="/logo.png" alt="TopGrade London FC Crest" class="h-9 w-auto" />
                    </div>
                    <div class="hidden sm:flex flex-col leading-none">
                        <span
                            style="
                                font-family: var(--tg-display);
                                font-size: 1.05rem;
                                line-height: 1;
                                letter-spacing: 0.04em;
                                color: var(--tg-text-strong);
                            "
                        >TOPGRADE</span>
                        <span
                            style="
                                font-family: var(--tg-body);
                                font-size: 0.6rem;
                                letter-spacing: 0.16em;
                                color: var(--tg-text-muted);
                                margin-top: 1px;
                            "
                        >LONDON FC</span>
                    </div>
                </Link>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-6">
                    <a
                        v-for="item in headerNav"
                        :key="item.href + item.label"
                        :href="item.href"
                        class="tg-nav-link tg-focus"
                    >
                        {{ item.label }}
                    </a>

                    <Link
                        href="/bookings/free-trial-session"
                        class="tg-btn tg-focus ml-2"
                    >
                        <span>Book a Trial</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </nav>

                <!-- Mobile Toggle -->
                <div class="flex items-center md:hidden">
                    <button
                        @click="mobileOpen = !mobileOpen"
                        class="p-2 tg-focus"
                        style="color: var(--tg-text);"
                        :aria-label="mobileOpen ? 'Close menu' : 'Open menu'"
                    >
                        <Menu v-if="!mobileOpen" class="w-6 h-6" />
                        <X v-else class="w-6 h-6" />
                    </button>
                </div>
            </div>

            <!-- Mobile Nav -->
            <div
                v-if="mobileOpen"
                class="md:hidden pb-6 bg-tg-bg border-t border-tg-border"
            >
                <a
                    v-for="item in headerNav"
                    :key="item.href + item.label"
                    :href="item.href"
                    @click="mobileOpen = false"
                    class="block px-6 py-3 tg-focus"
                    style="
                        font-family: var(--tg-body);
                        font-size: 0.95rem;
                        font-weight: 500;
                        color: var(--tg-text);
                        opacity: 0.88;
                    "
                >
                    {{ item.label }}
                </a>
                <div class="px-6 pt-3">
                    <Link
                        href="/bookings/free-trial-session"
                        @click="mobileOpen = false"
                        class="tg-btn w-full justify-center"
                    >
                        <span>Book a Trial</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content (with padding for fixed header) -->
        <main class="relative z-10 pt-16">
            <slot />
        </main>

        <!-- Structured 4-Column Footer -->
        <footer
            class="relative z-10 px-4 py-16 sm:px-6 lg:px-8 bg-tg-bg-deep border-t border-tg-border"
        >
            <div class="mx-auto max-w-7xl space-y-12">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12">
                    <!-- Column 1: Club -->
                    <div class="space-y-4">
                        <h4
                            class="text-xs font-bold uppercase tracking-widest text-tg-accent"
                        >
                            Club
                        </h4>
                        <ul class="space-y-2.5 text-sm">
                            <li v-for="item in clubLinks" :key="item.label">
                                <a :href="item.href" class="tg-link text-tg-text-muted hover:text-tg-text-strong">
                                    {{ item.label }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Column 2: Information -->
                    <div class="space-y-4">
                        <h4
                            class="text-xs font-bold uppercase tracking-widest text-tg-accent"
                        >
                            Information
                        </h4>
                        <ul class="space-y-2.5 text-sm">
                            <li v-for="item in infoLinks" :key="item.label">
                                <a :href="item.href" class="tg-link text-tg-text-muted hover:text-tg-text-strong">
                                    {{ item.label }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Column 3: Support & Governance -->
                    <div class="space-y-4">
                        <h4
                            class="text-xs font-bold uppercase tracking-widest text-tg-accent"
                        >
                            Support &amp; Governance
                        </h4>
                        <ul class="space-y-2.5 text-sm">
                            <li v-for="item in supportLinks" :key="item.label">
                                <Link :href="item.href" class="tg-link text-tg-text-muted hover:text-tg-text-strong">
                                    {{ item.label }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Column 4: Verified UK Legal Identity -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-white rounded-full p-1 shadow-xs">
                                <img src="/logo.png" alt="TopGrade London FC" class="h-8 w-auto" />
                            </div>
                            <span
                                style="font-family: var(--tg-display);"
                                class="text-sm uppercase tracking-wider text-tg-text-strong"
                            >
                                TopGrade London FC
                            </span>
                        </div>

                        <div class="text-xs text-tg-text-muted space-y-1.5 leading-relaxed">
                            <p class="font-bold text-tg-text-strong">TOPGRADE LONDON FC CIC</p>
                            <p>Registered in England &amp; Wales</p>
                            <p>Company No. <span class="text-tg-text-strong font-mono">14087076</span></p>
                            <p>Community Interest Company (CIC)</p>
                            <p class="pt-1">
                                <span class="text-tg-text-strong">Registered Office:</span><br />
                                30 Broadwater Road, London, England, N17 6ES
                            </p>
                            <p class="pt-1">
                                <a
                                     href="mailto:info@topgradelondonfc.co.uk"
                                     class="text-tg-accent hover:underline"
                                 >
                                     info@topgradelondonfc.co.uk
                                 </a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div
                    class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-8 border-t border-tg-border text-xs text-tg-text-muted"
                >
                    <p>© 2026 TopGrade London FC CIC. All rights reserved.</p>
                    <div class="flex flex-wrap items-center gap-6">
                        <Link href="/privacy" class="tg-link text-xs">Privacy Policy</Link>
                        <Link href="/terms" class="tg-link text-xs">Terms &amp; Conditions</Link>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
