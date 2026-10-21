<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import EmptyState from '@/components/EmptyState.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Button } from '@/components/ui/button';
import {
    create as contactsCreate,
    destroy as contactsDestroy,
    edit as contactsEdit,
    index as contactsIndex,
    show as contactsShow,
} from '@/routes/contacts';

interface Contact {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
    company: string | null;
    position: string | null;
    status: 'active' | 'archived';
    created_at: string;
}

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedContacts {
    data: Contact[];
    from: number | null;
    to: number | null;
    total: number;
    links: PaginationLink[];
}

const props = defineProps<{
    contacts: PaginatedContacts;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Contacts',
                href: contactsIndex(),
            },
        ],
    },
});

const statusColors = {
    active: 'bg-green-100 text-green-800',
    archived: 'bg-gray-100 text-gray-800',
};

function formatDate(dateStr: string): string {
    const date = new Date(dateStr);

    return new Intl.DateTimeFormat(undefined, {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    }).format(date);
}

function paginationLabel(label: string): string {
    return label.replace('&laquo;', 'Previous').replace('&raquo;', 'Next');
}

function deleteContact(contact: Contact): void {
    if (
        !confirm(
            `Delete ${contact.first_name} ${contact.last_name}? This cannot be undone.`,
        )
    ) {
        return;
    }

    router.delete(contactsDestroy.url(contact.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Contacts" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            title="Contacts"
            description="Manage your contact directory."
        >
            <template #actions>
                <Button as-child>
                    <Link :href="contactsCreate()">Add Contact</Link>
                </Button>
            </template>
        </PageHeader>

        <div
            v-if="props.contacts.data.length > 0"
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
                                Email
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Company
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Status
                            </th>
                            <th
                                class="text-muted-foreground px-4 py-3 text-left text-xs font-medium uppercase tracking-wide"
                            >
                                Added
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
                            v-for="contact in props.contacts.data"
                            :key="contact.id"
                            class="hover:bg-muted/40 transition-colors"
                        >
                            <td class="px-4 py-4">
                                <Link
                                    :href="contactsEdit(contact.id)"
                                    class="text-foreground text-sm font-medium underline-offset-4 hover:underline"
                                >
                                    {{ contact.first_name }}
                                    {{ contact.last_name }}
                                </Link>
                            </td>
                            <td class="text-muted-foreground px-4 py-4 text-sm">
                                {{ contact.email }}
                            </td>
                            <td class="text-muted-foreground px-4 py-4 text-sm">
                                <span v-if="contact.company && contact.position"
                                    >{{ contact.company }} ({{
                                        contact.position
                                    }})</span
                                >
                                <span v-else-if="contact.company">{{
                                    contact.company
                                }}</span>
                                <span v-else-if="contact.position">{{
                                    contact.position
                                }}</span>
                                <span v-else>—</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <span
                                    :class="[
                                        statusColors[contact.status],
                                        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                    ]"
                                >
                                    {{
                                        contact.status.charAt(0).toUpperCase() +
                                        contact.status.slice(1)
                                    }}
                                </span>
                            </td>
                            <td
                                class="text-muted-foreground whitespace-nowrap px-4 py-4 text-sm"
                            >
                                {{ formatDate(contact.created_at) }}
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="contactsShow(contact.id)">
                                            View
                                        </Link>
                                    </Button>
                                    <Button variant="ghost" size="sm" as-child>
                                        <Link :href="contactsEdit(contact.id)">
                                            Edit
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="text-destructive hover:text-destructive"
                                        @click="deleteContact(contact)"
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
                    Showing {{ props.contacts.from }} to
                    {{ props.contacts.to }} of {{ props.contacts.total }}
                    entries
                </p>

                <div class="flex flex-wrap gap-1">
                    <template
                        v-for="link in props.contacts.links"
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
            title="No contacts yet"
            description="Add contacts to your directory."
        >
            <Button as-child>
                <Link :href="contactsCreate()">Add Contact</Link>
            </Button>
        </EmptyState>
    </div>
</template>
