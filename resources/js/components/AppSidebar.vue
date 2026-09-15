<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    BookOpen,
    CalendarCheck,
    ClipboardList,
    FileText,
    LayoutGrid,
    Menu,
    MessageSquare,
    Settings,
    Users,
} from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as bookableItemsIndex } from '@/routes/bookable-items';
import { index as bookingsIndex } from '@/routes/bookings';
import { index as contactsIndex } from '@/routes/contacts';
import { index as contentIndex } from '@/routes/content';
import { index as inquiriesIndex } from '@/routes/inquiries';
import { edit as profileEdit } from '@/routes/profile';
import type { NavGroup } from '@/types';

const navGroups: NavGroup[] = [
    {
        items: [
            {
                title: 'Dashboard',
                href: dashboard(),
                icon: LayoutGrid,
                exact: true,
            },
            {
                title: 'Activities',
                href: bookableItemsIndex(),
                icon: ClipboardList,
            },
            {
                title: 'Bookings',
                href: bookingsIndex(),
                icon: CalendarCheck,
            },
            {
                title: 'Contacts',
                href: contactsIndex(),
                icon: Users,
            },
            {
                title: 'Messages',
                href: inquiriesIndex(),
                icon: MessageSquare,
            },
            {
                title: 'Pages',
                href: contentIndex({ query: { type: 'page' } }),
                icon: FileText,
            },
            {
                title: 'Articles',
                href: contentIndex({ query: { type: 'article' } }),
                icon: BookOpen,
            },
            {
                title: 'Settings',
                href: profileEdit(),
                icon: Settings,
            },
        ],
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :groups="navGroups" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
