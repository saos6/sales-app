<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();

const openGroups = ref<Record<string, boolean>>({});

onMounted(() => {
    const savedState = localStorage.getItem('sidebarOpenGroups');
    if (savedState) {
        openGroups.value = JSON.parse(savedState);
    }
});

watch(openGroups, (newState) => {
    localStorage.setItem('sidebarOpenGroups', JSON.stringify(newState));
}, { deep: true });

const toggleGroup = (title: string) => {
    openGroups.value[title] = !openGroups.value[title];
};
</script>

<template>
    <SidebarMenu>
        <template v-for="item in items" :key="item.title">
            <SidebarGroup v-if="item.children" class="px-2 py-0">
                <SidebarGroupLabel :icon="item.icon" @click="toggleGroup(item.title)" class="cursor-pointer">
                    {{ item.title }}
                </SidebarGroupLabel>
                <SidebarMenu v-if="openGroups[item.title]">
                    <SidebarMenuItem v-for="child in item.children" :key="child.title">
                        <SidebarMenuButton as-child :is-active="child.href === page.url" :tooltip="child.title">
                            <Link :href="child.href">
                                <component :is="child.icon" />
                                <span>{{ child.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
            <SidebarMenuItem v-else>
                <SidebarMenuButton as-child :is-active="item.href === page.url" :tooltip="item.title">
                    <Link :href="item.href">
                        <component :is="item.icon" />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </template>
    </SidebarMenu>
</template>
