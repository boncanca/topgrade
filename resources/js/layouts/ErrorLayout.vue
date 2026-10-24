<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="bg-tg-bg text-tg-text relative flex min-h-screen select-none flex-col justify-between overflow-hidden"
    >
        <!-- Subtle Chalk Pitch Atmosphere Lines (Restrained lines, no gradients, no glowing blobs) -->
        <svg
            class="pointer-events-none absolute inset-0 h-full w-full stroke-white/10"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <!-- Corner Quadrant Arc -->
            <path
                d="M 0 0 A 240 240 0 0 1 240 240"
                fill="none"
                stroke-width="1.5"
                stroke-dasharray="6 6"
            />
            <!-- Midfield Centre Circle Arc (Pitch right flank) -->
            <path
                d="M 1200 500 A 300 300 0 0 1 1200 1100"
                fill="none"
                stroke-width="1.5"
                class="hidden md:block"
            />
        </svg>

        <!-- Top Header Bar (Zero Admin / Volara Shell) -->
        <header
            class="border-tg-border relative z-20 flex items-center justify-between border-b px-6 py-8 sm:px-12"
        >
            <Link href="/" class="group flex items-center gap-3.5">
                <img
                    src="/logo.png"
                    alt="TopGrade London FC Logo"
                    class="h-9 w-auto brightness-100 transition-opacity group-hover:opacity-85"
                />
                <div class="flex flex-col">
                    <span
                        class="text-tg-text-strong text-base uppercase leading-none tracking-wider sm:text-lg"
                        style="font-family: var(--tg-display)"
                    >
                        TopGrade London FC
                    </span>
                    <span
                        class="text-tg-text-muted mt-0.5 text-[10px] uppercase tracking-widest"
                    >
                        Play Stopped · Matchday Notice
                    </span>
                </div>
            </Link>

            <Link
                href="/"
                class="text-tg-text-muted hover:text-tg-text-strong text-xs font-semibold uppercase tracking-widest transition-colors"
            >
                ← Club Home
            </Link>
        </header>

        <!-- Standalone Error Page Content Slot -->
        <div class="relative z-10 flex flex-1 flex-col justify-center">
            <slot />
        </div>

        <!-- Bottom Matchday Metadata Bar -->
        <footer
            class="border-tg-border text-tg-text-muted relative z-20 flex flex-col items-center justify-between gap-3 border-t px-6 py-5 text-xs sm:flex-row sm:px-12"
        >
            <div>
                <span>Official Matchday Control · TOPGRADE LONDON FC CIC</span>
            </div>
            <div class="flex items-center gap-6">
                <Link
                    href="/privacy-policy"
                    class="hover:text-tg-text-strong transition-colors"
                    >Privacy Policy</Link
                >
                <Link
                    href="/terms-and-conditions"
                    class="hover:text-tg-text-strong transition-colors"
                    >Terms &amp; Conditions</Link
                >
                <Link
                    href="/contact"
                    class="hover:text-tg-text-strong transition-colors"
                    >Contact Club</Link
                >
            </div>
        </footer>
    </div>
</template>
