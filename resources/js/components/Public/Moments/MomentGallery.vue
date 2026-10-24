<script setup lang="ts">
import { Camera } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import MomentCard from './MomentCard.vue';

interface MomentItem {
    id: number;
    title: string;
    slug: string;
    published_at: string | null;
    images_count: number;
    cover_url: string | null;
    cover_mime?: string | null;
    media?: { is_video?: boolean }[];
}

const props = defineProps<{ moments: MomentItem[]; searchQuery?: string }>();
const emit = defineEmits<{ (e: 'clearSearch'): void }>();
const visibleCount = ref(8);
const visibleMoments = computed(() =>
    props.moments.slice(0, visibleCount.value),
);
watch(
    () => props.moments,
    () => {
        visibleCount.value = 8;
    },
);
</script>

<template>
    <section
        class="mx-auto max-w-7xl px-4 pb-12 pt-6 sm:px-6 lg:px-8"
        aria-label="Moment albums"
    >
        <div
            v-if="moments.length === 0"
            class="border-tg-border bg-tg-bg-deep my-8 rounded-xl border border-dashed p-12 text-center"
            role="status"
        >
            <Camera class="text-tg-text-muted mx-auto mb-4 size-10" />
            <h3 class="mb-2 text-xl text-white">No moments found</h3>
            <p class="text-tg-text-muted text-sm">
                {{
                    searchQuery
                        ? 'Try another search or clear your filters.'
                        : 'Club life, matchdays and training — coming soon.'
                }}
            </p>
            <button
                v-if="searchQuery"
                type="button"
                class="tg-btn ghost mt-6"
                @click="emit('clearSearch')"
            >
                Clear filters
            </button>
        </div>
        <div
            v-else
            class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3"
        >
            <MomentCard
                v-for="(item, index) in visibleMoments"
                :key="item.id"
                :moment="item"
                :class="{
                    'lg:col-span-2': index === 0 && visibleMoments.length > 1,
                }"
            />
        </div>
        <div v-if="moments.length > visibleCount" class="mt-8 text-center">
            <button
                type="button"
                class="tg-btn ghost"
                @click="visibleCount += 6"
            >
                Load more moments
            </button>
            <p class="text-tg-text-muted mt-3 text-xs" role="status">
                Showing {{ visibleMoments.length }} of
                {{ moments.length }} albums
            </p>
        </div>
    </section>
</template>
