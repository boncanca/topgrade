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
    <div class="flex flex-col items-center justify-center py-24">
        <h3 class="text-foreground mb-2 text-lg font-medium">
            {{ title }}
        </h3>
        <p
            v-if="description"
            class="text-muted-foreground mb-8 max-w-md text-center text-sm"
        >
            {{ description }}
        </p>
        <slot />
    </div>
</template>
