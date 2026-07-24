<script setup lang="ts">
import { ref } from 'vue';

interface FAQItem {
  id: string;
  question: string;
  answer: string;
}

interface Props {
  title: string;
  description?: string;
  items: FAQItem[];
  dark?: boolean;
}

withDefaults(defineProps<Props>(), {
  dark: false,
});

const openId = ref<string | null>(null);

function toggleItem(id: string) {
  openId.value = openId.value === id ? null : id;
}
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

      <div class="mx-auto max-w-2xl space-y-4">
        <div
          v-for="item in items"
          :key="item.id"
          class="border border-border rounded-lg overflow-hidden"
          :class="{
            'bg-dark-section/50 border-sidebar-border': dark,
          }"
          style="transition: all 200ms cubic-bezier(0.4, 0, 0.2, 1);"
        >
          <button
            @click="toggleItem(item.id)"
            class="flex w-full items-center justify-between gap-4 px-6 py-4 text-left font-semibold hover:bg-muted/50 transition-colors"
            :class="{
              'hover:bg-sidebar-accent/30': dark,
            }"
          >
            <span>{{ item.question }}</span>
            <i
              :class="{
                'las la-chevron-down transition-transform': true,
                'rotate-180': openId === item.id,
              }"
            />
          </button>

          <div
            v-if="openId === item.id"
            class="border-t border-border px-6 py-4 text-muted-foreground animate-in slide-in-from-top-2"
            :class="{
              'border-sidebar-border text-sidebar-foreground/70': dark,
            }"
          >
            {{ item.answer }}
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
