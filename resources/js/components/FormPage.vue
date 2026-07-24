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
  <div class="min-h-screen bg-background">
    <div class="form-container py-12">
      <div class="mb-12">
        <h1 class="text-3xl font-semibold tracking-tight text-foreground mb-2">
          {{ title }}
        </h1>
        <p v-if="description" class="text-sm text-muted-foreground">
          {{ description }}
        </p>
      </div>

      <slot />
    </div>
  </div>
</template>
