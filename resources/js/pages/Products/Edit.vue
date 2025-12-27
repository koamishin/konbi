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
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Category {
    id: number;
    name: string;
}

interface Brand {
    id: number;
    name: string;
}

interface Product {
    id: number;
    name: string;
    sku: string;
    barcode: string;
    category_id: number | null;
    brand_id: number | null;
    description: string | null;
    is_active: boolean;
    requires_age_verification: boolean;
}

const props = defineProps<{
    product: Product;
    categories: Category[];
    brands: Brand[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
    {
        title: 'Edit',
        href: `/products/${props.product.id}/edit`,
    },
];

const form = useForm({
    name: props.product.name,
    sku: props.product.sku,
    barcode: props.product.barcode,
    category_id: props.product.category_id,
    brand_id: props.product.brand_id,
    description: props.product.description,
    is_active: !!props.product.is_active,
    requires_age_verification: !!props.product.requires_age_verification,
});

const submit = () => {
    form.put(`/products/${props.product.id}`);
};
</script>

<template>
    <Head title="Edit Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="mx-auto flex h-full w-full max-w-2xl flex-1 flex-col gap-4 p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">
                        Edit Product
                    </h2>
                    <p class="text-muted-foreground">Update product details.</p>
                </div>
            </div>

            <Card>
                <form @submit.prevent="submit">
                    <CardHeader>
                        <CardTitle>Product Details</CardTitle>
                        <CardDescription>
                            Update information for {{ product.name }}.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="name">Product Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Coca Cola 330ml"
                                required
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="sku">SKU</Label>
                                <Input
                                    id="sku"
                                    v-model="form.sku"
                                    type="text"
                                    placeholder="COKE-330"
                                    required
                                />
                                <InputError :message="form.errors.sku" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="barcode">Barcode</Label>
                                <Input
                                    id="barcode"
                                    v-model="form.barcode"
                                    type="text"
                                    placeholder="5449000131805"
                                />
                                <InputError :message="form.errors.barcode" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="category">Category</Label>
                                <select
                                    id="category"
                                    v-model="form.category_id"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option :value="null">No Category</option>
                                    <option
                                        v-for="category in categories"
                                        :key="category.id"
                                        :value="category.id"
                                    >
                                        {{ category.name }}
                                    </option>
                                </select>
                                <InputError
                                    :message="form.errors.category_id"
                                />
                            </div>
                            <div class="grid gap-2">
                                <Label for="brand">Brand</Label>
                                <select
                                    id="brand"
                                    v-model="form.brand_id"
                                    class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <option :value="null">No Brand</option>
                                    <option
                                        v-for="brand in brands"
                                        :key="brand.id"
                                        :value="brand.id"
                                    >
                                        {{ brand.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.brand_id" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="description">Description</Label>
                            <Input
                                id="description"
                                v-model="form.description"
                                type="text"
                                placeholder="Product description..."
                            />
                            <InputError :message="form.errors.description" />
                        </div>

                        <div class="mt-2 flex flex-col gap-4">
                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    id="is_active"
                                    :checked="form.is_active"
                                    @update:checked="form.is_active = $event"
                                />
                                <Label for="is_active">Active Status</Label>
                            </div>
                            <div class="flex items-center space-x-2">
                                <Checkbox
                                    id="age_verify"
                                    :checked="form.requires_age_verification"
                                    @update:checked="
                                        form.requires_age_verification = $event
                                    "
                                />
                                <Label for="age_verify"
                                    >Requires Age Verification</Label
                                >
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-between">
                        <Button variant="ghost" as-child>
                            <Link href="/products">Cancel</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            Update Product
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
