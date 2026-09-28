<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Camera, ChevronLeft, ChevronRight, Play } from '@lucide/vue';
import MomentMedia from './MomentMedia.vue';

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

const props = withDefaults(
    defineProps<{
        moment: MomentItem;
        isActive?: boolean;
        hasActiveSibling?: boolean;
        aspectClass?: string;
    }>(),
    {
        isActive: false,
        hasActiveSibling: false,
        aspectClass: 'aspect-16/10 sm:aspect-4/3 min-h-[300px]',
    }
);

const emit = defineEmits<{
    (e: 'select', momentId: number): void;
}>();

const currentPhotoIdx = ref(0);

const mediaList = computed(() => {
    if (props.moment.media && props.moment.media.length > 0) {
        return props.moment.media;
    }
    if (props.moment.cover_url) {
        return [
            {
                id: 0,
                url: props.moment.cover_url,
                name: props.moment.title,
                mime_type: props.moment.cover_mime,
                is_video: props.moment.is_video,
            },
        ];
    }
    return [];
});

const activeMedia = computed(() => {
    return mediaList.value[currentPhotoIdx.value] || null;
});

function nextMedia(e: Event): void {
    e.stopPropagation();
    e.preventDefault();
    if (mediaList.value.length <= 1) return;
    currentPhotoIdx.value = (currentPhotoIdx.value + 1) % mediaList.value.length;
}

function prevMedia(e: Event): void {
    e.stopPropagation();
    e.preventDefault();
    if (mediaList.value.length <= 1) return;
    currentPhotoIdx.value = (currentPhotoIdx.value - 1 + mediaList.value.length) % mediaList.value.length;
}

function setMedia(idx: number, e: Event): void {
    e.stopPropagation();
    e.preventDefault();
    currentPhotoIdx.value = idx;
}
</script>

<template>
    <div
        class="relative transition-all duration-400 ease-out"
        :class="[
            isActive
                ? 'z-20 scale-[1.02] sm:scale-[1.025]'
                : hasActiveSibling
                  ? 'opacity-80 sm:opacity-85 hover:opacity-100 hover:scale-[1.01]'
                  : 'hover:scale-[1.01]',
        ]"
        @click="emit('select', moment.id)"
    >
        <!-- The Editorial Moment Container -->
        <div
            class="block w-full h-full rounded-xl overflow-hidden border bg-tg-bg-deep relative transition-all duration-300 shadow-lg group"
            :class="[
                isActive
                    ? 'border-tg-accent/80 shadow-2xl ring-1 ring-tg-accent/40'
                    : 'border-tg-border hover:border-tg-border/90',
            ]"
        >
            <!-- Media Frame with Varied Aspect Ratio -->
            <div
                class="relative w-full overflow-hidden bg-black flex items-center justify-center select-none"
                :class="aspectClass"
            >
                <!-- Render Photography or Video -->
                <template v-if="activeMedia">
                    <MomentMedia
                        :src="activeMedia.url"
                        :alt="activeMedia.name || moment.title"
                        :is-video="Boolean(activeMedia.is_video)"
                        :poster="moment.cover_url"
                        :active="isActive"
                        fit-class="object-cover"
                        class="w-full h-full group-hover:scale-105 transition-transform duration-700 ease-out"
                    />
                </template>

                <div
                    v-else
                    class="w-full h-full flex items-center justify-center text-tg-text-muted/40 bg-tg-bg-deep"
                >
                    <Camera class="w-10 h-10" />
                </div>

                <!-- Video Badge if media is video -->
                <div
                    v-if="moment.is_video || activeMedia?.is_video"
                    class="absolute top-3 left-3 z-15 px-2 py-1 rounded bg-black/75 border border-white/15 flex items-center gap-1.5 text-[10px] uppercase font-mono tracking-wider text-white"
                >
                    <Play class="w-3 h-3 fill-tg-accent text-tg-accent" />
                    <span>Video</span>
                </div>

                <!-- Multi-image Quick Nav on Active/Hovered Card -->
                <div
                    v-if="mediaList.length > 1"
                    class="absolute top-3 right-3 z-15 flex items-center gap-1.5 opacity-90 transition-opacity"
                >
                    <button
                        type="button"
                        class="w-6 h-6 rounded-full bg-black/75 border border-white/20 text-white flex items-center justify-center hover:bg-black focus:outline-none"
                        aria-label="Previous image"
                        @click="prevMedia"
                    >
                        <ChevronLeft class="w-3.5 h-3.5" />
                    </button>
                    <div class="px-2 py-0.5 rounded-full bg-black/75 border border-white/15 font-mono text-[10px] text-white">
                        {{ currentPhotoIdx + 1 }}/{{ mediaList.length }}
                    </div>
                    <button
                        type="button"
                        class="w-6 h-6 rounded-full bg-black/75 border border-white/20 text-white flex items-center justify-center hover:bg-black focus:outline-none"
                        aria-label="Next image"
                        @click="nextMedia"
                    >
                        <ChevronRight class="w-3.5 h-3.5" />
                    </button>
                </div>

                <!-- Solid Translucent Lower Tray (No Gradients, Restrained) -->
                <div
                    class="absolute inset-x-0 bottom-0 p-4 sm:p-5 z-10 flex flex-col justify-end bg-black/85 border-t border-white/10"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <!-- Moment Title -->
                            <h3
                                class="text-base sm:text-lg font-normal uppercase text-white tracking-tight leading-snug group-hover:text-tg-accent transition-colors truncate"
                                style="font-family: var(--tg-display);"
                            >
                                {{ moment.title }}
                            </h3>

                            <!-- Moment Description -->
                            <p
                                v-if="moment.description"
                                class="text-xs text-gray-300 mt-1 line-clamp-1 leading-relaxed font-normal"
                            >
                                {{ moment.description }}
                            </p>
                        </div>

                        <!-- Explore Link Icon -->
                        <Link
                            :href="`/moments/${moment.slug}`"
                            class="shrink-0 p-1.5 rounded-full border border-white/20 hover:border-tg-accent hover:bg-tg-accent hover:text-black text-white transition-all"
                            :aria-label="`View story: ${moment.title}`"
                            @click.stop
                        >
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>

                    <!-- Meta Bar: Date, Indicator Pips & Photos Count -->
                    <div class="pt-2.5 mt-2.5 border-t border-white/10 flex items-center justify-between text-[11px] text-gray-400 font-mono">
                        <span>{{ moment.published_at || 'Matchday Story' }}</span>

                        <!-- Multiple Photo Pips integrated into meta bar -->
                        <div
                            v-if="mediaList.length > 1"
                            class="flex items-center gap-1.5"
                        >
                            <button
                                v-for="(_, pIdx) in mediaList"
                                :key="pIdx"
                                type="button"
                                class="h-1.5 rounded-full transition-all duration-300 focus:outline-none"
                                :class="pIdx === currentPhotoIdx ? 'w-4 bg-tg-accent' : 'w-1.5 bg-white/40 hover:bg-white/70'"
                                :aria-label="`Go to image ${pIdx + 1}`"
                                @click="setMedia(pIdx, $event)"
                            />
                        </div>

                        <span>{{ moment.images_count }} {{ moment.images_count === 1 ? 'photo' : 'photos' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
