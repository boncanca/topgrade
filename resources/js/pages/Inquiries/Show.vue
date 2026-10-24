<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    AlertCircle,
    Archive,
    ArrowLeft,
    CheckCircle2,
    Clock,
    Copy,
    Edit3,
    ExternalLink,
    MessageSquare,
    Send,
    ShieldCheck,
    User,
} from '@lucide/vue';
import { ref } from 'vue';
import PageActions from '@/components/PageActions.vue';
import PageHeader from '@/components/PageHeader.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { show as contactShow } from '@/routes/contacts';
import {
    index as inquiriesIndex,
    edit as inquiriesEdit,
    update as inquiriesUpdate,
} from '@/routes/inquiries';

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
    ip_address?: string | null;
    user_agent?: string | null;
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
                title: 'Inquiry Details',
            },
        ],
    },
});

const processing = ref(false);
const copied = ref(false);

async function copyMessage(): Promise<void> {
    if (navigator?.clipboard) {
        await navigator.clipboard.writeText(props.inquiry.message);
        copied.value = true;
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    }
}

const statusConfig: Record<
    string,
    { label: string; badgeClass: string; icon: any }
> = {
    new: {
        label: 'New Inquiry',
        badgeClass:
            'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800',
        icon: AlertCircle,
    },
    open: {
        label: 'Open',
        badgeClass:
            'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800',
        icon: Clock,
    },
    in_progress: {
        label: 'In Progress',
        badgeClass:
            'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800',
        icon: MessageSquare,
    },
    resolved: {
        label: 'Resolved',
        badgeClass:
            'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
        icon: CheckCircle2,
    },
    closed: {
        label: 'Closed',
        badgeClass:
            'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-800',
        icon: ShieldCheck,
    },
    archived: {
        label: 'Archived',
        badgeClass:
            'bg-gray-500/10 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-800',
        icon: Archive,
    },
};

function updateStatus(newStatus: string): void {
    if (processing.value || props.inquiry.status === newStatus) {
        return;
    }

    processing.value = true;
    router.put(
        inquiriesUpdate.url(props.inquiry.id),
        { status: newStatus },
        {
            preserveScroll: true,
            onFinish: () => {
                processing.value = false;
            },
        },
    );
}

function handleBack(): void {
    router.visit(inquiriesIndex());
}

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

function getInitials(name: string): string {
    return name
        .split(' ')
        .map((part) => part.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2);
}
</script>

