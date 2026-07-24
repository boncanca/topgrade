<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import PageHeader from '@/components/PageHeader.vue';
import PageActions from '@/components/PageActions.vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Separator } from '@/components/ui/separator';
import { show as contactShow } from '@/routes/contacts';
import { index as inquiriesIndex, edit as inquiriesEdit, update as inquiriesUpdate } from '@/routes/inquiries';
import {
    ArrowLeft,
    Mail,
    Phone,
    User,
    Calendar,
    Clock,
    CheckCircle2,
    AlertCircle,
    Edit3,
    ExternalLink,
    MessageSquare,
    ShieldCheck,
    Send,
    Archive,
    Inbox
} from '@lucide/vue';

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
                        size="xs"
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

        <!-- Main Content Layout (2-Column Grid) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Panel (Message & Message Details) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Inquiry Message Card -->
                <Card class="shadow-xs border-border">
                    <CardHeader class="border-b border-border bg-muted/30 pb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <MessageSquare class="w-5 h-5 text-purple-600" />
                                <CardTitle class="text-lg font-bold text-foreground">Message Content</CardTitle>
                            </div>
                            <span class="text-xs text-muted-foreground flex items-center gap-1">
                                <Clock class="w-3.5 h-3.5" />
                                {{ formatDate(inquiry.created_at) }}
                            </span>
                        </div>
                    </CardHeader>
                    <CardContent class="pt-6 space-y-6">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground block mb-1">Subject</span>
                            <h3 class="text-xl font-bold text-foreground">{{ inquiry.subject }}</h3>
                        </div>

                        <Separator />

                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground block mb-2">Message Body</span>
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-800 text-slate-800 dark:text-slate-200 text-sm leading-relaxed whitespace-pre-wrap font-sans">
                                {{ inquiry.message }}
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter class="border-t border-border bg-muted/10 p-4 flex justify-between items-center">
                        <span class="text-xs text-muted-foreground">Received from {{ inquiry.email }}</span>
                        <Button as-child size="sm" variant="ghost" class="text-purple-600 hover:text-purple-700 hover:bg-purple-50 dark:hover:bg-purple-950/30">
                            <a :href="`mailto:${inquiry.email}?subject=Re: ${encodeURIComponent(inquiry.subject)}`" class="flex items-center gap-1.5">
                                <Send class="w-3.5 h-3.5" />
                                <span>Reply to Message</span>
                            </a>
                        </Button>
                    </CardFooter>
                </Card>
            </div>

            <!-- Right Sidebar (Sender Info, CRM Contact, Audit Trail) -->
            <div class="space-y-6">
                <!-- Sender Details Card -->
                <Card class="shadow-xs border-border">
                    <CardHeader class="border-b border-border bg-muted/30 pb-4">
                        <div class="flex items-center gap-2.5">
                            <User class="w-5 h-5 text-purple-600" />
                            <CardTitle class="text-base font-bold text-foreground">Sender Profile</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent class="pt-6 space-y-5">
                        <div class="flex items-center gap-3">
                            <Avatar class="h-10 w-10 border border-purple-200 bg-purple-100 text-purple-700 font-bold">
                                <AvatarFallback>{{ getInitials(inquiry.name) }}</AvatarFallback>
                            </Avatar>
                            <div>
                                <h4 class="font-bold text-foreground text-sm">{{ inquiry.name }}</h4>
                                <p class="text-xs text-muted-foreground">Inquiry Sender</p>
                            </div>
                        </div>

                        <Separator />

                        <div class="space-y-3 text-sm">
                            <div class="flex items-center gap-2.5">
                                <Mail class="w-4 h-4 text-purple-600 shrink-0" />
                                <a :href="`mailto:${inquiry.email}`" class="text-foreground hover:text-purple-600 transition-colors font-medium truncate">
                                    {{ inquiry.email }}
                                </a>
                            </div>

                            <div v-if="inquiry.phone" class="flex items-center gap-2.5">
                                <Phone class="w-4 h-4 text-purple-600 shrink-0" />
                                <a :href="`tel:${inquiry.phone}`" class="text-foreground hover:text-purple-600 transition-colors font-medium">
                                    {{ inquiry.phone }}
                                </a>
                            </div>

                            <div class="flex items-center gap-2.5">
                                <Calendar class="w-4 h-4 text-muted-foreground shrink-0" />
                                <span class="text-xs text-muted-foreground">Submitted {{ formatDate(inquiry.created_at) }}</span>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Linked CRM Contact Card -->
                <Card v-if="inquiry.contact" class="shadow-xs border-border bg-purple-50/40 dark:bg-purple-950/10 border-purple-200/60 dark:border-purple-900/40">
                    <CardHeader class="pb-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold uppercase tracking-wider text-purple-700 dark:text-purple-400 flex items-center gap-1.5">
                                <ShieldCheck class="w-4 h-4" />
                                Linked CRM Contact
                            </span>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-2 pt-0">
                        <p class="font-bold text-sm text-foreground">
                            {{ inquiry.contact.first_name }} {{ inquiry.contact.last_name }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ inquiry.contact.email }}
                        </p>
                    </CardContent>
                    <CardFooter class="pt-2">
                        <Button as-child variant="outline" size="sm" class="w-full bg-background border-purple-200 hover:border-purple-400 text-purple-700 dark:text-purple-300">
                            <Link :href="contactShow(inquiry.contact.id)" class="flex items-center justify-center gap-1.5">
                                <span>View CRM Profile</span>
                                <ExternalLink class="w-3.5 h-3.5" />
                            </Link>
                        </Button>
                    </CardFooter>
                </Card>

                <!-- System Metadata Card -->
                <Card class="shadow-xs border-border">
                    <CardHeader class="pb-3">
                        <CardTitle class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">System Audit Info</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2 text-xs text-muted-foreground pt-0">
                        <div class="flex justify-between">
                            <span>Inquiry ID:</span>
                            <span class="font-mono text-foreground">#{{ inquiry.id }}</span>
                        </div>
                        <div v-if="inquiry.ip_address" class="flex justify-between">
                            <span>IP Address:</span>
                            <span class="font-mono text-foreground">{{ inquiry.ip_address }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Received At:</span>
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

