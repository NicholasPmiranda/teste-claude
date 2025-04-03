<script setup lang="ts">
import { ref } from 'vue';
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem, SidebarSubMenu } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ChevronDown, ChevronRight } from 'lucide-vue-next';

defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();
const openSubmenus = ref({});

const toggleSubmenu = (itemTitle) => {
    openSubmenus.value[itemTitle] = !openSubmenus.value[itemTitle];
};

const isSubmenuOpen = (itemTitle) => {
    return !!openSubmenus.value[itemTitle];
};

const isActive = (href) => {
    return href === page.url;
};
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Platform</SidebarGroupLabel>
        <SidebarMenu>
            <template v-for="item in items" :key="item.title">
                <!-- Item com submenu -->
                <SidebarMenuItem v-if="item.children && item.children.length > 0">
                    <div @click="toggleSubmenu(item.title)" class="cursor-pointer">
                        <SidebarMenuButton 
                            :is-active="isActive(item.href) || isSubmenuOpen(item.title)"
                            :tooltip="item.title"
                        >
                            <div class="flex items-center justify-between w-full">
                                <div class="flex items-center">
                                    <component :is="item.icon" class="h-4 w-4 mr-2" />
                                    <span>{{ item.title }}</span>
                                </div>
                                <component :is="isSubmenuOpen(item.title) ? ChevronDown : ChevronRight" class="h-4 w-4" />
                            </div>
                        </SidebarMenuButton>
                    </div>
                    
                    <!-- Submenu -->
                    <div v-show="isSubmenuOpen(item.title)" class="pl-6 mt-1 space-y-1">
                        <SidebarMenuItem v-for="child in item.children" :key="child.title">
                            <SidebarMenuButton 
                                as-child 
                                :is-active="isActive(child.href)"
                                :tooltip="child.title"
                                size="sm"
                            >
                                <template v-if="child.onClick">
                                    <a href="#" @click.prevent="child.onClick" class="flex items-center">
                                        <component :is="child.icon" class="h-4 w-4 mr-2" />
                                        <span>{{ child.title }}</span>
                                    </a>
                                </template>
                                <Link v-else :href="child.href" class="flex items-center">
                                    <component :is="child.icon" class="h-4 w-4 mr-2" />
                                    <span>{{ child.title }}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </div>
                </SidebarMenuItem>
                
                <!-- Item sem submenu -->
                <SidebarMenuItem v-else>
                    <SidebarMenuButton 
                        as-child 
                        :is-active="isActive(item.href)"
                        :tooltip="item.title"
                    >
                        <template v-if="item.onClick">
                            <a href="#" @click.prevent="item.onClick" class="flex items-center">
                                <component :is="item.icon" class="h-4 w-4 mr-2" />
                                <span>{{ item.title }}</span>
                            </a>
                        </template>
                        <Link v-else :href="item.href" class="flex items-center">
                            <component :is="item.icon" class="h-4 w-4 mr-2" />
                            <span>{{ item.title }}</span>
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </template>
        </SidebarMenu>
    </SidebarGroup>
</template>
