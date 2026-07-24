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
    { label: 'Training', href: '/training' },
    { label: 'About', href: '/about' },
    { label: 'Contact', href: '/contact' },
];

const headerNav = computed(() => {
    const menu = page.props.headerMenu as any;
    if (menu?.items && menu.items.length > 0) {
        return menu.items.map((item: any) => ({
            label: item.label,
            href: item.url || '/',
        }));
    }
    return defaultNav;
});

const footerNav = computed(() => {
    const menu = page.props.footerMenu as any;
    if (menu?.items && menu.items.length > 0) {
        return menu.items.map((item: any) => ({
            label: item.label,
            href: item.url || '/',
        }));
    }
    return defaultNav;
});

// Close mobile menu when page changes
watch(() => page.url, () => {
    mobileOpen.value = false;
});
</script>

<template>
    <div :class="[isDark ? 'dark bg-slate-950 text-white' : 'bg-slate-50 text-slate-900', 'min-h-screen transition-colors duration-200']">
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 z-50 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm border-b border-slate-200 dark:border-slate-800 shadow-sm transition-colors">
            <div class="mx-auto max-w-7xl flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <!-- Logo -->
                <Link href="/" class="flex items-center gap-3">
                    <div class="bg-white rounded-full p-1.5 shadow-sm">
                        <img src="/logo.png" alt="TopGrade FC" class="h-10 w-auto" />
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

                    <Link href="/bookings" class="ml-4 px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition-colors shadow-sm">
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
                        class="block w-full px-4 py-2 bg-purple-600 text-white text-sm font-semibold text-center rounded-lg hover:bg-purple-700"
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
        <footer class="border-t border-slate-200 dark:border-slate-800 bg-slate-900 text-white px-4 py-12 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-6xl">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                    <div>
                        <h3 class="font-bold text-lg mb-4">TOPGRADE LONDON FC</h3>
                        <p class="text-sm text-slate-400">Professional football training academy for youth ages 4-18.</p>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2 text-sm">
                            <li v-for="item in footerNav" :key="item.href">
                                <Link :href="item.href" class="text-slate-400 hover:text-white">{{ item.label }}</Link>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold mb-4">Contact</h4>
                        <p class="text-sm text-slate-400">Email: info@topgradefc.com</p>
                        <p class="text-sm text-slate-400 mt-2">Phone: +44 (0) 20 1234 5678</p>
                    </div>
                </div>
                <div class="border-t border-slate-800 pt-8 text-center text-sm text-slate-400">
                    <p>© 2026 TopGrade London FC. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>
