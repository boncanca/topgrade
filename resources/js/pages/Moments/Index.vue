<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import EmptyState from '@/components/EmptyState.vue';
import PageHeader from '@/components/PageHeader.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Eye, Plus, Sparkles, Trash2, Edit3, Image } from '@lucide/vue';

interface MomentItem {
    id: number;
    title: string;
    slug: string;
    status: 'draft' | 'published';
    published_at: string | null;
    featured: boolean;
    sort_order: number;
    images_count: number;
    cover_url: string | null;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedMoments {
    data: MomentItem[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
}

defineProps<{
    moments: PaginatedMoments;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Moments',
                href: '/dashboard/moments',
            },
        ],
    },
});

function handleDelete(moment: MomentItem): void {
    if (!confirm(`Are you sure you want to delete the moment "${moment.title}" and its gallery images?`)) {
        return;
    }

    router.delete(`/dashboard/moments/${moment.id}`);
}

function formatDate(dateString: string | null): string {
    if (!dateString) return '—';
    return new Date(dateString).toLocaleDateString('en-GB', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}
</script>

<template>
    <Head title="Moments" />

    <div class="space-y-6 p-6">
        <PageHeader
            title="Moments"
            description="Manage visual stories, matchday photo sets, and training galleries."
        >
            <template #actions>
                <Button as-child>
                    <Link href="/dashboard/moments/create">
                        <Plus class="mr-2 h-4 w-4" />
                        Add Moment
                    </Link>
                </Button>
            </template>
        </PageHeader>

        <!-- Empty State -->
        <EmptyState
            v-if="moments.data.length === 0"
            title="No moments yet"
            description="Create your first gallery to showcase matchday action, squad training, and club life."
        >
            <template #action>
                <Button as-child>
                    <Link href="/dashboard/moments/create">
                        <Plus class="mr-2 h-4 w-4" />
                        Create Moment
                    </Link>
                </Button>
            </template>
        </EmptyState>

        <!-- Table -->
        <div v-else class="overflow-hidden rounded-lg border border-border bg-card">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-border bg-muted/50 text-xs uppercase tracking-wider text-muted-foreground">
                        <tr>
                            <th class="w-20 px-4 py-3">Cover</th>
                            <th class="px-4 py-3">Moment</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Images</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="w-32 px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        <tr
                            v-for="item in moments.data"
                            :key="item.id"
                            class="hover:bg-muted/30 transition-colors"
                        >
                            <!-- Cover Thumbnail -->
                            <td class="px-4 py-3">
                                <div class="h-14 w-14 overflow-hidden rounded border border-border bg-muted flex items-center justify-center">
                                    <img
                                        v-if="item.cover_url"
                                        :src="item.cover_url"
                                        :alt="item.title"
                                        class="h-full w-full object-cover"
                                    />
                                    <Image v-else class="h-6 w-6 text-muted-foreground/50" />
                                </div>
                            </td>

                            <!-- Title & Slug -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <Link
                                        :href="`/dashboard/moments/${item.id}/edit`"
                                        class="font-medium text-foreground hover:underline"
                                    >
                                        {{ item.title }}
                                    </Link>
                                    <span
                                        v-if="item.featured"
                                        class="inline-flex items-center gap-1 rounded bg-amber-500/10 px-2 py-0.5 text-[11px] font-semibold text-amber-500 border border-amber-500/20"
                                    >
                                        <Sparkles class="h-3 w-3" />
                                        Featured
                                    </span>
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    /moments/{{ item.slug }}
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3">
                                <StatusBadge :status="item.status" />
                            </td>

                            <!-- Images Count -->
                            <td class="px-4 py-3 text-muted-foreground">
                                <span class="font-medium text-foreground">{{ item.images_count }}</span>
                                <span class="text-xs ml-1">{{ item.images_count === 1 ? 'photo' : 'photos' }}</span>
                            </td>

                            <!-- Published At -->
                            <td class="px-4 py-3 text-muted-foreground">
                                {{ formatDate(item.published_at) }}
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a
                                        v-if="item.status === 'published'"
                                        :href="`/moments/${item.slug}`"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="rounded p-1.5 text-muted-foreground hover:bg-accent hover:text-foreground"
                                        title="View Public Gallery"
                                    >
                                        <Eye class="h-4 w-4" />
                                    </a>
                                    <Link
                                        :href="`/dashboard/moments/${item.id}/edit`"
                                        class="rounded p-1.5 text-muted-foreground hover:bg-accent hover:text-foreground"
                                        title="Edit Gallery"
                                    >
                                        <Edit3 class="h-4 w-4" />
                                    </Link>
                                    <button
                                        type="button"
                                        @click="handleDelete(item)"
                                        class="rounded p-1.5 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                        title="Delete Moment"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div
                v-if="moments.links && moments.links.length > 3"
                class="flex items-center justify-between border-t border-border px-4 py-3 text-sm"
            >
                <div class="text-muted-foreground">
                    Showing {{ moments.from }} to {{ moments.to }} of {{ moments.total }} moments
                </div>
                <div class="flex gap-1">
                    <template v-for="(link, i) in moments.links" :key="i">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="rounded px-3 py-1 text-xs font-medium transition-colors"
                            :class="link.active ? 'bg-primary text-primary-foreground' : 'hover:bg-muted text-muted-foreground'"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="px-2 py-1 text-xs text-muted-foreground/40"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
