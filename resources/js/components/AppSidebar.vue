<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    CalendarCheck,
    Camera,
    FileText,
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
                title: 'Dashboard',
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
                title: 'News & Updates',
                href: contentIndex({ query: { type: 'article' } }),
                icon: Newspaper,
            },
            {
                title: 'Media',
                href: '/dashboard/galleries',
                icon: Image,
            },
            {
                title: 'Team',
                href: bookableItemsIndex(),
                icon: Users,
            },
            {
                title: 'Fixtures & Results',
                href: bookingsIndex(),
                icon: CalendarCheck,
            },
            {
                title: 'Gallery',
                href: '/dashboard/moments',
                icon: Camera,
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
