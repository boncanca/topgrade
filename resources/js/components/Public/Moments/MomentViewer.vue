<script setup lang="ts">
import { ChevronLeft, ChevronRight, Play, X } from '@lucide/vue';
import {
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogOverlay,
    DialogPortal,
    DialogRoot,
    DialogTitle,
} from 'reka-ui';
import { computed, nextTick, ref, watch } from 'vue';

interface GalleryItem {
    id: number;
    url: string;
    name: string;
    is_video?: boolean;
}
const props = defineProps<{ gallery: GalleryItem[]; title: string }>();
const open = ref(false);
const activeIndex = ref(0);
const failed = ref(false);
const current = computed(() => props.gallery[activeIndex.value]);
const thumbnailStrip = ref<HTMLElement | null>(null);
let opener: HTMLElement | null = null;
let touchStart: { x: number; y: number } | null = null;

function show(index: number, event: MouseEvent): void {
    if (!props.gallery[index]) {
        return;
    }

    opener = event.currentTarget as HTMLElement;
    activeIndex.value = index;
    open.value = true;
}
function move(offset: number): void {
    if (props.gallery.length > 1) {
        activeIndex.value =
            (activeIndex.value + offset + props.gallery.length) %
            props.gallery.length;
    }
}
function onKeydown(event: KeyboardEvent): void {
    // Preserve native seeking/volume controls while a video has focus.
    if ((event.target as HTMLElement).closest('video')) {
        return;
    }

    if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
        event.preventDefault();
        move(event.key === 'ArrowRight' ? 1 : -1);
    }
}
function restoreFocus(event: Event): void {
    event.preventDefault();
    opener?.focus();
}
function onTouchStart(event: TouchEvent): void {
    if ((event.target as HTMLElement).closest('video, button')) {
        return;
    }

    const touch = event.changedTouches[0];

    if (touch) {
        touchStart = { x: touch.screenX, y: touch.screenY };
    }
}
function onTouchEnd(event: TouchEvent): void {
    const touch = event.changedTouches[0];

    if (touchStart && touch) {
        const dx = touch.screenX - touchStart.x;
        const dy = touch.screenY - touchStart.y;

        if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) {
            move(dx < 0 ? 1 : -1);
        }
    }

    touchStart = null;
}
watch(activeIndex, async () => {
    failed.value = false;
    await nextTick();
    thumbnailStrip.value
        ?.querySelector('[aria-current="true"]')
        ?.scrollIntoView({ block: 'nearest', inline: 'nearest' });
});
watch(open, () => {
    failed.value = false;
});
watch(
    () => props.gallery,
    () => {
        open.value = false;
        activeIndex.value = 0;
    },
);
defineExpose({ show });
</script>

<template>
    <DialogRoot v-model:open="open">
        <DialogPortal>
            <DialogOverlay class="fixed inset-0 z-50 bg-black/95" />
            <DialogContent
                class="bg-tg-bg-deep fixed inset-0 z-50 flex h-dvh flex-col gap-3 p-3 text-white outline-none sm:p-6"
                @keydown="onKeydown"
                @close-auto-focus="restoreFocus"
            >
                <div
                    class="flex shrink-0 items-center justify-between gap-4 border-b border-white/15 pb-3"
                >
                    <div class="min-w-0">
                        <DialogTitle class="truncate text-lg sm:text-xl">{{
                            title
                        }}</DialogTitle>
                        <DialogDescription class="text-tg-text-muted text-xs"
                            >Use the arrow buttons or keys to browse. Press
                            Escape to close.</DialogDescription
                        >
                    </div>
                    <DialogClose
                        class="focus-visible:outline-tg-accent shrink-0 rounded-full border border-white/30 p-3 focus-visible:outline-2"
                        aria-label="Close photo viewer"
                        ><X class="size-5"
                    /></DialogClose>
                </div>
                <div
                    class="relative flex min-h-0 grow items-center justify-center"
                    @touchstart.passive="onTouchStart"
                    @touchend.passive="onTouchEnd"
                    @touchcancel="touchStart = null"
                >
                    <p
                        v-if="failed"
                        role="status"
                        class="text-tg-text-muted px-14 text-center text-sm"
                    >
                        This media could not be loaded. Please try another item
                        or reopen the viewer.
                    </p>
                    <video
                        v-else-if="current?.is_video"
                        :key="current.id"
                        :src="current.url"
                        :aria-label="current.name || title"
                        controls
                        playsinline
                        preload="metadata"
                        class="max-h-full max-w-full object-contain"
                        @error="failed = true"
                    />
                    <img
                        v-else-if="current"
                        :key="current.id"
                        :src="current.url"
                        :alt="current.name || title"
                        class="max-h-full max-w-full object-contain"
                        @error="failed = true"
                    />
                    <template v-if="gallery.length > 1">
                        <button
                            type="button"
                            aria-label="Previous photo"
                            class="focus-visible:outline-tg-accent absolute left-1 top-1/2 -translate-y-1/2 rounded-full border border-white/30 bg-black/80 p-3 focus-visible:outline-2 sm:left-4"
                            @click="move(-1)"
                        >
                            <ChevronLeft class="size-5" />
                        </button>
                        <button
                            type="button"
                            aria-label="Next photo"
                            class="focus-visible:outline-tg-accent absolute right-1 top-1/2 -translate-y-1/2 rounded-full border border-white/30 bg-black/80 p-3 focus-visible:outline-2 sm:right-4"
                            @click="move(1)"
                        >
                            <ChevronRight class="size-5" />
                        </button>
                    </template>
                </div>
                <p
                    class="text-tg-text-muted shrink-0 text-center text-xs"
                    aria-live="polite"
                    aria-atomic="true"
                >
                    {{ String(activeIndex + 1).padStart(2, '0') }} /
                    {{ String(gallery.length).padStart(2, '0')
                    }}<span v-if="current?.name"> · {{ current.name }}</span>
                </p>
                <div
                    ref="thumbnailStrip"
                    class="flex shrink-0 gap-2 overflow-x-auto py-1"
                    aria-label="Choose a photo"
                >
                    <button
                        v-for="(item, index) in gallery"
                        :key="item.id"
                        type="button"
                        :aria-label="`View ${item.is_video ? 'video' : 'photo'} ${index + 1}`"
                        :aria-current="
                            index === activeIndex ? 'true' : undefined
                        "
                        class="focus-visible:outline-tg-accent relative h-14 w-20 shrink-0 overflow-hidden rounded border-2 focus-visible:outline-2 focus-visible:outline-offset-2 sm:h-16 sm:w-24"
                        :class="
                            index === activeIndex
                                ? 'border-tg-accent'
                                : 'border-transparent opacity-60 hover:opacity-100'
                        "
                        @click="activeIndex = index"
                    >
                        <span
                            v-if="item.is_video"
                            class="flex h-full items-center justify-center bg-white/10"
                            ><Play class="size-5"
                        /></span>
                        <img
                            v-else
                            :src="item.url"
                            alt=""
                            loading="lazy"
                            class="h-full w-full object-cover"
                        />
                    </button>
                </div>
            </DialogContent>
        </DialogPortal>
    </DialogRoot>
</template>
