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
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const activeStore = computed(() => page.props.auth.store);

// If user has a store, show breadcrumbs. If not, this is likely onboarding.
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Stores',
        href: '/stores',
    },
    {
        title: 'Create',
        href: '/stores/create',
    },
];

const form = useForm({
    name: '',
    code: '',
    email: '',
    phone: '',
    address: '',
    is_active: true,
});

const submit = () => {
    form.post('/stores', {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Create Store" />

    <!-- If user has a store, use standard AppLayout -->
    <AppLayout v-if="activeStore" :breadcrumbs="breadcrumbs">
        <div
            class="mx-auto flex h-full w-full max-w-2xl flex-1 flex-col gap-4 p-4"
        >
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">
                        Create Store
                    </h2>
                    <p class="text-muted-foreground">
                        Add a new store location.
                    </p>
                </div>
            </div>

            <Card>
                <form @submit.prevent="submit">
                    <CardHeader>
                        <CardTitle>Store Details</CardTitle>
                        <CardDescription
                            >Enter the basic information for the new
                            store.</CardDescription
                        >
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
                    </CardContent>
                    <CardFooter class="flex justify-between">
                        <Button variant="ghost" as-child>
                            <Link href="/stores">Cancel</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing"
                            >Create Store</Button
                        >
                    </CardFooter>
                </form>
            </Card>
        </div>
    </AppLayout>

    <!-- If user DOES NOT have a store (Onboarding), use a Centered Modal-like Layout -->
    <div
        v-else
        class="flex min-h-screen items-center justify-center bg-muted/50 p-4"
    >
        <Card class="w-full max-w-md border-2 shadow-lg">
            <CardHeader class="space-y-1 text-center">
                <CardTitle class="text-2xl">Welcome to Konbi</CardTitle>
                <CardDescription>
                    To get started, you need to create your first store.
                </CardDescription>
            </CardHeader>
            <form @submit.prevent="submit">
                <CardContent class="grid gap-4">
                    <div class="grid gap-2">
                        <Label for="onboard-name">Store Name</Label>
                        <Input
                            id="onboard-name"
                            v-model="form.name"
                            type="text"
                            placeholder="My First Store"
                            required
                        />
                        <InputError :message="form.errors.name" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="onboard-code">Store Code</Label>
                        <Input
                            id="onboard-code"
                            v-model="form.code"
                            type="text"
                            placeholder="STORE-001"
                            required
                        />
                        <InputError :message="form.errors.code" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="onboard-email">Store Email</Label>
                        <Input
                            id="onboard-email"
                            v-model="form.email"
                            type="email"
                            placeholder="contact@mystore.com"
                        />
                        <InputError :message="form.errors.email" />
                    </div>
                </CardContent>
                <CardFooter>
                    <Button
                        class="w-full"
                        type="submit"
                        :disabled="form.processing"
                    >
                        Get Started
                    </Button>
                </CardFooter>
            </form>
        </Card>
    </div>
</template>
