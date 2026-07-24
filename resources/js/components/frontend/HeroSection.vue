<script setup lang="ts">
import { Button } from '@/components/ui/button';

interface Props {
  title: string;
  subtitle?: string;
  description?: string;
  backgroundImage?: string;
  actions?: Array<{
    label: string;
    href: string;
    variant?: 'default' | 'outline';
  }>;
}

defineProps<Props>();
</script>

<template>
  <section
    class="relative min-h-[500px] overflow-hidden bg-gradient-hero py-20 md:min-h-screen md:py-32"
    :style="backgroundImage ? { backgroundImage: `url('${backgroundImage}')` } : {}"
  >
    <div class="absolute inset-0 bg-gradient-hero pointer-events-none" />

    <div class="container-center relative z-10">
      <div class="max-w-3xl">
        <div v-if="subtitle" class="mb-4">
          <p class="text-sm font-semibold uppercase tracking-widest text-accent">
            {{ subtitle }}
          </p>
        </div>

        <h1
          class="mb-6 text-4xl font-bold text-sidebar-foreground md:text-5xl lg:text-6xl"
        >
          {{ title }}
        </h1>

        <p v-if="description" class="mb-8 text-lg text-sidebar-foreground/90 md:text-xl">
          {{ description }}
        </p>

        <div v-if="actions?.length" class="flex flex-wrap gap-4">
          <Button
            v-for="(action, idx) in actions"
            :key="idx"
            :variant="action.variant || 'default'"
            as-child
            class="text-base"
          >
            <a :href="action.href">
              {{ action.label }}
            </a>
          </Button>
        </div>
      </div>
    </div>
  </section>
</template>
