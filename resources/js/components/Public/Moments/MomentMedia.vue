<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue';

const props = withDefaults(
    defineProps<{
        src: string;
        alt?: string;
        isVideo?: boolean;
        poster?: string | null;
        loading?: 'lazy' | 'eager';
        active?: boolean;
        fitClass?: string;
        positionClass?: string;
    }>(),
    {
        alt: 'Moment visual',
        isVideo: false,
        poster: null,
        loading: 'lazy',
        active: true,
        fitClass: 'object-cover',
        positionClass: 'object-center',
    }
);

const videoRef = ref<HTMLVideoElement | null>(null);
const videoError = ref(false);
const prefersReducedMotion = ref(false);

const checkReducedMotion = () => {
    prefersReducedMotion.value = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
};

watch(
    () => props.active,
    (isActive) => {
        if (!videoRef.value || prefersReducedMotion.value) return;
        if (isActive) {
            videoRef.value.play().catch(() => {
                videoError.value = true;
            });
        } else {
            videoRef.value.pause();
        }
    }
);

onMounted(() => {
    checkReducedMotion();
    if (props.isVideo && videoRef.value && props.active && !prefersReducedMotion.value) {
        videoRef.value.play().catch(() => {
            videoError.value = true;
        });
    }
});

onUnmounted(() => {
    if (videoRef.value) {
        videoRef.value.pause();
    }
});
</script>

<template>
    <div class="relative w-full h-full overflow-hidden bg-black select-none">
        <!-- Video Player -->
        <video
            v-if="isVideo && !videoError && !prefersReducedMotion"
            ref="videoRef"
            :src="src"
            :poster="poster || undefined"
            autoplay
            muted
            loop
            playsinline
            preload="metadata"
            class="w-full h-full transition-opacity duration-500"
            :class="[fitClass, positionClass]"
            @error="videoError = true"
        />

        <!-- Image or Video Fallback Poster -->
        <img
            v-else
            :src="isVideo && poster ? poster : src"
            :alt="alt"
            :loading="loading"
            decoding="async"
            class="w-full h-full transition-transform duration-700 ease-out"
            :class="[fitClass, positionClass]"
        />
    </div>
</template>