<template>
    <Head :title="`Inquiry #${inquiry.id} - ${inquiry.subject}`" />

    <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Header -->
        <PageHeader
            :title="`Inquiry #${inquiry.id}`"
            :description="inquiry.subject"
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <Button as-child variant="outline" size="sm">
                        <a
                            :href="`mailto:${inquiry.email}?subject=Re: ${encodeURIComponent(inquiry.subject)}`"
                            class="flex items-center gap-2"
                        >
                            <Send class="h-4 w-4 text-purple-600" />
                            <span>Reply via Email</span>
                        </a>
                    </Button>
                    <Button as-child variant="default" size="sm">
                        <Link
                            :href="inquiriesEdit(inquiry.id)"
                            class="flex items-center gap-2"
                        >
                            <Edit3 class="h-4 w-4" />
                            <span>Edit Status</span>
                        </Link>
                    </Button>
                </div>
            </template>
        </PageHeader>

        <!-- Status Bar -->
        <Card class="bg-card border-border shadow-xs">
            <CardContent
                class="flex flex-wrap items-center justify-between gap-4 p-4"
            >
                <div class="flex items-center gap-3">
                    <span class="text-muted-foreground text-sm font-medium"
                        >Current Status:</span
                    >
                    <Badge
                        variant="outline"
                        :class="[
                            statusConfig[inquiry.status]?.badgeClass ??
                                'bg-slate-100 text-slate-800',
                            'flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold',
                        ]"
                    >
                        <component
                            :is="
                                statusConfig[inquiry.status]?.icon ??
                                AlertCircle
                            "
                            class="h-3.5 w-3.5"
                        />
                        {{
                            statusConfig[inquiry.status]?.label ??
                            inquiry.status
                        }}
                    </Badge>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-muted-foreground mr-1 text-xs"
                        >Mark as:</span
                    >
                    <Button
                        v-for="(config, statusKey) in statusConfig"
                        :key="statusKey"
                        type="button"
                        size="sm"
                        :variant="
                            inquiry.status === statusKey ? 'default' : 'outline'
                        "
                        :disabled="processing"
                        class="h-7 px-2.5 text-xs transition-all"
                        @click="updateStatus(statusKey)"
                    >
                        {{ config.label }}
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Main Content Layout (Expanded and Properly Aligned) -->
        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">
            <!-- Expanded Main Message Card (2 Cols on Desktop, Full Width) -->
            <div class="space-y-6 lg:col-span-2">
                <Card class="border-border overflow-hidden shadow-md">
                    <CardHeader
                        class="border-border bg-muted/40 border-b p-6 sm:p-8"
                    >
                        <div
                            class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center"
                        >
                            <!-- Sender Information -->
                            <div class="flex items-center gap-4">
                                <Avatar
                                    class="shadow-xs h-14 w-14 shrink-0 border border-purple-300 bg-purple-100 text-lg font-bold text-purple-700 dark:border-purple-700 dark:bg-purple-950 dark:text-purple-300"
                                >
                                    <AvatarFallback>{{
                                        getInitials(inquiry.name)
                                    }}</AvatarFallback>
                                </Avatar>
                                <div>
                                    <h3
                                        class="text-foreground flex items-center gap-2 text-lg font-bold sm:text-xl"
                                    >
                                        {{ inquiry.name }}
                                    </h3>
                                    <div
                                        class="text-muted-foreground mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-sm"
                                    >
                                        <a
                                            :href="`mailto:${inquiry.email}`"
                                            class="font-medium underline-offset-4 transition-colors hover:text-purple-600 hover:underline dark:hover:text-purple-400"
                                        >
                                            {{ inquiry.email }}
                                        </a>
                                        <span
                                            v-if="inquiry.phone"
                                            class="text-border"
                                            >•</span
                                        >
                                        <a
                                            v-if="inquiry.phone"
                                            :href="`tel:${inquiry.phone}`"
                                            class="transition-colors hover:text-purple-600 dark:hover:text-purple-400"
                                        >
                                            {{ inquiry.phone }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Fast Action Buttons -->
                            <div
                                class="flex shrink-0 items-center gap-2 self-start sm:self-auto"
                            >
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="outline"
                                    @click="copyMessage"
                                    class="shadow-xs h-9 cursor-pointer gap-1.5 px-3.5 text-xs"
                                >
                                    <CheckCircle2
                                        v-if="copied"
                                        class="h-4 w-4 text-emerald-500"
                                    />
                                    <Copy
                                        v-else
                                        class="text-muted-foreground h-4 w-4"
                                    />
                                    <span>{{
                                        copied ? 'Copied Text' : 'Copy Message'
                                    }}</span>
                                </Button>
                                <Button
                                    as-child
                                    size="sm"
                                    variant="default"
                                    class="shadow-xs h-9 gap-1.5 px-3.5 text-xs"
                                >
                                    <a
                                        :href="`mailto:${inquiry.email}?subject=Re: ${encodeURIComponent(inquiry.subject)}`"
                                    >
                                        <Send class="h-4 w-4" />
                                        <span>Reply</span>
                                    </a>
                                </Button>
                            </div>
                        </div>
                    </CardHeader>

                    <!-- Expanded Message Content Body -->
                    <CardContent class="space-y-6 p-6 sm:p-8 md:p-10">
                        <!-- Subject -->
                        <div>
                            <span
                                class="text-muted-foreground/80 mb-1 block text-xs font-bold uppercase tracking-wider"
                            >
                                Enquiry Subject
                            </span>
                            <h2
                                class="text-foreground text-2xl font-extrabold leading-snug tracking-tight sm:text-3xl"
                            >
                                {{ inquiry.subject }}
                            </h2>
                        </div>

                        <Separator class="my-2" />

                        <!-- Full Wide Message Text -->
                        <div>
                            <div class="mb-3 flex items-center justify-between">
                                <span
                                    class="text-muted-foreground/80 text-xs font-bold uppercase tracking-wider"
                                >
                                    Message Body
                                </span>
                                <span
                                    class="text-muted-foreground flex items-center gap-1.5 text-xs"
                                >
                                    <Clock
                                        class="text-muted-foreground h-3.5 w-3.5"
                                    />
                                    Received
                                    {{ formatDate(inquiry.created_at) }}
                                </span>
                            </div>

                            <div
                                class="w-full select-text whitespace-pre-wrap rounded-xl border border-slate-200 bg-slate-50/80 p-6 font-sans text-base leading-relaxed text-slate-900 shadow-inner sm:p-8 sm:text-lg sm:leading-8 md:p-10 dark:border-slate-800 dark:bg-slate-900/60 dark:text-slate-100"
                            >
                                {{ inquiry.message }}
                            </div>
                        </div>
                    </CardContent>

                    <CardFooter
                        class="border-border bg-muted/20 flex flex-wrap items-center justify-between gap-3 border-t p-4 sm:p-6"
                    >
                        <span class="text-muted-foreground text-xs">
                            Enquiry submitted through the public website contact
                            portal.
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="text-muted-foreground mr-1 text-xs"
                                >Status:</span
                            >
                            <Badge
                                variant="outline"
                                :class="[
                                    statusConfig[inquiry.status]?.badgeClass ??
                                        'bg-slate-100 text-slate-800',
                                    'rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                ]"
                            >
                                {{
                                    statusConfig[inquiry.status]?.label ??
                                    inquiry.status
                                }}
                            </Badge>
                        </div>
                    </CardFooter>
                </Card>
            </div>

            <!-- Right Sidebar Panel (CRM & System Audit) -->
            <div class="space-y-6">
                <!-- Linked CRM Contact Card -->
                <Card
                    v-if="inquiry.contact"
                    class="border-purple-200/80 bg-purple-50/30 shadow-sm dark:border-purple-900/60 dark:bg-purple-950/10"
                >
                    <CardHeader
                        class="border-b border-purple-100 pb-3 dark:border-purple-900/40"
                    >
                        <div class="flex items-center justify-between">
                            <span
                                class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-purple-700 dark:text-purple-300"
                            >
                                <ShieldCheck
                                    class="h-4 w-4 text-purple-600 dark:text-purple-400"
                                />
                                Linked CRM Contact
                            </span>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-3 pt-4">
                        <div>
                            <p class="text-foreground text-base font-bold">
                                {{ inquiry.contact.first_name }}
                                {{ inquiry.contact.last_name }}
                            </p>
                            <p class="text-muted-foreground mt-0.5 text-sm">
                                {{ inquiry.contact.email }}
                            </p>
                            <p
                                v-if="inquiry.contact.phone"
                                class="text-muted-foreground text-sm"
                            >
                                {{ inquiry.contact.phone }}
                            </p>
                        </div>
                        <Button
                            as-child
                            variant="outline"
                            size="sm"
                            class="bg-background mt-2 w-full border-purple-200 text-purple-700 hover:border-purple-400 dark:text-purple-300"
                        >
                            <Link
                                :href="contactShow(inquiry.contact.id)"
                                class="flex items-center justify-center gap-1.5"
                            >
                                <span>View CRM Profile</span>
                                <ExternalLink class="h-3.5 w-3.5" />
                            </Link>
                        </Button>
                    </CardContent>
                </Card>

                <!-- Sender Fast Details -->
                <Card class="shadow-xs border-border">
                    <CardHeader class="border-border bg-muted/20 border-b pb-3">
                        <div class="flex items-center gap-2">
                            <User class="text-muted-foreground h-4 w-4" />
                            <CardTitle
                                class="text-muted-foreground text-xs font-bold uppercase tracking-wider"
                            >
                                Sender Record
                            </CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-3 pt-4 text-sm">
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-muted-foreground text-xs"
                                >Name:</span
                            >
                            <span
                                class="text-foreground text-right font-medium"
                                >{{ inquiry.name }}</span
                            >
                        </div>
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-muted-foreground text-xs"
                                >Email:</span
                            >
                            <span
                                class="text-foreground max-w-[180px] truncate text-right font-medium"
                                >{{ inquiry.email }}</span
                            >
                        </div>
                        <div
                            v-if="inquiry.phone"
                            class="flex items-start justify-between gap-2"
                        >
                            <span class="text-muted-foreground text-xs"
                                >Phone:</span
                            >
                            <span
                                class="text-foreground text-right font-medium"
                                >{{ inquiry.phone }}</span
                            >
                        </div>
                    </CardContent>
                </Card>

                <!-- System Metadata Card -->
                <Card class="shadow-xs border-border">
                    <CardHeader class="border-border bg-muted/20 border-b pb-3">
                        <CardTitle
                            class="text-muted-foreground text-xs font-bold uppercase tracking-wider"
                        >
                            System & Security Audit
                        </CardTitle>
                    </CardHeader>
                    <CardContent
                        class="text-muted-foreground space-y-2.5 pt-4 text-xs"
                    >
                        <div class="flex justify-between">
                            <span>Inquiry Ref:</span>
                            <span class="text-foreground font-mono font-medium"
                                >#{{ inquiry.id }}</span
                            >
                        </div>
                        <div
                            v-if="inquiry.ip_address"
                            class="flex justify-between"
                        >
                            <span>IP Address:</span>
                            <span class="text-foreground font-mono">{{
                                inquiry.ip_address
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Submitted At:</span>
                            <span class="text-foreground">{{
                                formatDate(inquiry.created_at)
                            }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Footer Actions -->
        <PageActions>
            <Button
                type="button"
                variant="outline"
                @click="handleBack"
                class="flex items-center gap-2"
            >
                <ArrowLeft class="h-4 w-4" />
                <span>Back to Inquiries</span>
            </Button>
        </PageActions>
    </div>
</template>
