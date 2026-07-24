<script setup lang="ts">
import { defineAsyncComponent, h } from 'vue';

interface Block {
    id: number;
    uuid: string;
    type: string;
    payload: any;
    settings?: any;
}

const props = defineProps<{
    blocks: Block[];
}>();

const blockRegistry: Record<string, any> = {
    hero: defineAsyncComponent(() => import('./Blocks/Hero.vue')),
    rich_text: defineAsyncComponent(() => import('./Blocks/RichText.vue')),
    cta: defineAsyncComponent(() => import('./Blocks/CTA.vue')),
    image: defineAsyncComponent(() => import('./Blocks/Image.vue')),
    two_column: defineAsyncComponent(() => import('./Blocks/TwoColumn.vue')),
    feature_list: defineAsyncComponent(() => import('./Blocks/FeatureList.vue')),
};

// Fallback component for unregistered block types
const FallbackBlock = (blockProps: { type: string; uuid: string }) => {
    return h(
        'div',
        { class: 'p-4 border border-dashed border-red-500 text-red-500 rounded-lg bg-red-50 dark:bg-red-950/20 text-sm font-medium' },
        `Unsupported block type: "${blockProps.type}" (UUID: ${blockProps.uuid})`
    );
};
</script>

<template>
    <div class="cms-blocks flex flex-col gap-8">
        <template v-for="block in props.blocks" :key="block.uuid">
            <component
                :is="blockRegistry[block.type] || FallbackBlock"
                :payload="block.payload"
                :settings="block.settings"
                :type="block.type"
                :uuid="block.uuid"
                :data-block-type="block.type"
                :data-block-uuid="block.uuid"
            />
        </template>
    </div>
</template>
