<script setup lang="ts">
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
import {
    AlertTriangle,
    DollarSign,
    Package,
    ShoppingCart,
} from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

defineProps<{
    stats: {
        today_sales: number;
        today_transactions: number;
        low_stock_count: number;
        total_products: number;
    };
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <h2 class="text-3xl font-bold tracking-tight">Dashboard</h2>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium">
                            Total Sales (Today)
                        </CardTitle>
                        <DollarSign class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            ${{ stats.today_sales.toFixed(2) }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            +20.1% from yesterday
                        </p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium">
                            Transactions
                        </CardTitle>
                        <ShoppingCart class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            +{{ stats.today_transactions }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            +180.1% from last hour
                        </p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium">
                            Products in Stock
                        </CardTitle>
                        <Package class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">
                            {{ stats.total_products }}
                        </div>
                        <p class="text-xs text-muted-foreground">Active SKUs</p>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader
                        class="flex flex-row items-center justify-between space-y-0 pb-2"
                    >
                        <CardTitle class="text-sm font-medium">
                            Low Stock Alerts
                        </CardTitle>
                        <AlertTriangle class="h-4 w-4 text-red-500" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-red-500">
                            {{ stats.low_stock_count }}
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Items below reorder level
                        </p>
                    </CardContent>
                </Card>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                <Card class="col-span-4">
                    <CardHeader>
                        <CardTitle>Overview</CardTitle>
                        <CardDescription
                            >Sales performance over time.</CardDescription
                        >
                    </CardHeader>
                    <CardContent class="pl-2">
                        <div
                            class="flex h-[200px] items-center justify-center rounded-md border border-dashed text-muted-foreground"
                        >
                            Chart Placeholder
                        </div>
                    </CardContent>
                </Card>
                <Card class="col-span-3">
                    <CardHeader>
                        <CardTitle>Recent Sales</CardTitle>
                        <CardDescription>
                            You made 265 sales this month.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-8">
                            <!-- Placeholder for recent sales list -->
                            <div class="flex items-center">
                                <span
                                    class="relative flex h-9 w-9 shrink-0 overflow-hidden rounded-full bg-slate-200"
                                ></span>
                                <div class="ml-4 space-y-1">
                                    <p class="text-sm leading-none font-medium">
                                        Walk-in Customer
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        cash
                                    </p>
                                </div>
                                <div class="ml-auto font-medium">+$12.00</div>
                            </div>
                            <div class="flex items-center">
                                <span
                                    class="relative flex h-9 w-9 shrink-0 overflow-hidden rounded-full bg-slate-200"
                                ></span>
                                <div class="ml-4 space-y-1">
                                    <p class="text-sm leading-none font-medium">
                                        Walk-in Customer
                                    </p>
                                    <p class="text-sm text-muted-foreground">
                                        card
                                    </p>
                                </div>
                                <div class="ml-auto font-medium">+$45.50</div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
