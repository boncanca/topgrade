<script setup lang="ts">
import { Search, X } from '@lucide/vue';

defineProps<{
    modelValue: string;
    count: number;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();
</script>

<template>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-3">
        <div class="flex items-center justify-between gap-4 border-b border-tg-border/70 pb-4">
            <!-- Left: Archive Title and Count -->
            <div class="flex items-baseline gap-3">
                <h2 class="text-xs font-bold uppercase tracking-widest text-white font-mono">
                    Visual Archive
                </h2>
                <span class="text-xs text-tg-text-muted font-mono">
                    {{ count }} {{ count === 1 ? 'story' : 'stories' }}
                </span>
            </div>

            <!-- Right: Sleek Search Bar -->
            <div class="relative w-48 sm:w-64">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-tg-text-muted pointer-events-none" />
                <input
                    :value="modelValue"
                    type="text"
                    placeholder="Search moments..."
                    class="w-full rounded-full border border-tg-border bg-tg-bg-deep pl-8 pr-8 py-1.5 text-xs text-white placeholder:text-tg-text-muted outline-none focus:border-tg-accent focus:ring-1 focus:ring-tg-accent transition-all"
                    @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
                />
                <button
                    v-if="modelValue"
                    type="button"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-tg-text-muted hover:text-white"
                    aria-label="Clear search"
                    @click="emit('update:modelValue', '')"
                >
                    <X class="w-3.5 h-3.5" />
                </button>
            </div>
        </div>
    </section>
</template>
