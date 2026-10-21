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
        <div class="flex items-start justify-between gap-8">
            <div>
                <h1
                    class="text-foreground mb-2 text-3xl font-semibold tracking-tight"
                >
                    {{ title }}
                </h1>
                <p v-if="description" class="text-muted-foreground text-sm">
                    {{ description }}
                </p>
            </div>
            <div class="flex items-center gap-4">
                <slot name="actions" />
            </div>
        </div>
    </div>
</template>
