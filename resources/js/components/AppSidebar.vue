<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Activity,
    CalendarCheck,
    FileText,
    History,
    Image,
    LayoutGrid,
    Newspaper,
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
import { index as contentIndex } from '@/routes/content';
import { edit as profileEdit } from '@/routes/profile';
import type { NavGroup } from '@/types';

const navGroups: NavGroup[] = [
    {
        items: [
            {
                title: 'Overview',
                href: dashboard(),
                icon: LayoutGrid,
                exact: true,
            },
            {
                title: 'Pages',
                href: contentIndex({ query: { type: 'page' } }),
                icon: FileText,
            },
            {
                title: 'Articles',
                href: contentIndex({ query: { type: 'article' } }),
                icon: Newspaper,
            },
            {
                title: 'Galleries',
                href: '/dashboard/galleries',
                icon: Image,
            },
            {
                title: 'Activities',
                href: bookableItemsIndex(),
                icon: Activity,
            },
            {
                title: 'Bookings',
                href: bookingsIndex(),
                icon: CalendarCheck,
            },
            {
                title: 'Users',
                href: '/dashboard/users',
                icon: Users,
            },
            {
                title: 'Logs',
                href: '/dashboard/logs',
                icon: History,
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
