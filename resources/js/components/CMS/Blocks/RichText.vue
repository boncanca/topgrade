<script setup lang="ts">
interface Payload {
    content: string;
}

interface Settings {
    prose_class?: string;
}

const props = defineProps<{
    payload: Payload;
    settings?: Settings;
}>();
</script>

<template>
    <div class="py-12">
        <div class="mx-auto max-w-3xl px-6 lg:px-8">
            <div
                class="prose dark:prose-invert text-foreground max-w-none leading-7"
                :class="props.settings?.prose_class ?? 'text-base'"
            >
                {{ props.payload.content }}
            </div>
        </div>
    </div>
</template>
