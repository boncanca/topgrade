<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { computed, ref, onMounted } from 'vue';
import SeoHead from '@/components/SEO/SeoHead.vue';
import ErrorLayout from '@/layouts/ErrorLayout.vue';

defineOptions({
    layout: ErrorLayout,
});

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
    situation:
        | 'throw-in'
        | 'referee'
        | 'halftime'
        | 'var'
        | 'pitch'
        | 'stoppage';
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
        playerAlt: 'TopGrade youth player preparing touchline throw-in',
    },
    403: {
        code: '403',
        label: "REFEREE'S CALL",
        headline: 'Access denied.',
        description:
            "The referee has stopped play. You don't have permission to enter this area.",
        action: 'Return to the Club',
        actionHref: '/',
        secondaryAction: 'Go to Sign In',
        secondaryHref: '/auth/login',
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
        description:
            props.message ||
            'Something unexpected interrupted the match. Return to the club to continue.',
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
        prefersReducedMotion.value = window.matchMedia(
            '(prefers-reduced-motion: reduce)',
        ).matches;
    }
});

function handleSecondaryClick() {
    if (current.value.isReloadSecondary) {
        window.location.reload();
    }
}

function handleBallInteraction() {
    if (ballState.value === 'in-motion') {
        return;
    }

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

    <!-- Main Matchday Notice Grid inside ErrorLayout -->
    <section
        class="relative z-10 mx-auto flex w-full max-w-6xl flex-col justify-center px-6 py-12 sm:px-12 lg:py-16"
    >
        <div class="grid grid-cols-1 items-center gap-10 lg:grid-cols-12">
            <!-- Left Column: Editorial Information & Direct Actions -->
            <div class="max-w-xl space-y-6 lg:col-span-7">
                <!-- Whistle / Match Situation Pill -->
                <div
                    class="rounded-xs border-tg-border bg-tg-bg-deep text-tg-accent inline-flex items-center gap-2.5 border px-3 py-1.5 text-xs font-bold uppercase tracking-widest"
                >
                    <span
                        class="bg-tg-accent inline-block h-2 w-2 animate-pulse rounded-full"
                    />
                    <span>{{ current.label }}</span>
                    <span class="text-tg-border-strong">|</span>
                    <span class="text-tg-text-muted">MATCHDAY STOPPAGE</span>
                </div>

                <!-- Large Display Status Code -->
                <div class="leading-none">
                    <span
                        class="text-tg-text-strong block select-all text-7xl font-normal tracking-tight sm:text-8xl md:text-9xl"
                        style="font-family: var(--tg-display)"
                    >
                        {{ current.code }}
                    </span>
                </div>

                <!-- Headline & Explanation -->
                <div class="space-y-3">
                    <h1
                        class="text-tg-text-strong text-2xl font-normal uppercase tracking-wide sm:text-3xl md:text-4xl"
                        style="font-family: var(--tg-display)"
                    >
                        {{ current.headline }}
                    </h1>
                    <p
                        class="text-tg-text text-tg-text-muted text-base leading-relaxed sm:text-lg"
                    >
                        {{ current.description }}
                    </p>
                </div>

                <!-- Contextual Actions -->
                <div class="flex flex-wrap items-center gap-5 pt-4">
                    <!-- Primary Action Button -->
                    <Link
                        :href="current.actionHref"
                        class="rounded-xs bg-tg-accent hover:bg-tg-accent-hover inline-flex items-center justify-center px-6 py-3 text-xs font-bold uppercase tracking-widest text-white shadow-sm transition-colors"
                    >
                        {{ current.action }}
                    </Link>

                    <!-- Secondary Action (Real browser reload for 500/503 or contextual link) -->
                    <button
                        v-if="current.isReloadSecondary"
                        type="button"
                        @click="handleSecondaryClick"
                        class="text-tg-text-muted hover:text-tg-text-strong inline-flex cursor-pointer items-center gap-2 py-3 text-xs uppercase tracking-widest transition-colors"
                    >
                        <span>↻</span>
                        <span>{{ current.secondaryAction }}</span>
                    </button>

                    <Link
                        v-else-if="current.secondaryHref"
                        :href="current.secondaryHref"
                        class="text-tg-text-muted hover:text-tg-text-strong inline-flex items-center gap-1.5 py-3 text-xs uppercase tracking-widest transition-colors"
                    >
                        <span>{{ current.secondaryAction }}</span>
                        <span>→</span>
                    </Link>
                </div>
            </div>

            <!-- Right Column: Interactive Pitch Boundary & Ball Physics Zone -->
            <div
                class="relative flex min-h-[340px] flex-col items-center justify-center sm:min-h-[420px] lg:col-span-5 lg:items-end"
            >
                <!-- Optional Compositional Editorial Player Cutout (Primary on 404 throw-in, optional on 503) -->
                <div
                    v-if="current.showPlayer && current.playerImage"
                    class="pointer-events-none absolute bottom-6 right-4 z-10 select-none transition-opacity duration-300 sm:right-12"
                    :class="[
                        ballState === 'in-motion' ? 'opacity-40' : 'opacity-85',
                    ]"
                >
                    <img
                        :src="current.playerImage"
                        :alt="
                            current.playerAlt || 'TopGrade youth player action'
                        "
                        class="h-[280px] w-auto object-contain drop-shadow-lg filter sm:h-[360px] md:h-[400px]"
                        loading="eager"
                    />
                </div>

                <!-- Touchline Boundary Pitch Strip -->
                <div
                    class="relative flex w-full max-w-[340px] flex-col items-center pt-20 sm:max-w-[400px] lg:items-end"
                >
                    <!-- Interactive Match Ball -->
                    <div class="relative z-20 flex flex-col items-center">
                        <!-- Ball trigger & physics container -->
                        <button
                            type="button"
                            @click="handleBallInteraction"
                            class="focus-visible:ring-tg-accent group relative cursor-pointer rounded-full p-2 transition-transform focus:outline-none focus-visible:ring-2"
                            :class="[
                                ballState === 'in-motion'
                                    ? 'ball-launch-trajectory'
                                    : 'hover:scale-105',
                            ]"
                            :aria-label="current.ballAction"
                        >
                            <img
                                src="/ball-optimized.webp"
                                alt="TopGrade Official Match Ball"
                                class="h-24 w-24 object-contain sm:h-28 sm:w-28"
                                :class="[
                                    prefersReducedMotion
                                        ? ''
                                        : ballState === 'in-motion'
                                          ? 'ball-spin-accelerated'
                                          : 'ball-spin-ambient',
                                ]"
                            />

                            <!-- Ground Contact Shadow -->
                            <div
                                class="mx-auto mt-1 h-3 w-20 rounded-full bg-black/70 blur-[3px] transition-all sm:w-24"
                                :class="[
                                    ballState === 'in-motion'
                                        ? 'scale-50 opacity-10'
                                        : 'opacity-70',
                                ]"
                                aria-hidden="true"
                            />
                        </button>

                        <!-- Contextual Ball Action CTA Prompt -->
                        <button
                            type="button"
                            @click="handleBallInteraction"
                            class="text-tg-highlight rounded-xs border-tg-border bg-tg-bg-deep/80 mt-3 flex cursor-pointer items-center gap-1.5 border px-3 py-1.5 text-xs font-semibold uppercase tracking-wider transition-colors hover:text-white"
                            :disabled="ballState === 'in-motion'"
                        >
                            <span v-if="ballState === 'in-motion'"
                                >Playing ball back in...</span
                            >
                            <span v-else>{{ current.ballAction }}</span>
                        </button>
                    </div>

                    <!-- Crisp Chalk Touchline Marker -->
                    <div
                        class="text-tg-text-muted mt-6 flex w-full items-center justify-between border-t-2 border-dashed border-white/20 pt-3 font-mono text-[10px] uppercase tracking-widest"
                    >
                        <span>TOUCHLINE // PITCH EDGE</span>
                        <span>{{ current.label }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
