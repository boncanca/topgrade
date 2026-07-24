<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import EmptyState from '@/components/EmptyState.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import { index as activitiesIndex } from '@/routes/bookable-items';

interface Activity {
    id: number;
    name: string;
}

interface Schedule {
    id: number;
    starts_at: string;
    ends_at: string;
    capacity: number | null;
    location: string | null;
    status: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedSchedules {
    data: Schedule[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
}

const props = defineProps<{
    activity: Activity;
    schedules: PaginatedSchedules;
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

function formatDateTime(dateStr: string): string {
    const date = new Date(dateStr);
    return new Intl.DateTimeFormat(undefined, {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
}

function getStatusColor(status: string): string {
    return {
        active: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
        full: 'bg-amber-100 text-amber-800',
    }[status] || 'bg-gray-100 text-gray-800';
}

function deleteSchedule(schedule: Schedule): void {
    if (!confirm('Delete this schedule? This action cannot be undone.')) {
        return;
    }

    router.delete(
        route('bookable-items.schedules.destroy', [props.activity.id, schedule.id])
    );
}

function paginationLabel(label: string): string {
    return label.replace('&laquo;', 'Previous').replace('&raquo;', 'Next');
}
</script>

<template>
    <Head :title="`${activity.name} - Schedules`" />

    <PageHeader
        :title="`${activity.name} Sessions`"
        description="Manage training sessions and schedules for this activity."
    />

    <div class="space-y-6 p-6">
        <!-- Create Button -->
        <div class="flex justify-end">
            <Link
                :href="route('bookable-items.schedules.create', activity.id)"
                class="inline-flex items-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
            >
                + Add Schedule
            </Link>
        </div>

        <!-- Schedules Table -->
        <div v-if="schedules.total > 0" class="overflow-x-auto rounded-lg border border-input">
            <table class="w-full">
                <thead class="border-b border-input bg-muted/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-foreground">
                            Date & Time
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-foreground">
                            Location
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-foreground">
                            Capacity
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-foreground">
                            Status
                        </th>
                        <th class="px-6 py-3 text-right text-sm font-medium text-foreground">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-input">
                    <tr v-for="schedule in schedules.data" :key="schedule.id" class="hover:bg-muted/50">
                        <td class="px-6 py-4 text-sm">
                            {{ formatDateTime(schedule.starts_at) }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ schedule.location || '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ schedule.capacity || '—' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span :class="['inline-block rounded-full px-3 py-1 text-xs font-medium', getStatusColor(schedule.status)]">
                                {{ schedule.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="route('bookable-items.schedules.edit', [activity.id, schedule.id])"
                                    class="text-xs font-medium text-primary hover:underline"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="deleteSchedule(schedule)"
                                    class="text-xs font-medium text-destructive hover:underline"
                                >
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Empty State -->
        <EmptyState
            v-else
            title="No schedules yet"
            description="Create your first schedule to start accepting bookings."
            action-text="Create Schedule"
            :action-href="route('bookable-items.schedules.create', activity.id)"
        />

        <!-- Pagination -->
        <div v-if="schedules.links.length > 3" class="flex items-center justify-center gap-2">
            <Link
                v-for="link in schedules.links"
                :key="link.label"
                :href="link.url || '#'"
                :class="[
                    'rounded-md px-3 py-2 text-sm font-medium transition-colors',
                    link.active
                        ? 'bg-primary text-primary-foreground'
                        : 'border border-input text-foreground hover:bg-muted',
                ]"
            >
                {{ paginationLabel(link.label) }}
            </Link>
        </div>
    </div>
</template>
