<script setup lang="ts">
interface TwoColumnPayload {
    left_title?: string;
    left_body?: string;
    right_title?: string;
    right_body?: string;
}

interface TwoColumnSettings {
    ratio?: '1:1' | '2:1' | '1:2';
}

const props = defineProps<{
    payload: TwoColumnPayload;
    settings?: TwoColumnSettings;
}>();

const gridClass: Record<string, string> = {
    '1:1': 'grid-cols-1 md:grid-cols-2',
    '2:1': 'grid-cols-1 md:grid-cols-3',
    '1:2': 'grid-cols-1 md:grid-cols-3',
};

const leftColClass: Record<string, string> = {
    '1:1': 'md:col-span-1',
    '2:1': 'md:col-span-2',
    '1:2': 'md:col-span-1',
};

const rightColClass: Record<string, string> = {
    '1:1': 'md:col-span-1',
    '2:1': 'md:col-span-1',
    '1:2': 'md:col-span-2',
};

const ratio = props.settings?.ratio ?? '1:1';
</script>

<template>
    <div :class="['grid gap-8', gridClass[ratio]]">
        <div :class="leftColClass[ratio]">
            <h3 v-if="props.payload.left_title" class="mb-4 text-xl font-semibold text-foreground">
                {{ props.payload.left_title }}
            </h3>
            <!-- eslint-disable-next-line vue/no-v-html -->
            <div
                v-if="props.payload.left_body"
                class="prose prose-sm dark:prose-invert"
                v-html="props.payload.left_body"
            />
        </div>

        <div :class="rightColClass[ratio]">
            <h3 v-if="props.payload.right_title" class="mb-4 text-xl font-semibold text-foreground">
                {{ props.payload.right_title }}
            </h3>
            <!-- eslint-disable-next-line vue/no-v-html -->
            <div
                v-if="props.payload.right_body"
                class="prose prose-sm dark:prose-invert"
                v-html="props.payload.right_body"
            />
        </div>
    </div>
</template>
