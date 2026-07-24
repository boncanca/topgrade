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
    if (!confirm(`Delete inquiry from ${inquiry.name}? This cannot be undone.`)) {
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
            class="overflow-hidden rounded-lg border border-border bg-background"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] table-auto">
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
                                Subject
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Status
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Submitted
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
                            v-for="inquiry in props.inquiries.data"
                            :key="inquiry.id"
                            class="transition-colors hover:bg-muted/40"
                        >
                            <td class="px-4 py-4">
                                <div class="flex flex-col gap-1">
                                    <p class="text-sm font-medium text-foreground">
                                        {{ inquiry.name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ inquiry.email }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-foreground">
                                {{ inquiry.subject }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span
                                    :class="[
                                        statusColors[inquiry.status],
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    ]"
                                >
                                    {{
                                        inquiry.status
                                            .split('_')
                                            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                                            .join(' ')
                                    }}
                                </span>
                            </td>
                            <td
                                class="px-4 py-4 text-sm whitespace-nowrap text-muted-foreground"
                            >
                                {{ formatDate(inquiry.created_at) }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="inquiriesEdit(inquiry.id)">
                                            View
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive"
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
                class="flex flex-col gap-4 border-t border-border px-4 py-3 text-sm text-muted-foreground sm:flex-row sm:items-center sm:justify-between"
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
