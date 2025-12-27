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

interface Store {
    id: number;
    name: string;
    code: string;
    email: string;
    is_active: boolean;
}

defineProps<{
    stores: Store[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Stores',
        href: '/stores',
    },
];
</script>

<template>
    <Head title="Stores" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">Stores</h2>
                    <p class="text-muted-foreground">
                        Manage your store locations.
                    </p>
                </div>
                <Button as-child>
                    <Link href="/stores/create">Create Store</Link>
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>All Stores</CardTitle>
                    <CardDescription>
                        List of all registered stores in the system.
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
                                        Code
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                                    >
                                        Name
                                    </th>
                                    <th
                                        class="h-12 px-4 text-left align-middle font-medium text-muted-foreground"
                                    >
                                        Email
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
                                    v-for="store in stores"
                                    :key="store.id"
                                    class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted"
                                >
                                    <td class="p-4 align-middle">
                                        {{ store.code }}
                                    </td>
                                    <td class="p-4 align-middle font-medium">
                                        {{ store.name }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        {{ store.email || '-' }}
                                    </td>
                                    <td class="p-4 align-middle">
                                        <Badge
                                            :variant="
                                                store.is_active
                                                    ? 'default'
                                                    : 'destructive'
                                            "
                                        >
                                            {{
                                                store.is_active
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
                                                :href="`/stores/${store.id}/edit`"
                                                >Edit</Link
                                            >
                                        </Button>
                                    </td>
                                </tr>
                                <tr v-if="stores.length === 0">
                                    <td
                                        colspan="5"
                                        class="p-4 text-center text-muted-foreground"
                                    >
                                        No stores found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
