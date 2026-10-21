<script setup lang="ts">
interface Props {
    title: string;
    description?: string;
}

withDefaults(defineProps<Props>(), {
    description: undefined,
});
</script>

<template>
    <div class="mb-12">
        <div class="mb-6">
            <h2 class="text-foreground text-lg font-medium">{{ title }}</h2>
            <p v-if="description" class="text-muted-foreground mt-1 text-sm">
                {{ description }}
            </p>
        </div>
        <div class="space-y-6">
            <slot />
        </div>
    </div>
</template>
