<script setup lang="ts">
import { ArrowUpRight, Monitor, Moon, Sun } from '@lucide/vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { useAppearance } from '@/composables/useAppearance';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const { appearance, updateAppearance } = useAppearance();

const appearances = [
    { value: 'light', label: 'Light', icon: Sun },
    { value: 'dark', label: 'Dark', icon: Moon },
    { value: 'system', label: 'System', icon: Monitor },
] as const;
</script>

<template>
    <header
        class="border-sidebar-border/70 group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 flex h-16 shrink-0 items-center justify-between gap-3 border-b px-4 transition-[width,height] ease-linear md:px-6"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger
                class="-ml-1 text-neutral-500 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-white"
            />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :items="breadcrumbs" />
            </template>
        </div>

        <div class="flex items-center gap-2.5">
            <!-- Theme Switcher -->
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <button
                        type="button"
                        class="size-8.5 shadow-2xs flex items-center justify-center rounded-lg border border-neutral-200 bg-white text-neutral-700 transition-colors hover:border-purple-300 hover:text-purple-600 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-300 dark:hover:border-purple-600/50 dark:hover:text-purple-300"
                        title="Switch theme"
                        aria-label="Switch theme"
                    >
                        <Sun v-if="appearance === 'light'" class="size-4" />
                        <Moon
                            v-else-if="appearance === 'dark'"
                            class="size-4"
                        />
                        <Monitor v-else class="size-4" />
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    align="end"
                    class="min-w-[130px] rounded-lg"
                >
                    <DropdownMenuItem
                        v-for="item in appearances"
                        :key="item.value"
                        @click="updateAppearance(item.value)"
                        :class="[
                            'flex cursor-pointer items-center gap-2 text-xs font-medium',
                            appearance === item.value
                                ? 'bg-purple-600 text-white focus:bg-purple-600 focus:text-white'
                                : 'text-neutral-700 dark:text-neutral-300',
                        ]"
                    >
                        <component :is="item.icon" class="size-3.5" />
                        <span>{{ item.label }}</span>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <!-- View Website Button -->
            <a
                href="/"
                target="_blank"
                rel="noopener noreferrer"
                class="shadow-2xs inline-flex items-center gap-1.5 rounded-lg border border-neutral-200 bg-white px-3 py-1.5 text-xs font-medium text-neutral-800 transition-all hover:border-purple-400 hover:text-purple-600 dark:border-neutral-800 dark:bg-neutral-900 dark:text-neutral-200 dark:hover:border-purple-500/50 dark:hover:text-purple-300"
            >
                <span>View Website</span>
                <ArrowUpRight
                    class="size-3.5 text-neutral-400 group-hover:text-purple-500"
                />
            </a>
        </div>
    </header>
</template>
