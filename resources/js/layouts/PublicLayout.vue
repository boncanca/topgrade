<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, watch, onMounted } from 'vue';
import { Menu, X, Sun, Moon } from '@lucide/vue';

const mobileOpen = ref(false);
const page = usePage();

const isDark = ref(true);

onMounted(() => {
    const savedTheme = localStorage.getItem('tglfc_theme');
    if (savedTheme) {
        isDark.value = savedTheme === 'dark';
    } else {
        isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches;
    }
    applyTheme();
});

function toggleTheme() {
    isDark.value = !isDark.value;
    localStorage.setItem('tglfc_theme', isDark.value ? 'dark' : 'light');
    applyTheme();
}

function applyTheme() {
    if (isDark.value) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
}

const defaultNav = [
    { label: 'Home', href: '/' },
    { label: 'About', href: '/about' },
    { label: 'Activities', href: '/bookings' },
    { label: 'Articles', href: '/articles' },
    { label: 'Contact', href: '/contact' },
];

const headerNav = defaultNav;

const footerNav = [
    { label: 'Home', href: '/' },
    { label: 'About', href: '/about' },
    { label: 'Activities', href: '/bookings' },
    { label: 'Articles', href: '/articles' },
    { label: 'Contact', href: '/contact' },
    { label: 'Privacy Policy', href: '/privacy' },
    { label: 'Terms & Conditions', href: '/terms' },
];

// Close mobile menu when page changes
watch(() => page.url, () => {
    mobileOpen.value = false;
});
</script>

<template>
    <div
        :class="[isDark ? 'dark bg-slate-950 text-white' : 'bg-slate-50 text-slate-900', 'min-h-screen transition-colors duration-200']"
        style="--brand-primary: #6B21A8; --brand-secondary: #C026D3; --brand-accent: #F59E0B;"
    >
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 z-50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm border-b border-slate-200 dark:border-slate-800 shadow-sm transition-colors">
            <div class="mx-auto max-w-7xl flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-3">
                    <div class="bg-white rounded-full p-1.5 shadow-sm">
                        <img src="/logo.png" alt="TopGrade London FC" class="h-10 w-auto" />
                    </div>
                    <span class="text-lg font-bold text-slate-900 dark:text-white tracking-wider hidden sm:block">
                        TOPGRADE LONDON FC
                    </span>
                </Link>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-1">
                    <Link
                        v-for="item in headerNav"
                        :key="item.href"
                        :href="item.href"
                        class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition-colors rounded-md"
                    >
                        {{ item.label }}
                    </Link>

                    <!-- Light / Dark Mode Toggle Button -->
                    <button
                        @click="toggleTheme"
                        type="button"
                        class="p-2 ml-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                        :aria-label="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                    >
                        <Sun v-if="isDark" class="w-5 h-5 text-amber-400" />
                        <Moon v-else class="w-5 h-5 text-slate-700" />
                    </button>

                    <Link href="/bookings" class="ml-4 px-4 py-2 bg-[var(--brand-primary)] text-white text-sm font-semibold rounded-lg hover:opacity-90 transition-opacity shadow-sm">
                        Book a Trial
                    </Link>
                </nav>

                <!-- Mobile Toggle -->
                <div class="flex items-center gap-2 md:hidden">
                    <button
                        @click="toggleTheme"
                        type="button"
                        class="p-2 rounded-lg text-slate-600 dark:text-slate-300"
                        :aria-label="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                    >
                        <Sun v-if="isDark" class="w-5 h-5 text-amber-400" />
                        <Moon v-else class="w-5 h-5 text-slate-700" />
                    </button>

                    <button
                        @click="mobileOpen = !mobileOpen"
                        class="text-slate-900 dark:text-white p-2"
                        :aria-label="mobileOpen ? 'Close menu' : 'Open menu'"
                    >
                        <Menu v-if="!mobileOpen" class="w-6 h-6" />
                        <X v-else class="w-6 h-6" />
                    </button>
                </div>
            </div>

            <!-- Mobile Nav -->
            <div v-if="mobileOpen" class="md:hidden bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 pb-4">
                <Link
                    v-for="item in headerNav"
                    :key="item.href"
                    :href="item.href"
                    @click="mobileOpen = false"
                    class="block px-6 py-3 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white"
                >
                    {{ item.label }}
                </Link>
                <div class="px-6 pt-2">
                    <Link
                        href="/bookings"
                        @click="mobileOpen = false"
                        class="block w-full px-4 py-2 bg-[var(--brand-primary)] text-white text-sm font-semibold text-center rounded-lg hover:opacity-90"
                    >
                        Book a Trial
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content (with padding for fixed header) -->
        <main class="pt-16">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-800 bg-slate-950 text-white px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                    <div class="md:col-span-1 space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-white rounded-full p-1.5 shadow-sm">
                                <img src="/logo.png" alt="TopGrade London FC" class="h-9 w-auto" />
                            </div>
                            <span class="text-base font-bold tracking-wider text-white">
                                TOPGRADE LONDON FC
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            A London youth football club and non-profit Community Interest Company (CIC) dedicated to technical player development, teamwork, and match play.
                        </p>
                        <div class="pt-1">
                            <span class="inline-block px-2.5 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-purple-900/40 text-purple-300 border border-purple-500/30">
                                London FA Sanctioned
                            </span>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-sm uppercase tracking-wider text-purple-300 mb-4">Navigation</h4>
                        <ul class="space-y-2.5 text-xs">
                            <li><Link href="/" class="text-slate-400 hover:text-white transition-colors">Home</Link></li>
                            <li><Link href="/about" class="text-slate-400 hover:text-white transition-colors">About the Club</Link></li>
                            <li><Link href="/bookings" class="text-slate-400 hover:text-white transition-colors">Squads & Training</Link></li>
                            <li><Link href="/articles" class="text-slate-400 hover:text-white transition-colors">Club News</Link></li>
                            <li><Link href="/contact" class="text-slate-400 hover:text-white transition-colors">Contact Us</Link></li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold text-sm uppercase tracking-wider text-purple-300 mb-4">Training Grounds</h4>
                        <ul class="space-y-3 text-xs text-slate-400">
                            <li>
                                <span class="font-semibold text-white block">Tottenham Training Ground:</span>
                                <span>Fredrick Knight Sports Ground / Willoughby Lane, N17 (3G Pitch)</span>
                            </li>
                            <li>
                                <span class="font-semibold text-white block">Hackney Marshes Match Grounds:</span>
                                <span>Homerton Road, E9 (Saturday League Grass Pitches)</span>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-bold text-sm uppercase tracking-wider text-purple-300 mb-4">Club Enquiries</h4>
                        <ul class="space-y-2.5 text-xs text-slate-400">
                            <li>
                                <span class="text-slate-300 block">General & Trial Enquiries:</span>
                                <a href="mailto:topgradelondonfc@hotmail.com" class="text-purple-400 hover:underline">topgradelondonfc@hotmail.com</a>
                            </li>
                            <li class="pt-1">
                                <Link
                                    href="/bookings"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[var(--brand-primary)] hover:bg-purple-700 text-white font-semibold transition-colors text-xs"
                                >
                                    <span>Book a Trial Session</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-slate-900 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                    <p>© 2026 TopGrade London FC (CIC). All rights reserved.</p>
                    <div class="flex items-center gap-6">
                        <Link href="/privacy" class="hover:text-slate-300 transition-colors">Privacy Policy</Link>
                        <Link href="/terms" class="hover:text-slate-300 transition-colors">Terms & Conditions</Link>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
