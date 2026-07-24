<script setup lang="ts">
import { Button } from '@/components/ui/button';

interface PricingFeature {
  label: string;
  included: boolean;
}

interface Props {
  name: string;
  description?: string;
  price: number;
  period?: string;
  currency?: string;
  features: PricingFeature[];
  cta?: {
    label: string;
    href: string;
  };
  highlighted?: boolean;
}

withDefaults(defineProps<Props>(), {
  period: 'month',
  currency: '$',
  highlighted: false,
});
</script>

<template>
  <div
    class="card-hover relative flex flex-col overflow-hidden"
    :class="{
      'ring-2 ring-accent md:scale-105': highlighted,
    }"
  >
    <!-- Highlighted badge -->
    <div
      v-if="highlighted"
      class="absolute -right-12 top-6 w-40 rotate-45 bg-gradient-primary py-2 text-center text-sm font-bold text-white"
    >
      POPULAR
    </div>

    <!-- Header -->
    <div class="mb-6 border-b border-border pb-6">
      <h3 class="text-2xl font-bold">{{ name }}</h3>
      <p v-if="description" class="mt-2 text-muted-foreground">
        {{ description }}
      </p>
    </div>

    <!-- Price -->
    <div class="mb-8">
      <div class="flex items-baseline gap-1">
        <span class="text-5xl font-bold">{{ currency }}{{ price }}</span>
        <span class="text-muted-foreground">/{{ period }}</span>
      </div>
    </div>

    <!-- Features -->
    <div class="mb-8 flex-1">
      <ul class="space-y-4">
        <li
          v-for="(feature, idx) in features"
          :key="idx"
          class="flex items-center gap-3"
          :class="{ 'opacity-50': !feature.included }"
        >
          <span
            class="flex h-5 w-5 items-center justify-center rounded-full"
            :class="{
              'bg-primary/20 text-primary': feature.included,
              'bg-muted text-muted-foreground': !feature.included,
            }"
          >
            <i
              :class="{
                'las la-check text-sm': feature.included,
                'las la-times text-xs': !feature.included,
              }"
            />
          </span>
          <span class="text-sm">{{ feature.label }}</span>
        </li>
      </ul>
    </div>

    <!-- CTA Button -->
    <Button
      v-if="cta"
      as-child
      :variant="highlighted ? 'default' : 'outline'"
      class="w-full"
    >
      <a :href="cta.href">
        {{ cta.label }}
      </a>
    </Button>
  </div>
</template>
