<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import EmptyState from '@/components/EmptyState.vue';
import PageHeader from '@/components/PageHeader.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import {
    create as contentCreate,
    destroy as contentDestroy,
    edit as contentEdit,
    index as contentIndex,
    show as contentShow,
} from '@/routes/content';

type ContentStatus = 'draft' | 'published' | 'archived';

interface ContentType {
    id: number;
    name: string;
}

interface ContentItem {
    id: number;
    title: string;
    slug: string;
    status: ContentStatus;
    published_at: string | null;
    content_type: ContentType | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedContent {
    data: ContentItem[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
}

const props = defineProps<{
    content: PaginatedContent;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Content',
                href: contentIndex(),
            },
        ],
    },
});

function formattedDate(value: string | null): string {
    if (!value) {
        return 'Not published';
    }

    return new Intl.DateTimeFormat(undefined, {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    }).format(new Date(value));
}

function paginationLabel(label: string): string {
    return label.replace('&laquo;', 'Previous').replace('&raquo;', 'Next');
}

function deleteContent(item: ContentItem): void {
    if (!confirm(`Delete "${item.title}"? This cannot be undone.`)) {
        return;
    }

    router.delete(contentDestroy.url(item.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Content" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Content"
            description="Manage pages, articles, and reusable site content."
        >
            <template #actions>
                <Button as-child>
                    <Link :href="contentCreate()">Create Content</Link>
                </Button>
            </template>
        </PageHeader>

        <div
            v-if="props.content.data.length > 0"
            class="overflow-hidden rounded-lg border border-border bg-background"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] table-auto">
                    <thead class="border-b border-border bg-muted/40">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Title
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Type
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Status
                            </th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium tracking-wide text-muted-foreground uppercase"
                            >
                                Published
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
                            v-for="item in props.content.data"
                            :key="item.id"
                            class="transition-colors hover:bg-muted/40"
                        >
                            <td class="px-4 py-4">
                                <div class="flex min-w-0 flex-col gap-1">
                                    <Link
                                        :href="contentShow(item.id)"
                                        class="truncate text-sm font-medium text-foreground underline-offset-4 hover:underline"
                                    >
                                        {{ item.title }}
                                    </Link>
                                    <span
                                        class="truncate text-xs text-muted-foreground"
                                    >
                                        /{{ item.slug }}
                                    </span>
                                </div>
                            </td>
                            <td
                                class="px-4 py-4 text-sm whitespace-nowrap text-muted-foreground"
                            >
                                {{ item.content_type?.name ?? 'Ungrouped' }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <StatusBadge :status="item.status" />
                            </td>
                            <td
                                class="px-4 py-4 text-sm whitespace-nowrap text-muted-foreground"
                            >
                                {{ formattedDate(item.published_at) }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="contentEdit(item.id)">
                                            Edit
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive"
                                        @click="deleteContent(item)"
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
                    Showing {{ props.content.from }} to
                    {{ props.content.to }} of {{ props.content.total }}
                    entries
                </p>

                <div class="flex flex-wrap gap-1">
                    <template
                        v-for="link in props.content.links"
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
            title="No content yet"
            description="Create your first page, article, or reusable content block."
        >
            <Button as-child>
                <Link :href="contentCreate()">Create Content</Link>
            </Button>
        </EmptyState>
    </div>
</template>
