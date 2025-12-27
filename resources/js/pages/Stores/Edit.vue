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

interface Store {
    id: number;
    name: string;
    code: string;
    email: string;
    phone: string;
    address: string;
    is_active: boolean;
}

const props = defineProps<{
    store: Store;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Stores',
        href: '/stores',
    },
    {
        title: 'Edit',
        href: `/stores/${props.store.id}/edit`,
    },
];

const form = useForm({
    name: props.store.name,
    code: props.store.code,
    email: props.store.email,
    phone: props.store.phone,
    address: props.store.address,
    is_active: !!props.store.is_active,
});

const submit = () => {
    form.put(`/stores/${props.store.id}`);
};
</script>

<template>
    <Head title="Edit Store" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="mx-auto flex h-full w-full max-w-2xl flex-1 flex-col gap-4 p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">
                        Edit Store
                    </h2>
                    <p class="text-muted-foreground">
                        Update store information.
                    </p>
                </div>
            </div>

            <Card>
                <form @submit.prevent="submit">
                    <CardHeader>
                        <CardTitle>Store Details</CardTitle>
                        <CardDescription>
                            Update information for {{ store.name }}.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="name">Store Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                placeholder="Konbi Downtown"
                                required
                            />
                            <InputError :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="code">Store Code</Label>
                            <Input
                                id="code"
                                v-model="form.code"
                                type="text"
                                placeholder="STORE-002"
                                required
                            />
                            <InputError :message="form.errors.code" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="store@example.com"
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="phone">Phone</Label>
                            <Input
                                id="phone"
                                v-model="form.phone"
                                type="text"
                                placeholder="+1 234 567 890"
                            />
                            <InputError :message="form.errors.phone" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="address">Address</Label>
                            <Input
                                id="address"
                                v-model="form.address"
                                type="text"
                                placeholder="123 Main St"
                            />
                            <InputError :message="form.errors.address" />
                        </div>

                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="is_active"
                                :checked="form.is_active"
                                @update:checked="form.is_active = $event"
                            />
                            <Label for="is_active">Active Status</Label>
                        </div>
                    </CardContent>
                    <CardFooter class="flex justify-between">
                        <Button variant="ghost" as-child>
                            <Link href="/stores">Cancel</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing">
                            Update Store
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>
</template>
