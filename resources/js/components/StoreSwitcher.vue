<script setup lang="ts">
import CreateStoreDialog from '@/components/CreateStoreDialog.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import { router, usePage } from '@inertiajs/vue3';
import { ChevronsUpDown, Plus, Store } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const page = usePage();
const { isMobile } = useSidebar();
const isCreateOpen = ref(false);

const activeStore = computed(() => page.props.auth.store);
const availableStores = computed(() => page.props.auth.availableStores || []);

const switchStore = (storeId: number | null) => {
    router.post(
        route('store-selection.update'),
        {
            store_id: storeId,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                // Optional: toast or reload
            },
        },
    );
};
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton
                        size="lg"
                        class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                    >
                        <div
                            class="flex aspect-square size-8 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground"
                        >
                            <Store class="size-4" />
                        </div>
                        <div
                            class="grid flex-1 text-left text-sm leading-tight"
                        >
                            <span class="truncate font-semibold">{{
                                activeStore?.name || 'All Stores'
                            }}</span>
                            <span class="truncate text-xs">{{
                                activeStore ? 'Active Store' : 'Global View'
                            }}</span>
                        </div>
                        <ChevronsUpDown class="ml-auto" />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    class="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg"
                    align="start"
                    side="bottom"
                    :side-offset="4"
                >
                    <DropdownMenuLabel class="text-xs text-muted-foreground">
                        Select Store
                    </DropdownMenuLabel>

                    <DropdownMenuItem
                        class="gap-2 p-2"
                        @click="switchStore(null)"
                    >
                        <div
                            class="flex size-6 items-center justify-center rounded-sm border"
                        >
                            <Store class="size-4" />
                        </div>
                        Global View
                    </DropdownMenuItem>

                    <template v-for="store in availableStores" :key="store.id">
                        <DropdownMenuItem
                            class="gap-2 p-2"
                            @click="switchStore(store.id)"
                        >
                            <div
                                class="flex size-6 items-center justify-center rounded-sm border"
                            >
                                <Store class="size-4" />
                            </div>
                            {{ store.name }}
                        </DropdownMenuItem>
                    </template>

                    <DropdownMenuSeparator />
                    <DropdownMenuItem
                        class="gap-2 p-2"
                        @click="isCreateOpen = true"
                    >
                        <div
                            class="flex size-6 items-center justify-center rounded-md border bg-background"
                        >
                            <Plus class="size-4" />
                        </div>
                        <div class="font-medium text-muted-foreground">
                            Add store
                        </div>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>

    <CreateStoreDialog
        :open="isCreateOpen"
        @update:open="isCreateOpen = $event"
    />
</template>
