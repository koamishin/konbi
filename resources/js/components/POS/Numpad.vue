<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Delete } from 'lucide-vue-next';

const props = defineProps<{
    modelValue: string | number;
    maxLength?: number;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number): void;
    (e: 'submit'): void;
    (e: 'clear'): void;
}>();

const keys = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '.', '0', 'DEL'];

const handlePress = (key: string) => {
    let currentVal = String(props.modelValue);

    if (key === 'DEL') {
        const newVal = currentVal.slice(0, -1);
        emit('update:modelValue', newVal === '' ? '0' : newVal);
        return;
    }

    // Handle decimal point logic
    if (key === '.') {
        if (currentVal.includes('.')) return;
        emit('update:modelValue', currentVal + '.');
        return;
    }

    if (currentVal === '0' && key !== '.') {
        currentVal = '';
    }

    const newValue = currentVal + key;

    emit('update:modelValue', newValue);
};
</script>

<template>
    <div class="grid h-full w-full grid-cols-3 gap-2 select-none">
        <Button
            v-for="key in keys"
            :key="key"
            variant="outline"
            class="h-14 touch-manipulation text-2xl font-bold transition-transform active:scale-95"
            :class="{
                'border-destructive/20 bg-destructive/10 text-destructive hover:bg-destructive/20':
                    key === 'DEL',
                'col-span-1': true,
            }"
            @click="handlePress(key)"
        >
            <Delete v-if="key === 'DEL'" class="h-6 w-6" />
            <span v-else>{{ key }}</span>
        </Button>
        <Button
            class="col-span-3 mt-2 h-14 touch-manipulation bg-primary text-xl font-bold text-primary-foreground transition-transform active:scale-95"
            @click="$emit('submit')"
        >
            ENTER
        </Button>
    </div>
</template>
