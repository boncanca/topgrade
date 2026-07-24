<script setup lang="ts">
interface Feature {
  id: string;
  icon?: string;
  title: string;
  description: string;
  color?: 'primary' | 'secondary' | 'accent';
}

interface Props {
  title: string;
  description?: string;
  features: Feature[];
  columns?: number;
  dark?: boolean;
}

withDefaults(defineProps<Props>(), {
  columns: 3,
  dark: false,
});
</script>

<template>
  <section
    :class="{
      'section-base': true,
      'bg-dark-section text-sidebar-foreground': dark,
    }"
  >
    <div class="container-center">
      <div class="mb-12 text-center md:mb-16">
        <h2 class="mb-4 text-3xl font-bold md:text-4xl">{{ title }}</h2>
        <p v-if="description" class="mx-auto max-w-2xl text-lg text-muted-foreground">
          {{ description }}
        </p>
      </div>

      <div
        :class="{
          'grid gap-8': true,
          'grid-cols-1 md:grid-cols-2 lg:grid-cols-3': columns === 3,
          'grid-cols-1 md:grid-cols-2': columns === 2,
          'grid-cols-1': columns === 1,
        }"
      >
        <div
          v-for="feature in features"
          :key="feature.id"
          class="group card-hover"
          :class="{
            'bg-dark-section/50 border-sidebar-border text-sidebar-foreground': dark,
          }"
        >
          <div
            v-if="feature.icon"
            class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg"
            :class="{
              'bg-primary/10 text-primary': feature.color === 'primary' || !feature.color,
              'bg-secondary/10 text-secondary': feature.color === 'secondary',
              'bg-accent/10 text-accent': feature.color === 'accent',
            }"
          >
            <i :class="`las ${feature.icon} text-2xl`" />
          </div>

          <h3 class="mb-2 text-xl font-semibold">{{ feature.title }}</h3>
          <p :class="{ 'text-sidebar-foreground/80': dark }">
            {{ feature.description }}
          </p>
        </div>
      </div>
    </div>
  </section>
</template>
