<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

const page = usePage();

const breadcrumbs = computed(() => {
    const url = page.url;
    const segments = url.split('/').filter(Boolean);

    const crumbs: Array<{ label: string; href?: string }> = [
        { label: 'Dashboard', href: '/dashboard' },
    ];

    if (segments.length > 1) {
        const section = segments[1];
        const sectionNames: Record<string, string> = {
            content: 'Content',
            menus: 'Navigation',
            'bookable-items': 'Bookable Items',
            bookings: 'Bookings',
            contacts: 'Contacts',
        };

        if (sectionNames[section]) {
            crumbs.push({
                label: sectionNames[section],
                href: `/dashboard/${section}`,
            });

            if (segments.length > 2) {
                const action = segments[2];
                const actionNames: Record<string, string> = {
                    create: 'Create',
                    edit: 'Edit',
                };

                if (actionNames[action]) {
                    crumbs.push({ label: actionNames[action] });
                } else if (!isNaN(Number(action))) {
                    crumbs.push({ label: 'View' });
                }
            }
        }
    }

    return crumbs;
});
</script>

<template>
    <div class="flex h-screen bg-background">
        <!-- Sidebar -->
        <nav
            class="fixed top-0 bottom-0 left-0 w-60 overflow-y-auto border-r border-border bg-background"
        >
            <div class="p-6">
                <Link
                    href="/dashboard"
                    class="text-lg font-semibold hover:opacity-80"
                >
                    Volara
                </Link>
            </div>

            <ul class="space-y-1 px-4">
                <li>
                    <Link
                        href="/dashboard/content"
                        class="block rounded px-4 py-2 text-sm font-medium hover:bg-accent"
                        :class="
                            $page.url.startsWith('/dashboard/content')
                                ? 'bg-accent text-foreground'
                                : 'text-muted-foreground'
                        "
                    >
                        Content
                    </Link>
                </li>
                <li>
                    <Link
                        href="/dashboard/menus"
                        class="block rounded px-4 py-2 text-sm font-medium hover:bg-accent"
                        :class="
                            $page.url.startsWith('/dashboard/menus')
                                ? 'bg-accent text-foreground'
                                : 'text-muted-foreground'
                        "
                    >
                        Navigation
                    </Link>
                </li>
                <li>
                    <Link
                        href="/dashboard/bookable-items"
                        class="block rounded px-4 py-2 text-sm font-medium hover:bg-accent"
                        :class="
                            $page.url.startsWith('/dashboard/bookable-items')
                                ? 'bg-accent text-foreground'
                                : 'text-muted-foreground'
                        "
                    >
                        Bookable Items
                    </Link>
                </li>
                <li>
                    <Link
                        href="/dashboard/bookings"
                        class="block rounded px-4 py-2 text-sm font-medium hover:bg-accent"
                        :class="
                            $page.url.startsWith('/dashboard/bookings')
                                ? 'bg-accent text-foreground'
                                : 'text-muted-foreground'
                        "
                    >
                        Bookings
                    </Link>
                </li>
                <li>
                    <Link
                        href="/dashboard/contacts"
                        class="block rounded px-4 py-2 text-sm font-medium hover:bg-accent"
                        :class="
                            $page.url.startsWith('/dashboard/contacts')
                                ? 'bg-accent text-foreground'
                                : 'text-muted-foreground'
                        "
                    >
                        Contacts
                    </Link>
                </li>
                <li>
                    <Link
                        href="/dashboard/inquiries"
                        class="block rounded px-4 py-2 text-sm font-medium hover:bg-accent"
                        :class="
                            $page.url.startsWith('/dashboard/inquiries')
                                ? 'bg-accent text-foreground'
                                : 'text-muted-foreground'
                        "
                    >
                        Inquiries
                    </Link>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="ml-60 flex flex-1 flex-col overflow-auto">
            <!-- Header with Breadcrumbs -->
            <div class="border-b border-border bg-background px-8 py-4">
                <Breadcrumbs :items="breadcrumbs" />
            </div>
            <!-- Page Content -->
            <div class="flex-1 overflow-auto">
                <slot />
            </div>
        </main>
    </div>
</template>
