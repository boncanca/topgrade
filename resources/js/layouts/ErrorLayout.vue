<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-tg-bg text-tg-text relative flex flex-col justify-between overflow-hidden select-none">
        <!-- Subtle Chalk Pitch Atmosphere Lines (Restrained lines, no gradients, no glowing blobs) -->
        <svg
            class="absolute inset-0 w-full h-full pointer-events-none stroke-white/10"
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
        <header class="relative z-20 px-6 sm:px-12 py-8 flex items-center justify-between border-b border-tg-border">
            <Link href="/" class="flex items-center gap-3.5 group">
                <img
                    src="/logo.png"
                    alt="TopGrade London FC Logo"
                    class="h-9 w-auto brightness-100 transition-opacity group-hover:opacity-85"
                />
                <div class="flex flex-col">
                    <span
                        class="text-base sm:text-lg tracking-wider text-tg-text-strong uppercase leading-none"
                        style="font-family: var(--tg-display);"
                    >
                        TopGrade London FC
                    </span>
                    <span class="text-[10px] tracking-widest text-tg-text-muted uppercase mt-0.5">
                        Play Stopped · Matchday Notice
                    </span>
                </div>
            </Link>

            <Link
                href="/"
                class="text-xs font-semibold uppercase tracking-widest text-tg-text-muted hover:text-tg-text-strong transition-colors"
            >
                ← Club Home
            </Link>
        </header>

        <!-- Standalone Error Page Content Slot -->
        <div class="relative z-10 flex-1 flex flex-col justify-center">
            <slot />
        </div>

        <!-- Bottom Matchday Metadata Bar -->
        <footer class="relative z-20 px-6 sm:px-12 py-5 border-t border-tg-border flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-tg-text-muted">
            <div>
                <span>Official Matchday Control · TOPGRADE LONDON FC CIC</span>
            </div>
            <div class="flex items-center gap-6">
                <Link href="/privacy-policy" class="hover:text-tg-text-strong transition-colors">Privacy Policy</Link>
                <Link href="/terms-and-conditions" class="hover:text-tg-text-strong transition-colors">Terms &amp; Conditions</Link>
                <Link href="/contact" class="hover:text-tg-text-strong transition-colors">Contact Club</Link>
            </div>
        </footer>
    </div>
</template>
