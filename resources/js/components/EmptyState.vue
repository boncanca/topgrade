<script setup lang="ts">
interface Props {
  title: string
  description?: string
}

withDefaults(defineProps<Props>(), {
  description: undefined,
})
</script>

<template>
  <div class="flex flex-col items-center justify-center py-24">
    <h3 class="text-lg font-medium text-foreground mb-2">
      {{ title }}
    </h3>
    <p v-if="description" class="text-sm text-muted-foreground mb-8 text-center max-w-md">
      {{ description }}
    </p>
    <slot />
  </div>
</template>
