<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Briefcase, CheckSquare, Users, ListChecks, BarChart, MessageSquare, Paperclip } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Projetos',
        href: route('projects.index'),
        icon: Briefcase,
        children: [
            {
                title: 'Todos os Projetos',
                href: route('projects.index'),
                icon: Briefcase,
            },
            {
                title: 'Projetos Ativos',
                href: route('projects.by-status', 'in_progress'),
                icon: BarChart,
            },
            {
                title: 'Projetos Concluídos',
                href: route('projects.by-status', 'completed'),
                icon: CheckSquare,
            },
        ]
    },
    {
        title: 'Tarefas',
        href: route('tasks.by-user', { user_id: usePage().props.auth.user.id }),
        icon: ListChecks,
        children: [
            {
                title: 'Minhas Tarefas',
                href: route('tasks.by-user', { user_id: usePage().props.auth.user.id }),
                icon: CheckSquare,
            },
            {
                title: 'Tarefas Pendentes',
                href: '#',
                icon: ListChecks,
                onClick: () => {
                    // Redirecionar para tarefas pendentes do primeiro projeto
                    // Isso é apenas um exemplo, você pode ajustar conforme necessário
                    window.location.href = '/projects/1/tasks/status/pending';
                }
            },
            {
                title: 'Tarefas Concluídas',
                href: '#',
                icon: CheckSquare,
                onClick: () => {
                    // Redirecionar para tarefas concluídas do primeiro projeto
                    window.location.href = '/projects/1/tasks/status/completed';
                }
            },
        ]
    },
    {
        title: 'Comentários',
        href: '#',
        icon: MessageSquare,
        onClick: () => {
            // Redirecionar para comentários da primeira tarefa
            window.location.href = '/tasks/1/comments';
        }
    },
    {
        title: 'Anexos',
        href: '#',
        icon: Paperclip,
        onClick: () => {
            // Redirecionar para anexos da primeira tarefa
            window.location.href = '/tasks/1/attachments';
        }
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
