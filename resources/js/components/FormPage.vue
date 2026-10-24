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
    <div class="bg-background min-h-screen">
        <div class="form-container py-12">
            <div class="mb-12">
                <h1
                    class="text-foreground mb-2 text-3xl font-semibold tracking-tight"
                >
                    {{ title }}
                </h1>
                <p v-if="description" class="text-muted-foreground text-sm">
                    {{ description }}
                </p>
            </div>

            <slot />
        </div>
    </div>
</template>
