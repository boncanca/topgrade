<script setup lang="ts">
import { Search, X } from '@lucide/vue';

defineProps<{ modelValue: string; count: number; mediaFilter: string }>();
const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'update:mediaFilter', value: 'all' | 'photos' | 'videos'): void;
}>();
const filters = [
    { value: 'all', label: 'All moments' },
    { value: 'photos', label: 'Photos' },
    { value: 'videos', label: 'Videos' },
] as const;
</script>

<template>
    <section class="mx-auto max-w-7xl px-4 pt-10 sm:px-6 sm:pt-14 lg:px-8">
        <div
            class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end"
        >
            <div>
                <p
                    class="text-tg-accent mb-3 text-xs font-semibold uppercase tracking-widest"
                >
                    The club in pictures
                </p>
                <h2 class="text-3xl leading-none text-white sm:text-4xl">
                    Every moment counts.
                </h2>
            </div>
            <div class="relative w-full sm:w-72">
                <label for="moment-search" class="sr-only"
                    >Search moments</label
                >
                <Search
                    class="text-tg-text-muted pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2"
                />
                <input
                    id="moment-search"
                    :value="modelValue"
                    type="search"
                    placeholder="Search moments"
                    class="border-tg-border bg-tg-bg-deep placeholder:text-tg-text-muted focus-visible:outline-tg-accent w-full rounded border py-3 pl-10 pr-10 text-sm text-white focus-visible:outline-2"
                    @input="
                        emit(
                            'update:modelValue',
                            ($event.target as HTMLInputElement).value,
                        )
                    "
                />
                <button
                    v-if="modelValue"
                    type="button"
                    class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-white"
                    aria-label="Clear search"
                    @click="emit('update:modelValue', '')"
                >
                    <X class="size-4" />
                </button>
            </div>
        </div>
        <div class="mt-7 flex flex-wrap items-center justify-between gap-4">
            <div
                class="flex flex-wrap gap-2"
                role="group"
                aria-label="Filter albums by media"
            >
                <button
                    v-for="filter in filters"
                    :key="filter.value"
                    type="button"
                    :aria-pressed="mediaFilter === filter.value"
                    class="focus-visible:outline-tg-accent rounded border px-4 py-2.5 text-sm transition-colors focus-visible:outline-2 focus-visible:outline-offset-2"
                    :class="
                        mediaFilter === filter.value
                            ? 'border-tg-accent bg-tg-accent text-white'
                            : 'border-tg-border bg-tg-bg-deep text-tg-text-muted hover:text-white'
                    "
                    @click="emit('update:mediaFilter', filter.value)"
                >
                    {{ filter.label }}
                </button>
            </div>
            <p class="text-tg-text-muted text-xs" role="status">
                {{ count }} {{ count === 1 ? 'album' : 'albums' }}
            </p>
        </div>
    </section>
</template>
