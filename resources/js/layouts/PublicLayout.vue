<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { Menu, X, ArrowRight } from '@lucide/vue';

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

const footerNav = [
    { label: 'About Club', href: '/#develop' },
    { label: 'Weekly Training', href: '/#train' },
    { label: 'Our Teams', href: '/#teams' },
    { label: 'Moments Gallery', href: '/#gallery' },
    { label: 'Matchday Venues', href: '/#matchday' },
    { label: 'Bookings & Trials', href: '/bookings' },
    { label: 'Contact Us', href: '/contact' },
];

// Close mobile menu when page changes
watch(() => page.url, () => {
    mobileOpen.value = false;
});
</script>

<template>
    <div class="min-h-screen bg-tg-bg text-tg-text">
        <!-- Pitch Grid Background -->
        <div class="tg-pitchgrid" aria-hidden="true">
            <svg viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice">
                <!-- Outer boundary -->
                <rect x="60" y="50" width="1320" height="800" stroke="currentColor" />
                <!-- Halfway line -->
                <line x1="720" y1="50" x2="720" y2="850" stroke="currentColor" />
                <!-- Centre circle -->
                <circle cx="720" cy="450" r="91.5" stroke="currentColor" />
                <!-- Centre spot -->
                <circle cx="720" cy="450" r="3" fill="currentColor" />
                <!-- Left penalty area -->
                <rect x="60" y="238" width="165" height="424" stroke="currentColor" />
                <!-- Left goal area -->
                <rect x="60" y="338" width="55" height="224" stroke="currentColor" />
                <!-- Left penalty arc -->
                <path d="M225 356 A91.5 91.5 0 0 1 225 544" stroke="currentColor" fill="none" />
                <!-- Right penalty area -->
                <rect x="1215" y="238" width="165" height="424" stroke="currentColor" />
                <!-- Right goal area -->
                <rect x="1325" y="338" width="55" height="224" stroke="currentColor" />
                <!-- Right penalty arc -->
                <path d="M1215 356 A91.5 91.5 0 0 0 1215 544" stroke="currentColor" fill="none" />
            </svg>
        </div>

        <!-- Header: Solid, Non-frosted header per guidelines -->
        <header
            class="fixed top-0 left-0 right-0 z-50 bg-tg-bg border-b border-tg-border"
        >
            <div class="mx-auto max-w-7xl flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-3 tg-focus">
                    <div class="bg-white rounded p-1 shadow-xs">
                        <img src="/logo.png" alt="TopGrade London FC" class="h-9 w-auto" />
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

        <!-- Footer -->
        <footer
            class="relative z-10 px-4 py-16 sm:px-6 lg:px-8 bg-tg-bg-deep border-t border-tg-border"
        >
            <div class="mx-auto max-w-7xl">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                    <!-- Club Identity -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-white rounded-full p-1.5 shadow-xs">
                                <img src="/logo.png" alt="TopGrade London FC" class="h-12 w-auto" />
                            </div>
                        </div>
                        <div>
                            <span
                                style="
                                    font-family: var(--tg-display);
                                    font-size: 1.2rem;
                                    line-height: 1;
                                    letter-spacing: 0.03em;
                                    color: var(--tg-text);
                                    display: block;
                                "
                            >TOPGRADE<br />LONDON FC</span>
                        </div>
                        <p
                            style="
                                font-family: var(--tg-body);
                                font-size: 0.82rem;
                                line-height: 1.6;
                                color: var(--tg-text-muted);
                                max-width: 20rem;
                            "
                        >
                            Youth football club.<br />
                            Training in Tottenham.
                        </p>
                    </div>

                    <!-- Navigation -->
                    <div>
                        <nav class="flex flex-col gap-2.5">
                            <a
                                v-for="item in footerNav"
                                :key="item.href + item.label"
                                :href="item.href"
                                class="tg-link tg-focus"
                            >
                                {{ item.label }}
                            </a>
                        </nav>
                    </div>

                    <!-- CTA + Info -->
                    <div class="space-y-6">
                        <Link
                            href="/bookings/free-trial-session"
                            class="tg-btn tg-focus"
                        >
                            <span>Book a Trial</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>

                        <p
                            style="
                                font-family: var(--tg-body);
                                font-size: 0.78rem;
                                line-height: 1.5;
                                color: var(--tg-text-muted);
                            "
                        >
                            North &amp; East London<br />
                            Youth Football Club.
                        </p>

                        <div
                            style="
                                font-family: var(--tg-body);
                                font-size: 0.76rem;
                                color: var(--tg-text-muted);
                            "
                        >
                            <a
                                href="mailto:topgradelondonfc@hotmail.com"
                                class="tg-focus"
                                style="color: var(--tg-magenta); text-decoration: none;"
                            >
                                topgradelondonfc@hotmail.com
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div
                    class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-8"
                    style="
                        border-top: 1px solid var(--tg-border);
                        font-family: var(--tg-body);
                        font-size: 0.74rem;
                        color: var(--tg-text-muted);
                    "
                >
                    <p>© 2026 TopGrade London FC (CIC). All rights reserved.</p>
                    <div class="flex items-center gap-6">
                        <Link href="/privacy" class="tg-link tg-focus" style="font-size: 0.74rem;">Privacy</Link>
                        <Link href="/terms" class="tg-link tg-focus" style="font-size: 0.74rem;">Terms</Link>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
