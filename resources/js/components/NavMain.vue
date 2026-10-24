<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import type { NavGroup } from '@/types';

defineProps<{
    groups: NavGroup[];
}>();

const { isCurrentOrParentUrl, isCurrentUrl } = useCurrentUrl();
</script>

<template>
    <SidebarGroup
        v-for="(group, index) in groups"
        :key="group.title ?? index"
        class="px-2 py-0"
    >
        <SidebarGroupLabel v-if="group.title">{{
            group.title
        }}</SidebarGroupLabel>
        <SidebarMenu>
            <SidebarMenuItem v-for="item in group.items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="
                        item.exact
                            ? isCurrentUrl(item.href)
                            : isCurrentOrParentUrl(item.href)
                    "
                    :tooltip="item.title"
                    class="font-medium transition-all duration-200 hover:bg-purple-600/10 hover:text-purple-600 data-[active=true]:bg-purple-600 data-[active=true]:text-white data-[active=true]:shadow-md data-[active=true]:shadow-purple-900/30 dark:hover:bg-purple-950/40 dark:hover:text-purple-300"
                >
                    <Link :href="item.href">
                        <component :is="item.icon" class="size-4" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
