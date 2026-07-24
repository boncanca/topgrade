<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import {
    index as menuIndex,
    edit as menuEdit,
} from '@/routes/menus';

interface MenuItem {
    id: number;
    label: string;
    url: string | null;
    sort_order: number;
}

interface Menu {
    id: number;
    name: string;
    slug: string;
    location: string;
    items?: MenuItem[];
}

const props = defineProps<{
    menu: Menu;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Navigation',
                href: menuIndex(),
            },
            {
                title: 'View Menu',
            },
        ],
    },
});
</script>

<template>
    <Head :title="`Menu: ${props.menu.name}`" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            :title="props.menu.name"
            :description="`Details for menu located in ${props.menu.location}`"
        >
            <template #actions>
                <div class="flex gap-2">
                    <Button variant="outline" as-child>
                        <Link :href="menuIndex()">Back to Navigation</Link>
                    </Button>
                    <Button as-child>
                        <Link :href="menuEdit(props.menu.id)">Edit Menu</Link>
                    </Button>
                </div>
            </template>
        </PageHeader>

        <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-lg border border-border bg-background p-6 md:col-span-1">
                <h3 class="text-sm font-semibold text-foreground mb-4">Configuration</h3>
                <div class="space-y-3">
                    <div>
                        <span class="text-xs text-muted-foreground block uppercase font-medium">Slug</span>
                        <span class="text-sm font-mono text-foreground">{{ props.menu.slug }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-muted-foreground block uppercase font-medium">Location</span>
                        <span class="text-sm text-foreground capitalize">{{ props.menu.location }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-border bg-background p-6 md:col-span-2">
                <h3 class="text-sm font-semibold text-foreground mb-4">Menu Items</h3>
                <div v-if="props.menu.items && props.menu.items.length > 0" class="overflow-hidden rounded-md border border-border">
                    <table class="w-full table-auto text-sm">
                        <thead class="bg-muted/40 border-b border-border">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-muted-foreground">Label</th>
                                <th class="px-4 py-2 text-left font-medium text-muted-foreground">URL</th>
                                <th class="px-4 py-2 text-right font-medium text-muted-foreground">Sort Order</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            <tr v-for="item in props.menu.items" :key="item.id" class="hover:bg-muted/20">
                                <td class="px-4 py-3 font-medium text-foreground">{{ item.label }}</td>
                                <td class="px-4 py-3 text-muted-foreground font-mono text-xs">{{ item.url ?? '/' }}</td>
                                <td class="px-4 py-3 text-right text-muted-foreground">{{ item.sort_order }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else class="text-center py-8 text-muted-foreground text-sm">
                    No items in this menu yet.
                </div>
            </div>
        </div>
    </div>
</template>
