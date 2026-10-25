<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Activity,
    CalendarCheck,
    FileText,
    History,
    Image,
    LayoutGrid,
    MessageSquare,
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
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { dashboard } from '@/routes';
import { index as bookableItemsIndex } from '@/routes/bookable-items';
import { index as bookingsIndex } from '@/routes/bookings';
import { index as contentIndex } from '@/routes/content';
import { index as inquiriesIndex } from '@/routes/inquiries';
import { edit as profileEdit } from '@/routes/profile';
import type { NavGroup } from '@/types';

const page = usePage();
const currentUser = computed(() => page.props.auth?.user);

const navGroups = computed<NavGroup[]>(() => {
    const isDev = Boolean(
        currentUser.value?.is_dev_admin ||
        currentUser.value?.role === 'dev' ||
        currentUser.value?.is_system_account
    );

    const items = [
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
            title: 'Messages',
            href: inquiriesIndex(),
            icon: MessageSquare,
        },
        {
            title: 'Users',
            href: '/dashboard/users',
            icon: Users,
        },
        ...(isDev ? [{
            title: 'Logs',
            href: '/dashboard/logs',
            icon: History,
        }] : []),
        {
            title: 'Settings',
            href: profileEdit(),
            icon: Settings,
        },
    ];

    return [{ items }];
});
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
