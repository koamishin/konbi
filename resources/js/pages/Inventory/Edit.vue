<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface InventoryItem {
    id: number;
    quantity: number;
    reorder_level: number;
    reorder_quantity: number;
    cost_price: number;
    selling_price: number;
    expiry_date: string | null;
    product: {
        name: string;
        sku: string;
    };
}

const props = defineProps<{
    inventory: InventoryItem;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Inventory',
        href: '/inventory',
    },
    {
        title: 'Adjust',
        href: `/inventory/${props.inventory.id}/edit`,
    },
];

const form = useForm({
    quantity: props.inventory.quantity,
    reorder_level: props.inventory.reorder_level,
    reorder_quantity: props.inventory.reorder_quantity,
    cost_price: props.inventory.cost_price,
    selling_price: props.inventory.selling_price,
    expiry_date: props.inventory.expiry_date,
});

const submit = () => {
    form.put(`/inventory/${props.inventory.id}`);
};
</script>

<template>
    <Head title="Adjust Inventory" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="mx-auto flex h-full w-full max-w-2xl flex-1 flex-col gap-4 p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">
                        Adjust Inventory
                    </h2>
                    <p class="text-muted-foreground">
                        Update stock and pricing for
                        {{ inventory.product.name }}.
                    </p>
                </div>
            </div>

            <Card>
                <form @submit.prevent="submit">
                    <CardHeader>
                        <CardTitle>Inventory Details</CardTitle>
                        <CardDescription>
                            SKU: {{ inventory.product.sku }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="quantity">Quantity On Hand</Label>
                            <Input
                                id="quantity"
                                v-model.number="form.quantity"
                                type="number"
                                required
                            />
                            <InputError :message="form.errors.quantity" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="cost_price">Cost Price</Label>
                                <Input
                                    id="cost_price"
                                    v-model.number="form.cost_price"
                                    type="number"
                                    step="0.01"
                                    required
                                />
                                <InputError :message="form.errors.cost_price" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="selling_price">Selling Price</Label>
                                <Input
                                    id="selling_price"
                                    v-model.number="form.selling_price"
                                    type="number"
                                    step="0.01"
                                    required
                                />
                                <InputError
                                    :message="form.errors.selling_price"
                                />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="reorder_level"
                                    >Reorder Level (Threshold)</Label
                                >
                                <Input
                                    id="reorder_level"
                                    v-model.number="form.reorder_level"
                                    type="number"
                                    required
                                />
                                <InputError
                                    :message="form.errors.reorder_level"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label for="reorder_quantity"
                                    >Reorder Quantity (Auto-Order)</Label
                                >
                                <Input
                                    id="reorder_quantity"
                                    v-model.number="form.reorder_quantity"
                                    type="number"
                                    required
                                />
                                <InputError
                                    :message="form.errors.reorder_quantity"
                                />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="expiry_date"
                                >Expiry Date (Next Batch)</Label
                            >
                            <Input
                                id="expiry_date"
                                v-model="form.expiry_date"
                                type="date"
                            />
                            <InputError :message="form.errors.expiry_date" />
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-between">
                        <Button variant="ghost" as-child>
                            <Link href="/inventory">Cancel</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            Save Changes
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
