<script setup lang="ts">
import { ref, computed } from 'vue';
import { Camera, ChevronLeft, ChevronRight } from '@lucide/vue';
import MomentCard from './MomentCard.vue';

interface MomentMediaItem {
    id: number;
    url: string;
    name: string;
    mime_type?: string | null;
    is_video?: boolean;
}

interface MomentItem {
    id: number;
    title: string;
    slug: string;
    description: string | null;
    published_at: string | null;
    images_count: number;
    cover_url: string | null;
    cover_mime?: string | null;
    is_video?: boolean;
    featured?: boolean;
    media?: MomentMediaItem[];
}

const props = defineProps<{
    moments: MomentItem[];
    searchQuery?: string;
}>();

const emit = defineEmits<{
    (e: 'clearSearch'): void;
}>();

const activeId = ref<number | null>(props.moments[0]?.id ?? null);

function selectMoment(id: number): void {
    activeId.value = id;
}

// Return editorial aspect ratio classes based on index to create an engaging visual rhythm
function getAspectClass(idx: number, total: number): string {
    if (total === 1) {
        return 'aspect-16/10 sm:aspect-21/10 min-h-[380px]';
    }
    const pattern = idx % 5;
    switch (pattern) {
        case 0:
            return 'aspect-16/10 md:aspect-4/3 min-h-[340px]';
        case 1:
            return 'aspect-4/3 md:aspect-16/10 min-h-[340px]';
        case 2:
            return 'aspect-16/9 md:aspect-21/10 min-h-[360px] md:col-span-2';
        case 3:
            return 'aspect-4/3 md:aspect-3/4 min-h-[340px]';
        case 4:
            return 'aspect-16/10 md:aspect-4/3 min-h-[340px]';
        default:
            return 'aspect-16/10 min-h-[320px]';
    }
}

function getColumnSpan(idx: number, total: number): string {
    if (total >= 3 && idx % 5 === 2) {
        return 'md:col-span-2';
    }
    return 'col-span-1';
}

function nextActive(): void {
    if (props.moments.length <= 1) return;
    const currentIdx = props.moments.findIndex((m) => m.id === activeId.value);
    const nextIdx = (currentIdx + 1) % props.moments.length;
    activeId.value = props.moments[nextIdx].id;
}

function prevActive(): void {
    if (props.moments.length <= 1) return;
    const currentIdx = props.moments.findIndex((m) => m.id === activeId.value);
    const prevIdx = (currentIdx - 1 + props.moments.length) % props.moments.length;
    activeId.value = props.moments[prevIdx].id;
}
</script>

<template>
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-12">
        <!-- Empty State -->
        <div
            v-if="moments.length === 0"
            class="rounded-xl border border-dashed border-tg-border p-12 text-center bg-tg-bg-deep/40 my-8"
        >
            <Camera class="w-10 h-10 mx-auto text-tg-text-muted/60 mb-3" />
            <h3
                class="text-lg uppercase text-tg-text-strong font-normal mb-2"
                style="font-family: var(--tg-display);"
            >
                No Moments Found
            </h3>
            <p class="text-sm text-tg-text-muted max-w-sm mx-auto">
                {{ searchQuery ? `No stories matching "${searchQuery}".` : 'Club life, matchdays and training — coming soon.' }}
            </p>
            <button
                v-if="searchQuery"
                type="button"
                class="mt-4 text-xs font-semibold uppercase text-tg-accent hover:underline"
                @click="emit('clearSearch')"
            >
                Clear search
            </button>
        </div>

        <!-- Editorial Gallery Grid with Active Focus -->
        <div v-else>
            <!-- Navigation Indicator Bar for Visual Archive -->
            <div class="flex items-center justify-between gap-4 mb-6 text-xs text-tg-text-muted">
                <span class="font-mono text-[11px] uppercase tracking-wider">
                    Select a moment for focal depth
                </span>

                <!-- Quick Arrow Navigator -->
                <div v-if="moments.length > 1" class="flex items-center gap-2">
                    <button
                        type="button"
                        class="p-1 rounded border border-tg-border hover:border-tg-accent text-white flex items-center justify-center transition-colors focus:outline-none"
                        aria-label="Previous story in archive"
                        @click="prevActive"
                    >
                        <ChevronLeft class="w-4 h-4" />
                    </button>
                    <button
                        type="button"
                        class="p-1 rounded border border-tg-border hover:border-tg-accent text-white flex items-center justify-center transition-colors focus:outline-none"
                        aria-label="Next story in archive"
                        @click="nextActive"
                    >
                        <ChevronRight class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <!-- Asymmetrical Editorial Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 items-start">
                <div
                    v-for="(item, idx) in moments"
                    :key="item.id"
                    :class="getColumnSpan(idx, moments.length)"
                >
                    <MomentCard
                        :moment="item"
                        :is-active="activeId === item.id"
                        :has-active-sibling="activeId !== null && activeId !== item.id"
                        :aspect-class="getAspectClass(idx, moments.length)"
                        @select="selectMoment"
                    />
                </div>
            </div>
        </div>
    </main>
</template>
