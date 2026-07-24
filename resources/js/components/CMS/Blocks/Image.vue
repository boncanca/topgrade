<script setup lang="ts">
interface ImagePayload {
    image: { url: string; mediaId?: number | null } | string;
    alt?: string;
    caption?: string;
}

interface ImageSettings {
    size?: 'full' | 'large' | 'medium';
}

const props = defineProps<{
    payload: ImagePayload;
    settings?: ImageSettings;
}>();

const imageUrl = typeof props.payload.image === 'object'
    ? props.payload.image.url
    : props.payload.image;

const maxWidthClass: Record<string, string> = {
    full: 'max-w-full',
    large: 'max-w-4xl',
    medium: 'max-w-2xl',
};
</script>

<template>
    <figure
        class="mx-auto"
        :class="maxWidthClass[props.settings?.size ?? 'full']"
    >
        <img
            :src="imageUrl"
            :alt="props.payload.alt ?? ''"
            class="w-full rounded-xl border border-border object-cover"
        />
        <figcaption
            v-if="props.payload.caption"
            class="mt-2 text-center text-sm text-muted-foreground"
        >
            {{ props.payload.caption }}
        </figcaption>
    </figure>
</template>
