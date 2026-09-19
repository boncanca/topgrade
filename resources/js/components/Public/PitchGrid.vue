<script setup lang="ts">
// Persistent global pitch grid environment layer (z-index: 2)
</script>

<template>
    <div class="tg-pitchgrid-layer" aria-hidden="true">
        <svg class="tg-pitchgrid-svg" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice">
            <defs>
                <!-- 72px Geometric Pitch Grid Pattern (No gradients, pure SVG strokes) -->
                <pattern
                    id="tactical-pitch-grid"
                    width="72"
                    height="72"
                    patternUnits="userSpaceOnUse"
                >
                    <path
                        d="M 72 0 L 0 0 0 72"
                        fill="none"
                        stroke="rgba(255, 255, 255, 0.05)"
                        stroke-width="1"
                    />
                    <!-- Micro crosshairs at grid intersections -->
                    <circle cx="0" cy="0" r="1" fill="rgba(255, 255, 255, 0.12)" />
                </pattern>
            </defs>

            <!-- Full Field Grid Pattern -->
            <rect width="100%" height="100%" fill="url(#tactical-pitch-grid)" />

            <!-- Pitch Markings (Authentic Youth / Grassroots Pitch Dimensions) -->
            <g class="pitch-lines" stroke="rgba(255, 255, 255, 0.08)" stroke-width="1.2" fill="none">
                <!-- Boundary lines -->
                <rect x="40" y="40" width="1360" height="820" />

                <!-- Halfway line -->
                <line x1="720" y1="40" x2="720" y2="860" />

                <!-- Centre circle & spot -->
                <circle cx="720" cy="450" r="90" />
                <circle cx="720" cy="450" r="3" fill="rgba(255, 255, 255, 0.15)" />

                <!-- Left penalty area & box -->
                <rect x="40" y="240" width="170" height="420" />
                <rect x="40" y="340" width="60" height="220" />
                <path d="M 210 370 A 90 90 0 0 1 210 530" />
                <circle cx="150" cy="450" r="2.5" fill="rgba(255, 255, 255, 0.15)" />

                <!-- Right penalty area & box -->
                <rect x="1230" y="240" width="170" height="420" />
                <rect x="1340" y="340" width="60" height="220" />
                <path d="M 1230 370 A 90 90 0 0 0 1230 530" />
                <circle cx="1290" cy="450" r="2.5" fill="rgba(255, 255, 255, 0.15)" />

                <!-- Corner Arcs -->
                <path d="M 40 60 A 20 20 0 0 0 60 40" />
                <path d="M 40 840 A 20 20 0 0 1 60 860" />
                <path d="M 1400 60 A 20 20 0 0 1 1380 40" />
                <path d="M 1400 840 A 20 20 0 0 0 1380 860" />
            </g>
        </svg>
    </div>
</template>

<style scoped>
.tg-pitchgrid-layer {
    position: fixed;
    inset: 0;
    z-index: 2;
    pointer-events: none;
    overflow: hidden;
}

.tg-pitchgrid-svg {
    width: 100%;
    height: 100%;
    will-change: transform;
    animation: pitchDrift 90s linear infinite;
}

@keyframes pitchDrift {
    0% {
        transform: translate3d(0, 0, 0);
    }
    50% {
        transform: translate3d(-18px, -18px, 0);
    }
    100% {
        transform: translate3d(0, 0, 0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .tg-pitchgrid-svg {
        animation: none;
    }
}
</style>
