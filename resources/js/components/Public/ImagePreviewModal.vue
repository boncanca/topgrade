<script setup lang="ts">
import { X } from '@lucide/vue';
import {
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogOverlay,
    DialogPortal,
    DialogRoot,
    DialogTitle,
} from 'reka-ui';

export interface PreviewImageData {
    url: string;
    alt?: string;
    title?: string;
    subtitle?: string;
}

defineProps<{
    open: boolean;
    image: PreviewImageData | null;
}>();

defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();
</script>

<template>
    <DialogRoot :open="open" @update:open="$emit('update:open', $event)">
        <DialogPortal>
            <DialogOverlay
                class="fixed inset-0 z-50 bg-black/92 backdrop-blur-sm transition-opacity duration-300"
            />
            <DialogContent
                class="fixed inset-0 z-50 flex h-dvh flex-col justify-between p-3 text-white outline-none sm:p-6"
                aria-modal="true"
            >
                <!-- Header -->
                <div
                    class="flex shrink-0 items-center justify-between gap-4 border-b border-white/10 pb-3"
                >
                    <div class="min-w-0 pr-4">
                        <DialogTitle
                            class="truncate text-base font-semibold tracking-wide text-white sm:text-lg"
                        >
                            {{ image?.title || 'TopGrade London FC Photo' }}
                        </DialogTitle>
                        <DialogDescription
                            class="text-tg-text-muted mt-0.5 truncate text-xs"
                        >
                            {{
                                image?.subtitle ||
                                'Uncropped high-resolution club photography. Press Escape or tap close to return.'
                            }}
                        </DialogDescription>
                    </div>
                    <DialogClose
                        class="focus-visible:outline-tg-accent flex shrink-0 items-center gap-1.5 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-xs text-white transition-colors hover:bg-white/20 focus-visible:outline-2"
                        aria-label="Close photo preview"
                    >
                        <X class="size-4" />
                        <span class="hidden sm:inline">Close</span>
                    </DialogClose>
                </div>

                <!-- Uncropped Full-Image Viewport -->
                <div
                    class="relative flex min-h-0 grow items-center justify-center p-2 sm:p-4"
                >
                    <img
                        v-if="image"
                        :src="image.url"
                        :alt="image.alt || image.title || 'TopGrade London FC'"
                        class="max-h-[82vh] max-w-full rounded-md object-contain shadow-2xl select-none"
                    />
                </div>

                <!-- Footer Pill / Details -->
                <div
                    v-if="image?.alt"
                    class="flex shrink-0 items-center justify-center border-t border-white/10 pt-2 text-center"
                >
                    <p
                        class="text-tg-text-muted max-w-2xl text-xs italic leading-normal"
                    >
                        {{ image.alt }}
                    </p>
                </div>
            </DialogContent>
        </DialogPortal>
    </DialogRoot>
</template>
