<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import EmptyState from '@/components/EmptyState.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import {
    create as menuCreate,
    destroy as menuDestroy,
    edit as menuEdit,
    index as menuIndex,
    show as menuShow,
} from '@/routes/menus';

interface MenuItem {
    id: number;
    label: string;
    url: string | null;
}

interface Menu {
    id: number;
    name: string;
    slug: string;
    location: string;
    items?: MenuItem[];
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedMenus {
    data: Menu[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
}

const props = defineProps<{
    menus: PaginatedMenus;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Navigation',
                href: menuIndex(),
            },
        ],
    },
});

function paginationLabel(label: string): string {
    return label.replace('&laquo;', 'Previous').replace('&raquo;', 'Next');
}

function deleteMenu(menu: Menu): void {
    if (!confirm(`Delete menu "${menu.name}"? This cannot be undone.`)) {
        return;
    }

    router.delete(menuDestroy.url(menu.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Navigation Menus" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Navigation Menus"
            description="Manage site menus and navigation links."
        >
            <template #actions>
                <Button as-child>
                    <Link :href="menuCreate()">Create Menu</Link>
                </Button>
            </template>
        </PageHeader>

        <div
            v-if="props.menus.data.length > 0"
            class="overflow-hidden rounded-lg border border-border bg-background"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] table-auto">
                    <thead class="border-b border-border bg-muted/40">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Name
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Slug
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Location
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Items Count
                            </th>
                            <th
                                class="px-4 py-3 text-right text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr
                            v-for="menu in props.menus.data"
                            :key="menu.id"
                            class="transition-colors hover:bg-muted/40"
                        >
                            <td class="px-4 py-4">
                                <Link
                                    :href="menuShow(menu.id)"
                                    class="text-sm font-medium text-foreground underline-offset-4 hover:underline"
                                >
                                    {{ menu.name }}
                                </Link>
                            </td>
                            <td class="px-4 py-4 text-sm text-muted-foreground">
                                {{ menu.slug }}
                            </td>
                            <td class="px-4 py-4 text-sm text-muted-foreground capitalize">
                                {{ menu.location }}
                            </td>
                            <td class="px-4 py-4 text-sm text-muted-foreground">
                                {{ menu.items?.length ?? 0 }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="menuEdit(menu.id)">
                                            Edit
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive"
                                        @click="deleteMenu(menu)"
                                    >
                                        Delete
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                class="flex flex-col gap-4 border-t border-border px-4 py-3 text-sm text-muted-foreground sm:flex-row sm:items-center sm:justify-between"
            >
                <p>
                    Showing {{ props.menus.from }} to
                    {{ props.menus.to }} of {{ props.menus.total }}
                    menus
                </p>

                <div class="flex flex-wrap gap-1">
                    <template
                        v-for="link in props.menus.links"
                        :key="`${link.label}-${link.url}`"
                    >
                        <Button
                            v-if="link.url"
                            as-child
                            size="sm"
                            :variant="link.active ? 'default' : 'outline'"
                        >
                            <Link :href="link.url" preserve-scroll>
                                {{ paginationLabel(link.label) }}
                            </Link>
                        </Button>
                        <Button v-else size="sm" variant="outline" disabled>
                            {{ paginationLabel(link.label) }}
                        </Button>
                    </template>
                </div>
            </div>
        </div>

        <EmptyState
            v-else
            title="No menus yet"
            description="Create your first navigation menu."
        >
            <Button as-child>
                <Link :href="menuCreate()">Create Menu</Link>
            </Button>
        </EmptyState>
    </div>
</template>
