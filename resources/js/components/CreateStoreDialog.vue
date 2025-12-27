<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';

const props = defineProps<{
    open: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'success', store: any): void; // We can't easily get the store back without API modification, but we'll try
}>();

const form = useForm({
    name: '',
    code: '',
    email: '',
    // Other fields can be minimal for quick create
});

const submit = () => {
    form.post('/stores', {
        onSuccess: () => {
            form.reset();
            emit('update:open', false);
            // Ideally we trigger a switch here, but without the ID it's hard.
            // We will modify the controller to redirect to the new store context if possible,
            // OR we'll just let the user see it in the list.
            // The user asked to "switch over to it".
            // To do that, we need the StoreController to handle a "switch" param or return the ID.
        },
    });
};
</script>

<template>
    <Dialog :open="open" @update:open="$emit('update:open', $event)">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>Create Store</DialogTitle>
                <DialogDescription>
                    Add a new store to your organization.
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submit" class="grid gap-4 py-4">
                <div class="grid gap-2">
                    <Label for="name">Store Name</Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        required
                        placeholder="My New Store"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="code">Store Code</Label>
                    <Input
                        id="code"
                        v-model="form.code"
                        required
                        placeholder="STORE-003"
                    />
                    <InputError :message="form.errors.code" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email (Optional)</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        placeholder="store@example.com"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <DialogFooter>
                    <Button type="submit" :disabled="form.processing"
                        >Create Store</Button
                    >
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
