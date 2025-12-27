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
import { Plus, X } from 'lucide-vue-next';
import { ref } from 'vue';

interface Category {
    id: number;
    name: string;
}

interface Brand {
    id: number;
    name: string;
}

defineProps<{
    categories: Category[];
    brands: Brand[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
    {
        title: 'Create',
        href: '/products/create',
    },
];

const form = useForm({
    name: '',
    sku: '',
    barcode: '',
    category_id: '' as string | number,
    category_name: '', // For new category
    brand_id: '' as string | number,
    brand_name: '', // For new brand
    description: '',
    is_active: true,
    requires_age_verification: false,
    quantity: 0,
    cost_price: 0,
    selling_price: 0,
    reorder_level: 10,
    reorder_quantity: 50,
});

const isNewCategory = ref(false);
const isNewBrand = ref(false);

const toggleNewCategory = () => {
    isNewCategory.value = !isNewCategory.value;
    if (isNewCategory.value) {
        form.category_id = '';
    } else {
        form.category_name = '';
    }
};

const toggleNewBrand = () => {
    isNewBrand.value = !isNewBrand.value;
    if (isNewBrand.value) {
        form.brand_id = '';
    } else {
        form.brand_name = '';
    }
};

const submit = () => {
    form.post('/products', {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Create Product" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="mx-auto flex h-full w-full max-w-2xl flex-1 flex-col gap-4 p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">
                        Create Product
                    </h2>
                    <p class="text-muted-foreground">
                        Add a new product to the catalog.
                    </p>
                </div>
            </div>

            <Card>
                <form @submit.prevent="submit">
                    <CardHeader>
                        <CardTitle>Product Details</CardTitle>
                        <CardDescription>
                            Enter the basic information for the new product.
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
                                <Label for="sku">SKU (Auto)</Label>
                                <Input
                                    id="sku"
                                    v-model="form.sku"
                                    type="text"
                                    placeholder="Auto-generated if empty"
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
                                <div class="flex items-center justify-between">
                                    <Label for="category">Category</Label>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        class="h-6 px-2 text-xs"
                                        @click="toggleNewCategory"
                                    >
                                        <component
                                            :is="isNewCategory ? X : Plus"
                                            class="mr-1 h-3 w-3"
                                        />
                                        {{
                                            isNewCategory
                                                ? 'Select Existing'
                                                : 'Create New'
                                        }}
                                    </Button>
                                </div>

                                <div v-if="isNewCategory">
                                    <Input
                                        id="category_name"
                                        v-model="form.category_name"
                                        placeholder="New Category Name"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.category_name"
                                    />
                                </div>
                                <div v-else>
                                    <select
                                        id="category"
                                        v-model="form.category_id"
                                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        <option value="" disabled>
                                            Select Category
                                        </option>
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
                            </div>

                            <div class="grid gap-2">
                                <div class="flex items-center justify-between">
                                    <Label for="brand">Brand</Label>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="sm"
                                        class="h-6 px-2 text-xs"
                                        @click="toggleNewBrand"
                                    >
                                        <component
                                            :is="isNewBrand ? X : Plus"
                                            class="mr-1 h-3 w-3"
                                        />
                                        {{
                                            isNewBrand
                                                ? 'Select Existing'
                                                : 'Create New'
                                        }}
                                    </Button>
                                </div>

                                <div v-if="isNewBrand">
                                    <Input
                                        id="brand_name"
                                        v-model="form.brand_name"
                                        placeholder="New Brand Name"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.brand_name"
                                    />
                                </div>
                                <div v-else>
                                    <select
                                        id="brand"
                                        v-model="form.brand_id"
                                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-ring focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        <option value="" disabled>
                                            Select Brand
                                        </option>
                                        <option
                                            v-for="brand in brands"
                                            :key="brand.id"
                                            :value="brand.id"
                                        >
                                            {{ brand.name }}
                                        </option>
                                    </select>
                                    <InputError
                                        :message="form.errors.brand_id"
                                    />
                                </div>
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

                        <!-- Initial Inventory Section -->
                        <div class="border-t pt-4">
                            <h3 class="mb-4 text-lg font-medium">
                                Initial Inventory
                            </h3>

                            <div class="mb-4 grid grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label for="quantity"
                                        >Initial Quantity</Label
                                    >
                                    <Input
                                        id="quantity"
                                        v-model.number="form.quantity"
                                        type="number"
                                        min="0"
                                        placeholder="0"
                                    />
                                    <InputError
                                        :message="form.errors.quantity"
                                    />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="reorder_level"
                                        >Reorder Level</Label
                                    >
                                    <Input
                                        id="reorder_level"
                                        v-model.number="form.reorder_level"
                                        type="number"
                                        min="0"
                                        placeholder="10"
                                    />
                                    <InputError
                                        :message="form.errors.reorder_level"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label for="cost_price">Cost Price</Label>
                                    <Input
                                        id="cost_price"
                                        v-model.number="form.cost_price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        placeholder="0.00"
                                    />
                                    <InputError
                                        :message="form.errors.cost_price"
                                    />
                                </div>
                                <div class="grid gap-2">
                                    <Label for="selling_price"
                                        >Selling Price</Label
                                    >
                                    <Input
                                        id="selling_price"
                                        v-model.number="form.selling_price"
                                        type="number"
                                        min="0"
                                        step="0.01"
                                        placeholder="0.00"
                                    />
                                    <InputError
                                        :message="form.errors.selling_price"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="mt-2 flex flex-col gap-4 border-t pt-4">
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
                            Create Product
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
