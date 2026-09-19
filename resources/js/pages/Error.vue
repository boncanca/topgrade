<script setup lang="ts">
import { computed, ref, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import SeoHead from '@/components/SEO/SeoHead.vue';

interface ErrorStateConfig {
    code: string;
    label: string;
    headline: string;
    description: string;
    action: string;
    actionHref: string;
    secondaryAction: string;
    secondaryHref?: string;
    isReloadSecondary?: boolean;
    ballAction: string;
    situation: 'throw-in' | 'referee' | 'halftime' | 'var' | 'pitch' | 'stoppage';
    showPlayer: boolean;
    playerImage?: string;
    playerAlt?: string;
}

const props = defineProps<{
    status: number;
    message?: string;
}>();

const errorStates: Record<number, ErrorStateConfig> = {
    404: {
        code: '404',
        label: 'OUT OF PLAY',
        headline: 'The ball has gone out.',
        description: "The page you're looking for has gone over the touchline.",
        action: 'Back to the Club',
        actionHref: '/',
        secondaryAction: 'Explore Training',
        secondaryHref: '/training',
        ballAction: 'Throw it back in →',
        situation: 'throw-in',
        showPlayer: true,
        playerImage: '/images/player-throwin.webp',
        playerAlt: 'TopGrade player preparing touchline throw-in',
    },
    403: {
        code: '403',
        label: "REFEREE'S CALL",
        headline: 'Access denied.',
        description: "The referee has stopped play. You don't have permission to enter this area.",
        action: 'Return to the Club',
        actionHref: '/',
        secondaryAction: 'Go to Sign In',
        secondaryHref: '/login',
        ballAction: 'Play it back →',
        situation: 'referee',
        showPlayer: false,
    },
    419: {
        code: '419',
        label: 'HALF-TIME',
        headline: 'Your session has expired.',
        description: 'The whistle has gone. Start again to continue.',
        action: 'Start Again',
        actionHref: '/',
        secondaryAction: 'Back to the Club',
        secondaryHref: '/',
        ballAction: 'Kick Off Again →',
        situation: 'halftime',
        showPlayer: false,
    },
    500: {
        code: '500',
        label: 'VAR REVIEW',
        headline: 'Play has been stopped.',
        description: "We're checking what went wrong. Please try again.",
        action: 'Back to the Club',
        actionHref: '/',
        secondaryAction: 'Try Again',
        isReloadSecondary: true,
        ballAction: 'Restart Play →',
        situation: 'var',
        showPlayer: false,
    },
    503: {
        code: '503',
        label: 'PITCH CLOSED',
        headline: 'The match is temporarily paused.',
        description: 'The pitch is being prepared. Please try again shortly.',
        action: 'Back to the Club',
        actionHref: '/',
        secondaryAction: 'Try Again',
        isReloadSecondary: true,
        ballAction: 'Return to the Pitch →',
        situation: 'pitch',
        showPlayer: true,
        playerImage: '/hero_player_cutout.webp',
        playerAlt: 'TopGrade pitch maintenance sideline player',
    },
};

const current = computed<ErrorStateConfig>(() => {
    if (errorStates[props.status]) {
        return errorStates[props.status];
    }
    return {
        code: String(props.status || 500),
        label: 'PLAY STOPPED',
        headline: 'Play has been stopped.',
        description: props.message || 'Something unexpected interrupted the match. Return to the club to continue.',
        action: 'Back to the Club',
        actionHref: '/',
        secondaryAction: 'Return to Pitch',
        secondaryHref: '/',
        ballAction: 'Restart Play →',
        situation: 'stoppage',
        showPlayer: false,
    };
});

const ballState = ref<'idle' | 'in-motion' | 'settled'>('idle');
const prefersReducedMotion = ref(false);

onMounted(() => {
    if (typeof window !== 'undefined' && window.matchMedia) {
        prefersReducedMotion.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }
});

function handleSecondaryClick() {
    if (current.value.isReloadSecondary) {
        window.location.reload();
    }
}

function handleBallInteraction() {
    if (ballState.value === 'in-motion') return;

    if (prefersReducedMotion.value) {
        router.visit(current.value.actionHref);
        return;
    }

    ballState.value = 'in-motion';

    // Ball executes authentic trajectory arc back onto the pitch before navigating
    setTimeout(() => {
        router.visit(current.value.actionHref);
    }, 620);
}
</script>

<template>
    <SeoHead
        :title="`${current.code} · ${current.label} | TopGrade London FC`"
        :description="current.description"
        path="/errors"
    />

    <main class="min-h-screen bg-tg-bg text-tg-text relative flex flex-col justify-between overflow-hidden select-none">
        <!-- Subtle Chalk Pitch Atmosphere Lines (No gradients, pure restrained lines) -->
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
            <!-- Midfield Centre Circle Arc (Right edge of pitch) -->
            <path
                d="M 1200 500 A 300 300 0 0 1 1200 1100"
                fill="none"
                stroke-width="1.5"
                class="hidden md:block"
            />
        </svg>

        <!-- Top Header Bar -->
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

        <!-- Main Matchday Notice Grid -->
        <section class="relative z-10 flex-1 max-w-6xl w-full mx-auto px-6 sm:px-12 py-12 lg:py-16 flex flex-col justify-center">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                
                <!-- Left Column: Editorial Information & Direct Actions -->
                <div class="lg:col-span-7 space-y-6 max-w-xl">
                    <!-- Whistle / Match Situation Pill -->
                    <div class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-xs border border-tg-border bg-tg-bg-deep text-tg-accent text-xs font-bold tracking-widest uppercase">
                        <span class="inline-block w-2 h-2 rounded-full bg-tg-accent animate-pulse" />
                        <span>{{ current.label }}</span>
                        <span class="text-tg-border-strong">|</span>
                        <span class="text-tg-text-muted">MATCHDAY STOPPAGE</span>
                    </div>

                    <!-- Large Display Status Code -->
                    <div class="leading-none">
                        <span
                            class="text-7xl sm:text-8xl md:text-9xl font-normal tracking-tight text-tg-text-strong block select-all"
                            style="font-family: var(--tg-display);"
                        >
                            {{ current.code }}
                        </span>
                    </div>

                    <!-- Headline & Explanation -->
                    <div class="space-y-3">
                        <h1
                            class="text-2xl sm:text-3xl md:text-4xl font-normal uppercase text-tg-text-strong tracking-wide"
                            style="font-family: var(--tg-display);"
                        >
                            {{ current.headline }}
                        </h1>
                        <p class="text-tg-text text-base sm:text-lg leading-relaxed text-tg-text-muted">
                            {{ current.description }}
                        </p>
                    </div>

                    <!-- Contextual Actions -->
                    <div class="pt-4 flex flex-wrap items-center gap-5">
                        <!-- Primary Action Button -->
                        <Link
                            :href="current.actionHref"
                            class="inline-flex items-center justify-center px-6 py-3 rounded-xs bg-tg-accent text-white font-bold text-xs uppercase tracking-widest hover:bg-tg-accent-hover transition-colors shadow-sm"
                        >
                            {{ current.action }}
                        </Link>

                        <!-- Secondary Action (Real browser reload for 500/503 or contextual link) -->
                        <button
                            v-if="current.isReloadSecondary"
                            type="button"
                            @click="handleSecondaryClick"
                            class="inline-flex items-center gap-2 text-xs uppercase tracking-widest text-tg-text-muted hover:text-tg-text-strong transition-colors cursor-pointer py-3"
                        >
                            <span>↻</span>
                            <span>{{ current.secondaryAction }}</span>
                        </button>

                        <Link
                            v-else-if="current.secondaryHref"
                            :href="current.secondaryHref"
                            class="inline-flex items-center gap-1.5 text-xs uppercase tracking-widest text-tg-text-muted hover:text-tg-text-strong transition-colors py-3"
                        >
                            <span>{{ current.secondaryAction }}</span>
                            <span>→</span>
                        </Link>
                    </div>
                </div>

                <!-- Right Column: Interactive Pitch Boundary & Ball Physics Zone -->
                <div class="lg:col-span-5 relative flex flex-col items-center lg:items-end justify-center min-h-[340px] sm:min-h-[420px]">
                    
                    <!-- Optional Compositional Editorial Player Cutout (Primary on 404 throw-in, optional on 503) -->
                    <div
                        v-if="current.showPlayer && current.playerImage"
                        class="absolute right-4 sm:right-12 bottom-6 pointer-events-none z-10 select-none transition-opacity duration-300"
                        :class="[ballState === 'in-motion' ? 'opacity-40' : 'opacity-85']"
                    >
                        <img
                            :src="current.playerImage"
                            :alt="current.playerAlt || 'TopGrade player'"
                            class="h-[280px] sm:h-[360px] md:h-[400px] w-auto object-contain filter drop-shadow-lg"
                            loading="eager"
                        />
                    </div>

                    <!-- Touchline Boundary Pitch Strip -->
                    <div class="relative w-full max-w-[340px] sm:max-w-[400px] pt-20 flex flex-col items-center lg:items-end">
                        
                        <!-- Interactive Match Ball -->
                        <div class="relative z-20 flex flex-col items-center">
                            
                            <!-- Ball trigger & physics container -->
                            <button
                                type="button"
                                @click="handleBallInteraction"
                                class="group relative focus:outline-none focus-visible:ring-2 focus-visible:ring-tg-accent rounded-full p-2 cursor-pointer transition-transform"
                                :class="[
                                    ballState === 'in-motion' ? 'ball-launch-trajectory' : 'hover:scale-105'
                                ]"
                                :aria-label="current.ballAction"
                            >
                                <img
                                    src="/ball-optimized.webp"
                                    alt="TopGrade Official Match Ball"
                                    class="w-24 h-24 sm:w-28 sm:h-28 object-contain"
                                    :class="[
                                        prefersReducedMotion ? '' : (ballState === 'in-motion' ? 'ball-spin-accelerated' : 'ball-spin-ambient')
                                    ]"
                                />
                                
                                <!-- Realistic Ground Contact Shadow -->
                                <div
                                    class="w-20 sm:w-24 h-3 mx-auto mt-1 rounded-full bg-black/70 blur-[3px] transition-all"
                                    :class="[ballState === 'in-motion' ? 'opacity-10 scale-50' : 'opacity-70']"
                                    aria-hidden="true"
                                />
                            </button>

                            <!-- Contextual Ball Action CTA Prompt -->
                            <button
                                type="button"
                                @click="handleBallInteraction"
                                class="mt-3 text-xs tracking-wider uppercase font-semibold text-tg-highlight hover:text-white transition-colors flex items-center gap-1.5 cursor-pointer py-1.5 px-3 rounded-xs border border-tg-border bg-tg-bg-deep/80"
                                :disabled="ballState === 'in-motion'"
                            >
                                <span v-if="ballState === 'in-motion'">Playing ball back in...</span>
                                <span v-else>{{ current.ballAction }}</span>
                            </button>
                        </div>

                        <!-- Crisp Chalk Touchline Marker -->
                        <div class="w-full mt-6 pt-3 border-t-2 border-dashed border-white/20 flex items-center justify-between text-[10px] tracking-widest uppercase text-tg-text-muted font-mono">
                            <span>TOUCHLINE // PITCH EDGE</span>
                            <span>{{ current.label }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </section>

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
    </main>
</template>

<style scoped>
/* Slow ambient rotation on the resting match ball */
.ball-spin-ambient {
    animation: ambientRoll 28s linear infinite;
}

@keyframes ambientRoll {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* Accelerated rotation during interaction */
.ball-spin-accelerated {
    animation: acceleratedRoll 0.6s cubic-bezier(0.2, 0.8, 0.4, 1) forwards;
}

@keyframes acceleratedRoll {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(-540deg);
    }
}

/* Authentic throw-in trajectory arc across touchline back into play */
.ball-launch-trajectory {
    animation: throwInArc 0.62s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

@keyframes throwInArc {
    0% {
        transform: translate3d(0, 0, 0) scale(1);
        opacity: 1;
    }
    45% {
        transform: translate3d(-60px, -45px, 0) scale(0.92);
        opacity: 0.95;
    }
    100% {
        transform: translate3d(-180px, 40px, 0) scale(0.6);
        opacity: 0;
    }
}

/* First-class reduced motion override: disable all animations */
@media (prefers-reduced-motion: reduce) {
    .ball-spin-ambient,
    .ball-spin-accelerated,
    .ball-launch-trajectory {
        animation: none !important;
        transform: none !important;
    }
}
</style>
