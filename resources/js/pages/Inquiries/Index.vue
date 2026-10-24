<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import EmptyState from '@/components/EmptyState.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import {
    destroy as inquiriesDestroy,
    edit as inquiriesEdit,
    index as inquiriesIndex,
    show as inquiriesShow,
} from '@/routes/inquiries';

interface Inquiry {
    id: number;
    name: string;
    email: string;
    subject: string;
    message?: string;
    status: 'new' | 'open' | 'in_progress' | 'resolved' | 'closed' | 'archived';
    created_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedInquiries {
    data: Inquiry[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
}

const props = defineProps<{
    inquiries: PaginatedInquiries;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Inquiries',
                href: inquiriesIndex(),
            },
        ],
    },
});

const statusColors = {
    new: 'bg-blue-100 text-blue-800',
    open: 'bg-amber-100 text-amber-800',
    in_progress: 'bg-amber-100 text-amber-800',
    resolved: 'bg-green-100 text-green-800',
    closed: 'bg-green-100 text-green-800',
    archived: 'bg-gray-100 text-gray-800',
};

function formatDate(dateStr: string): string {
    const date = new Date(dateStr);

    return new Intl.DateTimeFormat(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
}

function paginationLabel(label: string): string {
    return label.replace('&laquo;', 'Previous').replace('&raquo;', 'Next');
}

function deleteInquiry(inquiry: Inquiry): void {
    if (
        !confirm(`Delete inquiry from ${inquiry.name}? This cannot be undone.`)
    ) {
        return;
    }

    router.delete(inquiriesDestroy.url(inquiry.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Inquiries" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Inquiries"
            description="View and manage incoming inquiries."
        />

        <div
            v-if="props.inquiries.data.length > 0"
            class="border-border bg-background overflow-hidden rounded-lg border"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] table-auto">
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
                                Subject
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Status
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Submitted
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
                            v-for="inquiry in props.inquiries.data"
                            :key="inquiry.id"
                            class="hover:bg-muted/40 transition-colors"
                        >
                            <td class="px-4 py-4">
                                <div class="flex flex-col gap-1">
                                    <p
                                        class="text-foreground text-sm font-medium"
                                    >
                                        {{ inquiry.name }}
                                    </p>
                                    <p class="text-muted-foreground text-xs">
                                        {{ inquiry.email }}
                                    </p>
                                </div>
                            </td>
                            <td
                                class="text-foreground max-w-xs px-4 py-4 text-sm sm:max-w-md"
                            >
                                <Link
                                    :href="inquiriesShow(inquiry.id)"
                                    class="text-foreground block truncate font-semibold transition-colors hover:text-purple-600 dark:hover:text-purple-400"
                                >
                                    {{ inquiry.subject }}
                                </Link>
                                <p
                                    v-if="inquiry.message"
                                    class="text-muted-foreground mt-0.5 line-clamp-1 truncate text-xs"
                                >
                                    {{ inquiry.message }}
                                </p>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <span
                                    :class="[
                                        statusColors[inquiry.status],
                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                    ]"
                                >
                                    {{
                                        inquiry.status
                                            .split('_')
                                            .map(
                                                (word) =>
                                                    word
                                                        .charAt(0)
                                                        .toUpperCase() +
                                                    word.slice(1),
                                            )
                                            .join(' ')
                                    }}
                                </span>
                            </td>
                            <td
                                class="text-muted-foreground whitespace-nowrap px-4 py-4 text-sm"
                            >
                                {{ formatDate(inquiry.created_at) }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-1.5">
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        as-child
                                        class="h-8 px-2.5 text-xs"
                                    >
                                        <Link :href="inquiriesShow(inquiry.id)">
                                            View
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        as-child
                                        class="h-8 px-2.5 text-xs"
                                    >
                                        <Link :href="inquiriesEdit(inquiry.id)">
                                            Edit
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive h-8 px-2 text-xs"
                                        @click="deleteInquiry(inquiry)"
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
                    Showing {{ props.inquiries.from }} to
                    {{ props.inquiries.to }} of {{ props.inquiries.total }}
                    entries
                </p>

                <div class="flex flex-wrap gap-1">
                    <template
                        v-for="link in props.inquiries.links"
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
            title="No inquiries yet"
            description="Inquiries from your contact forms will appear here."
        />
    </div>
</template>
