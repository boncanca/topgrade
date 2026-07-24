<script setup lang="ts">
interface FeatureItem {
    icon?: string;
    text: string;
}

interface FeatureListPayload {
    title?: string;
    items?: FeatureItem[];
}

const props = defineProps<{
    payload: FeatureListPayload;
}>();
</script>

<template>
    <div>
        <h3 v-if="props.payload.title" class="mb-6 text-2xl font-semibold text-foreground">
            {{ props.payload.title }}
        </h3>

        <ul v-if="props.payload.items?.length" class="grid gap-4 sm:grid-cols-2">
            <li
                v-for="(item, idx) in props.payload.items"
                :key="idx"
                class="flex items-start gap-3"
            >
                <span
                    v-if="item.icon"
                    class="mt-0.5 flex-shrink-0 text-xl leading-none"
                    aria-hidden="true"
                >
                    {{ item.icon }}
                </span>
                <span class="text-sm text-foreground leading-relaxed">{{ item.text }}</span>
            </li>
        </ul>
    </div>
</template>
