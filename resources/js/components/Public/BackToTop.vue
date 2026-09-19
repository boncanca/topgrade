<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { ArrowUp } from '@lucide/vue';

const isVisible = ref(false);

const checkScroll = () => {
    isVisible.value = window.scrollY > 400;
};

const scrollToTop = () => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    window.scrollTo({
        top: 0,
        behavior: prefersReducedMotion ? 'auto' : 'smooth',
    });
};

onMounted(() => {
    checkScroll();
    window.addEventListener('scroll', checkScroll, { passive: true });
});

onUnmounted(() => {
    window.removeEventListener('scroll', checkScroll);
});
</script>

<template>
    <Transition
        enter-active-class="transition-opacity duration-200"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-150"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <button
            v-show="isVisible"
            type="button"
            aria-label="Back to top"
            @click="scrollToTop"
            class="fixed bottom-5 right-5 sm:bottom-6 sm:right-6 z-35 flex h-10 w-10 items-center justify-center rounded-xs border border-tg-border bg-tg-bg-deep text-tg-text-muted transition-colors duration-200 hover:border-tg-accent hover:text-tg-text-strong focus:outline-none focus:ring-2 focus:ring-tg-accent focus:ring-offset-2 focus:ring-offset-tg-bg shadow-sm"
        >
            <ArrowUp class="h-4 w-4 sm:h-5 sm:w-5" />
        </button>
    </Transition>
</template>
