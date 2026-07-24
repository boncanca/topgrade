<script setup lang="ts">
import { Link, router, useForm } from '@inertiajs/vue3';
import FormSection from '@/components/FormSection.vue';
import PageHeader from '@/components/PageHeader.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';
import { show as contactShow } from '@/routes/contacts';
import { index as inquiriesIndex, update as inquiriesUpdate } from '@/routes/inquiries';

interface Contact {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    phone: string | null;
}

interface Inquiry {
    id: number;
    name: string;
    email: string;
    phone: string | null;
    subject: string;
    message: string;
    status: 'new' | 'open' | 'in_progress' | 'resolved' | 'closed' | 'archived';
    created_at: string;
    contact: Contact | null;
}

const props = defineProps<{
    inquiry: Inquiry;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Inquiries',
                href: inquiriesIndex(),
            },
            {
                title: 'View Inquiry',
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

const form = useForm<{ status: 'new' | 'open' | 'in_progress' | 'resolved' | 'closed' | 'archived' }>({
    status: props.inquiry.status,
});

function updateStatus(newStatus: 'new' | 'open' | 'in_progress' | 'resolved' | 'closed' | 'archived'): void {
    form.status = newStatus;
    form.submit(inquiriesUpdate(props.inquiry.id));
}

function handleCancel(): void {
    router.visit(inquiriesIndex());
}

function formatDate(dateStr: string): string {
    const date = new Date(dateStr);
    return new Intl.DateTimeFormat(undefined, {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
}
</script>

<template>
    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6">
        <PageHeader
            :title="`Inquiry from ${inquiry.name}`"
            :description="inquiry.subject"
        />

        <div class="space-y-8">
            <FormSection title="Status">
                <div class="flex items-center gap-4">
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
                    <p class="text-xs text-muted-foreground">
                        Received: {{ formatDate(inquiry.created_at) }}
                    </p>
                </div>
            </FormSection>

            <FormSection
                title="Sender Information"
                description="Contact details from the inquiry."
            >
                <div class="space-y-4">
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
                            Name
                        </label>
                        <p class="text-sm text-foreground">
                            {{ inquiry.name }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
                            Email
                        </label>
                        <p class="text-sm text-foreground">
                            {{ inquiry.email }}
                        </p>
                    </div>

                    <div v-if="inquiry.phone">
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
                            Phone
                        </label>
                        <p class="text-sm text-foreground">
                            {{ inquiry.phone }}
                        </p>
                    </div>
                </div>
            </FormSection>

            <FormSection
                v-if="inquiry.contact"
                title="Linked Contact"
                description="This inquiry is linked to a contact in your system."
            >
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium">
                            {{ inquiry.contact.first_name }} {{ inquiry.contact.last_name }}
                        </p>
                        <p class="text-sm text-muted-foreground">
                            {{ inquiry.contact.email }}
                        </p>
                    </div>
                    <Button as-child variant="outline" size="sm">
                        <Link :href="contactShow(inquiry.contact.id)">
                            View Contact
                        </Link>
                    </Button>
                </div>
            </FormSection>

            <FormSection
                title="Message"
                description="The full inquiry message."
            >
                <div class="space-y-4">
                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
                            Subject
                        </label>
                        <p class="text-sm text-foreground">
                            {{ inquiry.subject }}
                        </p>
                    </div>

                    <div>
                        <label
                            class="mb-2 block text-sm font-medium text-foreground"
                        >
                            Message
                        </label>
                        <p class="whitespace-pre-wrap text-sm text-foreground">
                            {{ inquiry.message }}
                        </p>
                    </div>
                </div>
            </FormSection>
        </div>

        <PageActions>
            <div class="flex flex-wrap gap-2">
                <Button
                    v-if="inquiry.status !== 'open'"
                    type="button"
                    variant="outline"
                    @click="updateStatus('open')"
                    :disabled="form.processing"
                >
                    Mark as Open
                </Button>
                <Button
                    v-if="inquiry.status !== 'in_progress'"
                    type="button"
                    variant="outline"
                    @click="updateStatus('in_progress')"
                    :disabled="form.processing"
                >
                    In Progress
                </Button>
                <Button
                    v-if="inquiry.status !== 'resolved'"
                    type="button"
                    variant="outline"
                    @click="updateStatus('resolved')"
                    :disabled="form.processing"
                >
                    Resolved
                </Button>
                <Button
                    v-if="inquiry.status !== 'archived'"
                    type="button"
                    variant="outline"
                    @click="updateStatus('archived')"
                    :disabled="form.processing"
                >
                    Archive
                </Button>
            </div>
            <Button
                type="button"
                variant="outline"
                @click="handleCancel"
            >
                Back
            </Button>
        </PageActions>
    </div>
</template>
