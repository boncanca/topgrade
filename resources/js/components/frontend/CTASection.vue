<script setup lang="ts">
import { Button } from '@/components/ui/button';

interface Props {
  title: string;
  description?: string;
  primaryAction?: {
    label: string;
    href: string;
  };
  secondaryAction?: {
    label: string;
    href: string;
  };
  variant?: 'dark' | 'light' | 'gradient';
}

withDefaults(defineProps<Props>(), {
  variant: 'gradient',
});
</script>

<template>
  <section
    :class="{
      'section-base py-16 md:py-24': true,
      'bg-dark-section text-sidebar-foreground': variant === 'dark',
      'bg-surface-light': variant === 'light',
      'bg-gradient-primary text-white': variant === 'gradient',
    }"
  >
    <div class="container-center">
      <div class="flex flex-col items-center text-center">
        <h2 class="mb-4 max-w-3xl text-3xl font-bold md:text-4xl">
          {{ title }}
        </h2>

        <p v-if="description" class="mb-8 max-w-2xl text-lg opacity-90">
          {{ description }}
        </p>

        <div class="flex flex-wrap justify-center gap-4">
          <Button
            v-if="primaryAction"
            as-child
            :variant="variant === 'gradient' ? 'outline' : 'default'"
            class="text-base"
          >
            <a :href="primaryAction.href">
              {{ primaryAction.label }}
            </a>
          </Button>

          <Button
            v-if="secondaryAction"
            as-child
            :variant="variant === 'gradient' ? 'outline' : 'ghost'"
            class="text-base"
          >
            <a :href="secondaryAction.href">
              {{ secondaryAction.label }}
            </a>
          </Button>
        </div>
      </div>
    </div>
  </section>
</template>
