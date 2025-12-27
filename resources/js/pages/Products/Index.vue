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
    barcode: string;
    is_active: boolean;
    category?: { name: string };
    brand?: { name: string };
}

interface Paginator {
    data: Product[];
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    current_page: number;
    last_page: number;
    total: number;
}

defineProps<{
    products: Paginator;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
];
</script>

<template>
    <Head title="Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">Products</h2>
                    <p class="text-muted-foreground">
                        Manage your product catalog.
                    </p>
                </div>
                <Button as-child>
                    <Link href="/products/create">Create Product</Link>
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Product Catalog</CardTitle>
                    <CardDescription> List of all products. </CardDescription>
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
                                        Name
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                                    >
                                        Category
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                                    >
                                        Brand
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                                    >
                                        Status
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
                                    v-for="product in products.data"
                                    :key="product.id"
                                    class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                >
                                    <td
                                        class="p-4 align-middle font-mono text-xs"
                                    >
                                        {{ product.sku }}
                                    </td>
                                    <td class="p-4 align-middle font-medium">
                                        {{ product.name }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ product.category?.name || '-' }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ product.brand?.name || '-' }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        <Badge
                                            :variant="
                                                product.is_active
                                                    ? 'default'
                                                    : 'secondary'
                                            "
                                        >
                                            {{
                                                product.is_active
                                                    ? 'Active'
                                                    : 'Inactive'
                                            }}
                                        </Badge>
                                    </td>
                                    <td class="p-4 text-right align-middle">
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            as-child
                                        >
                                            <Link
                                                :href="`/products/${product.id}/edit`"
                                                >Edit</Link
                                            >
                                        </Button>
                                    </td>
                                </tr>
                                <tr v-if="products.data.length === 0">
                                    <td
                                        colspan="6"
                                        class="p-4 text-center text-muted-foreground"
                                    >
                                        No products found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        class="mt-4 flex justify-end gap-1"
                        v-if="products.links.length > 3"
                    >
                        <template v-for="(link, i) in products.links" :key="i">
                            <Button
                                v-if="link.url"
                                :variant="link.active ? 'default' : 'outline'"
                                size="sm"
                                as-child
                            >
                                <Link :href="link.url" v-html="link.label" />
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
