<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import EmptyState from '@/components/EmptyState.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import {
    create as activitiesCreate,
    destroy as activitiesDestroy,
    edit as activitiesEdit,
    index as activitiesIndex,
} from '@/routes/bookable-items';
import { index as schedulesIndex } from '@/routes/bookable-items/schedules';

interface Activity {
    id: number;
    name: string;
    duration_minutes: number;
    location: string;
    price: number | null;
    currency: string;
    is_active: boolean;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedActivities {
    data: Activity[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
}

const props = defineProps<{
    items: PaginatedActivities;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Activities',
                href: activitiesIndex(),
            },
        ],
    },
});

function formatPrice(price: number | null, currency: string): string {
    if (price === null) {
        return 'Free';
    }

    return new Intl.NumberFormat(undefined, {
        style: 'currency',
        currency: currency || 'USD',
    }).format(price);
}

function formatDuration(minutes: number): string {
    if (minutes < 60) {
        return `${minutes}m`;
    }

    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;

    return mins > 0 ? `${hours}h ${mins}m` : `${hours}h`;
}

function paginationLabel(label: string): string {
    return label.replace('&laquo;', 'Previous').replace('&raquo;', 'Next');
}

function deleteActivity(item: Activity): void {
    if (!confirm(`Delete "${item.name}"? This action cannot be undone.`)) {
        return;
    }

    router.delete(activitiesDestroy.url(item.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Activities" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Activities"
            description="Manage activities, sessions, and bookable offerings."
        >
            <template #actions>
                <Button as-child>
                    <Link :href="activitiesCreate()">Create Activity</Link>
                </Button>
            </template>
        </PageHeader>

        <div
            v-if="props.items.data.length > 0"
            class="border-border bg-background overflow-hidden rounded-lg border"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] table-auto">
                    <thead class="border-border bg-muted/40 border-b">
                        <tr>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Name
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Duration
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Location
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Price
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Status
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-right text-xs font-medium uppercase tracking-wide"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-border divide-y">
                        <tr
                            v-for="item in props.items.data"
                            :key="item.id"
                            class="hover:bg-muted/40 transition-colors"
                        >
                            <td class="px-4 py-4">
                                <Link
                                    :href="activitiesEdit(item.id)"
                                    class="text-foreground text-sm font-medium underline-offset-4 hover:underline"
                                >
                                    {{ item.name }}
                                </Link>
                            </td>
                            <td
                                class="text-muted-foreground whitespace-nowrap px-4 py-4 text-sm"
                            >
                                {{ formatDuration(item.duration_minutes) }}
                            </td>
                            <td
                                class="text-muted-foreground whitespace-nowrap px-4 py-4 text-sm"
                            >
                                {{ item.location || '—' }}
                            </td>
                            <td
                                class="text-muted-foreground whitespace-nowrap px-4 py-4 text-sm"
                            >
                                {{ formatPrice(item.price, item.currency) }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <span
                                    :class="[
                                        item.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800',
                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                    ]"
                                >
                                    {{ item.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        class="border-purple-200 font-semibold text-purple-700 hover:bg-purple-50 dark:border-purple-900 dark:text-purple-300 dark:hover:bg-purple-950"
                                        as-child
                                    >
                                        <Link :href="schedulesIndex(item.id)">
                                            📅 Manage Schedules
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="activitiesEdit(item.id)">
                                            Edit
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive"
                                        @click="deleteActivity(item)"
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
                class="border-border text-muted-foreground flex flex-col gap-4 border-t px-4 py-3 text-sm sm:flex-row sm:items-center sm:justify-between"
            >
                <p>
                    Showing {{ props.items.from }} to {{ props.items.to }} of
                    {{ props.items.total }}
                    entries
                </p>

                <div class="flex flex-wrap gap-1">
                    <template
                        v-for="link in props.items.links"
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
            title="No activities yet"
            description="Create your first activity, session, or offering to start accepting bookings."
        >
            <Button as-child>
                <Link :href="activitiesCreate()">Create Activity</Link>
            </Button>
        </EmptyState>
    </div>
</template>
