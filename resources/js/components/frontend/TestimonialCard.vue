<script setup lang="ts">
interface Props {
  name: string;
  role?: string;
  content: string;
  image?: string;
  rating?: number;
}

defineProps<Props>();
</script>

<template>
  <div class="card-hover flex flex-col">
    <!-- Rating -->
    <div v-if="rating" class="mb-4 flex gap-1">
      <i
        v-for="i in 5"
        :key="i"
        :class="{
          'las la-star text-accent text-lg': i <= rating,
          'las la-star text-border text-lg': i > rating,
        }"
      />
    </div>

    <!-- Content -->
    <p class="mb-6 flex-1 text-foreground">
      "{{ content }}"
    </p>

    <!-- Author -->
    <div class="flex items-center gap-4 border-t border-border pt-4">
      <div v-if="image" class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-full bg-muted">
        <img
          :src="image"
          :alt="name"
          class="h-full w-full object-cover"
        />
      </div>

      <div class="flex-1">
        <p class="font-semibold text-foreground">{{ name }}</p>
        <p v-if="role" class="text-sm text-muted-foreground">{{ role }}</p>
      </div>
    </div>
  </div>
</template>
