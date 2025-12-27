<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

interface StockAdjustment {
    id: number;
    type: string;
    quantity: number;
    reason: string;
    created_at: string;
    user?: {
        name: string;
    };
}

interface Product {
    name: string;
    sku: string;
}

interface InventoryItem {
    id: number;
    product?: Product;
}

interface Paginator {
    data: StockAdjustment[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}

const props = defineProps<{
    inventory: InventoryItem;
    history: Paginator;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inventory',
        href: '/inventory',
    },
    {
        title: 'History',
        href: `/inventory/${props.inventory.id}/history`,
    },
];

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString();
};
</script>

<template>
    <Head title="Inventory History" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">
                        Stock History
                    </h2>
                    <p class="text-muted-foreground">
                        History of stock movements for
                        {{ inventory.product?.name }} ({{
                            inventory.product?.sku
                        }}).
                    </p>
                </div>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Movements Log</CardTitle>
                    <CardDescription>
                        All sales, adjustments, and receipts.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="relative w-full overflow-auto">
                        <table class="w-full caption-bottom text-sm">
                            <thead class="[&_tr]:border-b">
                                <tr
                                    class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                >
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                                    >
                                        Type
                                    </th>
                                    <th
                                        class="h-12 px-4 text-right align-middle font-medium text-muted-foreground"
                                    >
                                        Change
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                                    >
                                        Reason
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                                    >
                                        User
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="[&_tr:last-child]:border-0">
                                <tr
                                    v-for="item in history.data"
                                    :key="item.id"
                                    class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                >
                                    <td class="p-4 align-middle">
                                        {{ formatDate(item.created_at) }}
                                    </td>
                                    <td class="p-4 align-middle capitalize">
                                        <Badge
                                            :variant="
                                                item.type === 'sale'
                                                    ? 'outline'
                                                    : 'default'
                                            "
                                        >
                                            {{ item.type }}
                                        </Badge>
                                    </td>
                                    <td
                                        class="p-4 text-right align-middle font-medium"
                                        :class="
                                            item.quantity > 0
                                                ? 'text-green-600'
                                                : 'text-red-600'
                                        "
                                    >
                                        {{ item.quantity > 0 ? '+' : ''
                                        }}{{ item.quantity }}
                                    </td>
                                    <td
                                        class="p-4 align-middle text-muted-foreground"
                                    >
                                        {{ item.reason || '-' }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ item.user?.name || 'System' }}
                                    </td>
                                </tr>
                                <tr v-if="history.data.length === 0">
                                    <td
                                        colspan="5"
                                        class="p-4 text-center text-muted-foreground"
                                    >
                                        No history found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        class="mt-4 flex justify-end gap-1"
                        v-if="history.links.length > 3"
                    >
                        <template v-for="(link, i) in history.links" :key="i">
                            <Button
                                v-if="link.url"
                                :variant="link.active ? 'default' : 'outline'"
                                size="sm"
                                as-child
                            >
                                <a :href="link.url" v-html="link.label" />
                            </Button>
                            <span
                                v-else
                                class="px-2 py-1 text-sm text-muted-foreground"
                                v-html="link.label"
                            ></span>
                        </template>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
