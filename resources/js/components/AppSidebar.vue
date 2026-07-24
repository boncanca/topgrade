<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    CalendarCheck,
    ClipboardList,
    FileText,
    Inbox,
    LayoutGrid,
    Menu,
    Settings,
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
import { index as menusIndex } from '@/routes/menus';
import { edit as profileEdit } from '@/routes/profile';
import type { NavGroup } from '@/types';

const navGroups: NavGroup[] = [
    {
        title: 'Overview',
        items: [
            {
                title: 'Dashboard',
                href: dashboard(),
                icon: LayoutGrid,
                exact: true,
            },
        ],
    },
    {
        title: 'Website',
        items: [
            {
                title: 'Pages',
                href: contentIndex(),
                icon: FileText,
            },
            {
                title: 'Menus',
                href: menusIndex(),
                icon: Menu,
            },
        ],
    },
    {
        title: 'Bookings',
        items: [
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
        ],
    },
    {
        title: 'People',
        items: [
            {
                title: 'Contacts',
                href: contactsIndex(),
                icon: ClipboardList,
            },
        ],
    },
    {
        title: 'Communication',
        items: [
            {
                title: 'Inquiries',
                href: inquiriesIndex(),
                icon: Inbox,
            },
        ],
    },
    {
        title: 'System',
        items: [
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
