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
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import { show as contactShow } from '@/routes/contacts';
import { index as inquiriesIndex, edit as inquiriesEdit, update as inquiriesUpdate } from '@/routes/inquiries';

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

const statusConfig: Record<string, { label: string; badgeClass: string; icon: any }> = {
    new: {
        label: 'New Inquiry',
        badgeClass: 'bg-blue-500/10 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-800',
        icon: AlertCircle,
    },
    open: {
        label: 'Open',
        badgeClass: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-800',
        icon: Clock,
    },
    in_progress: {
        label: 'In Progress',
        badgeClass: 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border-purple-200 dark:border-purple-800',
        icon: MessageSquare,
    },
    resolved: {
        label: 'Resolved',
        badgeClass: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
        icon: CheckCircle2,
    },
    closed: {
        label: 'Closed',
        badgeClass: 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-800',
        icon: ShieldCheck,
    },
    archived: {
        label: 'Archived',
        badgeClass: 'bg-gray-500/10 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-800',
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
        }
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
        .map(part => part.charAt(0))
        .join('')
        .toUpperCase()
        .slice(0, 2);
}
</script>

<template>
    <Head :title="`Inquiry #${inquiry.id} - ${inquiry.subject}`" />

    <div class="flex flex-1 flex-col gap-6 p-4 md:p-6 max-w-7xl mx-auto w-full">
        <!-- Header -->
        <PageHeader
            :title="`Inquiry #${inquiry.id}`"
            :description="inquiry.subject"
        >
            <template #actions>
                <div class="flex items-center gap-3">
                    <Button as-child variant="outline" size="sm">
                        <a :href="`mailto:${inquiry.email}?subject=Re: ${encodeURIComponent(inquiry.subject)}`" class="flex items-center gap-2">
                            <Send class="w-4 h-4 text-purple-600" />
                            <span>Reply via Email</span>
                        </a>
                    </Button>
                    <Button as-child variant="default" size="sm">
                        <Link :href="inquiriesEdit(inquiry.id)" class="flex items-center gap-2">
                            <Edit3 class="w-4 h-4" />
                            <span>Edit Status</span>
                        </Link>
                    </Button>
                </div>
            </template>
        </PageHeader>

        <!-- Status Bar -->
        <Card class="bg-card border-border shadow-xs">
            <CardContent class="p-4 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-muted-foreground">Current Status:</span>
                    <Badge
                        variant="outline"
                        :class="[
                            statusConfig[inquiry.status]?.badgeClass ?? 'bg-slate-100 text-slate-800',
                            'px-3 py-1 text-xs font-semibold rounded-full flex items-center gap-1.5 border'
                        ]"
                    >
                        <component
                            :is="statusConfig[inquiry.status]?.icon ?? AlertCircle"
                            class="w-3.5 h-3.5"
                        />
                        {{ statusConfig[inquiry.status]?.label ?? inquiry.status }}
                    </Badge>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs text-muted-foreground mr-1">Mark as:</span>
                    <Button
                        v-for="(config, statusKey) in statusConfig"
                        :key="statusKey"
                        type="button"
                        size="sm"
                        :variant="inquiry.status === statusKey ? 'default' : 'outline'"
                        :disabled="processing"
                        class="text-xs h-7 px-2.5 transition-all"
                        @click="updateStatus(statusKey)"
                    >
                        {{ config.label }}
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Main Content Layout (Expanded and Properly Aligned) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <!-- Expanded Main Message Card (2 Cols on Desktop, Full Width) -->
            <div class="lg:col-span-2 space-y-6">
                <Card class="shadow-md border-border overflow-hidden">
                    <CardHeader class="border-b border-border bg-muted/40 p-6 sm:p-8">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <!-- Sender Information -->
                            <div class="flex items-center gap-4">
                                <Avatar class="h-14 w-14 border border-purple-300 dark:border-purple-700 bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 font-bold text-lg shrink-0 shadow-xs">
                                    <AvatarFallback>{{ getInitials(inquiry.name) }}</AvatarFallback>
                                </Avatar>
                                <div>
                                    <h3 class="font-bold text-foreground text-lg sm:text-xl flex items-center gap-2">
                                        {{ inquiry.name }}
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-muted-foreground mt-1">
                                        <a :href="`mailto:${inquiry.email}`" class="hover:text-purple-600 dark:hover:text-purple-400 font-medium transition-colors underline-offset-4 hover:underline">
                                            {{ inquiry.email }}
                                        </a>
                                        <span v-if="inquiry.phone" class="text-border">•</span>
                                        <a v-if="inquiry.phone" :href="`tel:${inquiry.phone}`" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                                            {{ inquiry.phone }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Fast Action Buttons -->
                            <div class="flex items-center gap-2 shrink-0 self-start sm:self-auto">
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="outline"
                                    @click="copyMessage"
                                    class="text-xs h-9 px-3.5 gap-1.5 cursor-pointer shadow-xs"
                                >
                                    <CheckCircle2 v-if="copied" class="w-4 h-4 text-emerald-500" />
                                    <Copy v-else class="w-4 h-4 text-muted-foreground" />
                                    <span>{{ copied ? 'Copied Text' : 'Copy Message' }}</span>
                                </Button>
                                <Button as-child size="sm" variant="default" class="text-xs h-9 px-3.5 gap-1.5 shadow-xs">
                                    <a :href="`mailto:${inquiry.email}?subject=Re: ${encodeURIComponent(inquiry.subject)}`">
                                        <Send class="w-4 h-4" />
                                        <span>Reply</span>
                                    </a>
                                </Button>
                            </div>
                        </div>
                    </CardHeader>

                    <!-- Expanded Message Content Body -->
                    <CardContent class="p-6 sm:p-8 md:p-10 space-y-6">
                        <!-- Subject -->
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground/80 block mb-1">
                                Enquiry Subject
                            </span>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-foreground tracking-tight leading-snug">
                                {{ inquiry.subject }}
                            </h2>
                        </div>

                        <Separator class="my-2" />

                        <!-- Full Wide Message Text -->
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground/80">
                                    Message Body
                                </span>
                                <span class="text-xs text-muted-foreground flex items-center gap-1.5">
                                    <Clock class="w-3.5 h-3.5 text-muted-foreground" />
                                    Received {{ formatDate(inquiry.created_at) }}
                                </span>
                            </div>

                            <div class="w-full rounded-xl bg-slate-50/80 dark:bg-slate-900/60 p-6 sm:p-8 md:p-10 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-slate-100 text-base sm:text-lg leading-relaxed sm:leading-8 whitespace-pre-wrap font-sans select-text shadow-inner">
                                {{ inquiry.message }}
                            </div>
                        </div>
                    </CardContent>

                    <CardFooter class="border-t border-border bg-muted/20 p-4 sm:p-6 flex flex-wrap justify-between items-center gap-3">
                        <span class="text-xs text-muted-foreground">
                            Enquiry submitted through the public website contact portal.
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-muted-foreground mr-1">Status:</span>
                            <Badge
                                variant="outline"
                                :class="[
                                    statusConfig[inquiry.status]?.badgeClass ?? 'bg-slate-100 text-slate-800',
                                    'px-2.5 py-0.5 text-xs font-semibold rounded-full'
                                ]"
                            >
                                {{ statusConfig[inquiry.status]?.label ?? inquiry.status }}
                            </Badge>
                        </div>
                    </CardFooter>
                </Card>
            </div>

            <!-- Right Sidebar Panel (CRM & System Audit) -->
            <div class="space-y-6">
                <!-- Linked CRM Contact Card -->
                <Card v-if="inquiry.contact" class="shadow-sm border-purple-200/80 dark:border-purple-900/60 bg-purple-50/30 dark:bg-purple-950/10">
                    <CardHeader class="pb-3 border-b border-purple-100 dark:border-purple-900/40">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-purple-700 dark:text-purple-300 flex items-center gap-1.5">
                                <ShieldCheck class="w-4 h-4 text-purple-600 dark:text-purple-400" />
                                Linked CRM Contact
                            </span>
                        </div>
                    </CardHeader>
                    <CardContent class="pt-4 space-y-3">
                        <div>
                            <p class="font-bold text-base text-foreground">
                                {{ inquiry.contact.first_name }} {{ inquiry.contact.last_name }}
                            </p>
                            <p class="text-sm text-muted-foreground mt-0.5">
                                {{ inquiry.contact.email }}
                            </p>
                            <p v-if="inquiry.contact.phone" class="text-sm text-muted-foreground">
                                {{ inquiry.contact.phone }}
                            </p>
                        </div>
                        <Button as-child variant="outline" size="sm" class="w-full bg-background border-purple-200 hover:border-purple-400 text-purple-700 dark:text-purple-300 mt-2">
                            <Link :href="contactShow(inquiry.contact.id)" class="flex items-center justify-center gap-1.5">
                                <span>View CRM Profile</span>
                                <ExternalLink class="w-3.5 h-3.5" />
                            </Link>
                        </Button>
                    </CardContent>
                </Card>

                <!-- Sender Fast Details -->
                <Card class="shadow-xs border-border">
                    <CardHeader class="pb-3 border-b border-border bg-muted/20">
                        <div class="flex items-center gap-2">
                            <User class="w-4 h-4 text-muted-foreground" />
                            <CardTitle class="text-xs font-bold uppercase tracking-wider text-muted-foreground">
                                Sender Record
                            </CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent class="pt-4 space-y-3 text-sm">
                        <div class="flex justify-between items-start gap-2">
                            <span class="text-xs text-muted-foreground">Name:</span>
                            <span class="font-medium text-foreground text-right">{{ inquiry.name }}</span>
                        </div>
                        <div class="flex justify-between items-start gap-2">
                            <span class="text-xs text-muted-foreground">Email:</span>
                            <span class="font-medium text-foreground text-right truncate max-w-[180px]">{{ inquiry.email }}</span>
                        </div>
                        <div v-if="inquiry.phone" class="flex justify-between items-start gap-2">
                            <span class="text-xs text-muted-foreground">Phone:</span>
                            <span class="font-medium text-foreground text-right">{{ inquiry.phone }}</span>
                        </div>
                    </CardContent>
                </Card>

                <!-- System Metadata Card -->
                <Card class="shadow-xs border-border">
                    <CardHeader class="pb-3 border-b border-border bg-muted/20">
                        <CardTitle class="text-xs font-bold uppercase tracking-wider text-muted-foreground">
                            System & Security Audit
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="pt-4 space-y-2.5 text-xs text-muted-foreground">
                        <div class="flex justify-between">
                            <span>Inquiry Ref:</span>
                            <span class="font-mono font-medium text-foreground">#{{ inquiry.id }}</span>
                        </div>
                        <div v-if="inquiry.ip_address" class="flex justify-between">
                            <span>IP Address:</span>
                            <span class="font-mono text-foreground">{{ inquiry.ip_address }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Submitted At:</span>
                            <span class="text-foreground">{{ formatDate(inquiry.created_at) }}</span>
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
                <ArrowLeft class="w-4 h-4" />
                <span>Back to Inquiries</span>
            </Button>
        </PageActions>
    </div>
</template>

