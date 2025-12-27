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
import { Head, Link } from '@inertiajs/vue3';

interface Product {
    id: number;
    name: string;
    sku: string;
}

interface InventoryItem {
    id: number;
    quantity: number;
    cost_price: number;
    selling_price: number;
    reorder_level: number;
    product?: Product;
}

interface Paginator {
    data: InventoryItem[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
}

defineProps<{
    inventory: Paginator;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inventory',
        href: '/inventory',
    },
];
</script>

<template>
    <Head title="Inventory" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">Inventory</h2>
                    <p class="text-muted-foreground">
                        Manage stock levels and pricing.
                    </p>
                </div>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Current Stock</CardTitle>
                    <CardDescription>
                        Real-time view of product stock levels for this store.
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
                                        SKU
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                                    >
                                        Product
                                    </th>
                                    <th
                                        class="h-12 px-4 text-right align-middle font-medium text-muted-foreground"
                                    >
                                        Quantity
                                    </th>
                                    <th
                                        class="h-12 px-4 text-right align-middle font-medium text-muted-foreground"
                                    >
                                        Cost
                                    </th>
                                    <th
                                        class="h-12 px-4 text-right align-middle font-medium text-muted-foreground"
                                    >
                                        Price
                                    </th>
                                    <th
                                        class="h-12 px-4 text-right align-middle font-medium text-muted-foreground"
                                    >
                                        Reorder At
                                    </th>
                                    <th
                                        class="h-12 px-4 text-right align-middle font-medium text-muted-foreground"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="[&_tr:last-child]:border-0">
                                <tr
                                    v-for="item in inventory.data"
                                    :key="item.id"
                                    class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                >
                                    <td
                                        class="p-4 align-middle font-mono text-xs"
                                    >
                                        {{ item.product?.sku }}
                                    </td>
                                    <td class="p-4 align-middle font-medium">
                                        {{ item.product?.name }}
                                    </td>
                                    <td class="p-4 text-right align-middle">
                                        <Badge
                                            :variant="
                                                item.quantity <=
                                                item.reorder_level
                                                    ? 'destructive'
                                                    : 'outline'
                                            "
                                        >
                                            {{ item.quantity }}
                                        </Badge>
                                    </td>
                                    <td class="p-4 text-right align-middle">
                                        ${{ item.cost_price }}
                                    </td>
                                    <td
                                        class="p-4 text-right align-middle font-medium"
                                    >
                                        ${{ item.selling_price }}
                                    </td>
                                    <td class="p-4 text-right align-middle">
                                        {{ item.reorder_level }}
                                    </td>
                                    <td class="p-4 text-right align-middle">
                                        <div class="flex justify-end gap-2">
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                as-child
                                            >
                                                <Link
                                                    :href="`/inventory/${item.id}/history`"
                                                    >History</Link
                                                >
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                as-child
                                            >
                                                <Link
                                                    :href="`/inventory/${item.id}/edit`"
                                                    >Adjust</Link
                                                >
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="inventory.data.length === 0">
                                    <td
                                        colspan="7"
                                        class="p-4 text-center text-muted-foreground"
                                    >
                                        No inventory found. Add products to
                                        catalog first.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        class="mt-4 flex justify-end gap-1"
                        v-if="inventory.links.length > 3"
                    >
                        <template v-for="(link, i) in inventory.links" :key="i">
                            <Button
                                v-if="link.url"
                                :variant="link.active ? 'default' : 'outline'"
                                size="sm"
                                as-child
                            >
                                <!-- eslint-disable-next-line vue/no-v-text-v-html-on-component -->
                                <Link :href="link.url" v-html="link.label" />
                            </Button>
                            <!-- eslint-disable-next-line vue/no-v-text-v-html-on-component -->
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
