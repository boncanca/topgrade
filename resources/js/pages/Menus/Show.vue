<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { index as menuIndex, edit as menuEdit } from '@/routes/menus';

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
            <div
                class="border-border bg-background rounded-lg border p-6 md:col-span-1"
            >
                <h3 class="text-foreground mb-4 text-sm font-semibold">
                    Configuration
                </h3>
                <div class="space-y-3">
                    <div>
                        <span
                            class="text-muted-foreground block text-xs font-medium uppercase"
                            >Slug</span
                        >
                        <span class="text-foreground font-mono text-sm">{{
                            props.menu.slug
                        }}</span>
                    </div>
                    <div>
                        <span
                            class="text-muted-foreground block text-xs font-medium uppercase"
                            >Location</span
                        >
                        <span class="text-foreground text-sm capitalize">{{
                            props.menu.location
                        }}</span>
                    </div>
                </div>
            </div>

            <div
                class="border-border bg-background rounded-lg border p-6 md:col-span-2"
            >
                <h3 class="text-foreground mb-4 text-sm font-semibold">
                    Menu Items
                </h3>
                <div
                    v-if="props.menu.items && props.menu.items.length > 0"
                    class="border-border overflow-hidden rounded-md border"
                >
                    <table class="w-full table-auto text-sm">
                        <thead class="bg-muted/40 border-border border-b">
                            <tr>
                                <th
                                    class="text-muted-foreground px-4 py-2 text-left font-medium"
                                >
                                    Label
                                </th>
                                <th
                                    class="text-muted-foreground px-4 py-2 text-left font-medium"
                                >
                                    URL
                                </th>
                                <th
                                    class="text-muted-foreground px-4 py-2 text-right font-medium"
                                >
                                    Sort Order
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-border divide-y">
                            <tr
                                v-for="item in props.menu.items"
                                :key="item.id"
                                class="hover:bg-muted/20"
                            >
                                <td
                                    class="text-foreground px-4 py-3 font-medium"
                                >
                                    {{ item.label }}
                                </td>
                                <td
                                    class="text-muted-foreground px-4 py-3 font-mono text-xs"
                                >
                                    {{ item.url ?? '/' }}
                                </td>
                                <td
                                    class="text-muted-foreground px-4 py-3 text-right"
                                >
                                    {{ item.sort_order }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div
                    v-else
                    class="text-muted-foreground py-8 text-center text-sm"
                >
                    No items in this menu yet.
                </div>
            </div>
        </div>
    </div>
</template>
