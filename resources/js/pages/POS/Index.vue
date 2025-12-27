<script setup lang="ts">
import Numpad from '@/components/POS/Numpad.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
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
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import {
    Banknote,
    ChevronLeft,
    CreditCard,
    Minus,
    Monitor,
    Plus,
    Search,
    ShoppingCart,
    Tablet,
    Trash2,
} from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';

interface Product {
    id: number;
    inventory_id: number;
    sku: string;
    barcode: string;
    name: string;
    price: number;
    quantity: number;
    category: string;
    image_url: string | null;
}

interface Category {
    id: number;
    name: string;
}

const products = ref<Product[]>([]);
const categories = ref<Category[]>([]);
const selectedCategory = ref<string>('All');
const searchQuery = ref('');
const cart = ref<Array<Product & { cartQty: number }>>([]);
const isLoading = ref(true);
const isPaymentModalOpen = ref(false);
const paymentMethod = ref('cash');

// Mode & View State
const isTabletMode = ref(false); // Default to Desktop
const sidebarView = ref<'cart' | 'payment'>('cart');
const amountPaidString = ref(''); // For Numpad string manipulation

const breadcrumbs: BreadcrumbItem[] = [{ title: 'POS', href: '/pos' }];

const fetchCatalog = async () => {
    try {
        const { data } = await axios.get('/pos/products');
        products.value = data.products;
        categories.value = [{ id: 0, name: 'All' }, ...data.categories];
    } catch (e) {
        console.error('Failed to load catalog', e);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchCatalog();
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});

const handleKeydown = (e: KeyboardEvent) => {
    if (isTabletMode.value) return; // Disable keyboard shortcuts in tablet mode

    // F12 or Insert to Checkout
    if (e.key === 'F12' || e.key === 'Insert') {
        e.preventDefault();
        handleCheckout();
    }

    // Esc to cancel/close modal
    if (e.key === 'Escape') {
        if (isPaymentModalOpen.value) isPaymentModalOpen.value = false;
        if (sidebarView.value === 'payment') sidebarView.value = 'cart';
    }
};

const filteredProducts = computed(() => {
    let filtered = products.value;

    if (selectedCategory.value !== 'All') {
        filtered = filtered.filter(
            (p) => p.category === selectedCategory.value,
        );
    }

    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (p) =>
                p.name.toLowerCase().includes(q) ||
                p.sku.toLowerCase().includes(q) ||
                p.barcode.includes(q),
        );
    }

    return filtered;
});

const addToCart = (product: Product) => {
    if (product.quantity <= 0) return;

    const existing = cart.value.find((p) => p.id === product.id);
    if (existing) {
        if (existing.cartQty < product.quantity) {
            existing.cartQty++;
        }
    } else {
        cart.value.push({ ...product, cartQty: 1 });
    }
};

const updateQuantity = (index: number, delta: number) => {
    const item = cart.value[index];
    const newQty = item.cartQty + delta;

    if (newQty > 0 && newQty <= item.quantity) {
        item.cartQty = newQty;
    }
};

const removeFromCart = (index: number) => {
    cart.value.splice(index, 1);
};

const subtotal = computed(() =>
    cart.value.reduce((sum, item) => sum + item.price * item.cartQty, 0),
);
const tax = computed(() => subtotal.value * 0.1);
const total = computed(() => subtotal.value + tax.value);

const amountPaid = ref(0);
const change = computed(() => {
    const paid =
        isTabletMode.value && sidebarView.value === 'payment'
            ? parseFloat(amountPaidString.value || '0')
            : amountPaid.value;
    return paid - total.value;
});

const handleCheckout = () => {
    if (cart.value.length === 0) return;

    if (isTabletMode.value) {
        amountPaidString.value = '';
        sidebarView.value = 'payment';
    } else {
        amountPaid.value = total.value;
        isPaymentModalOpen.value = true;
    }
};

const processSale = async () => {
    const finalAmountPaid = isTabletMode.value
        ? parseFloat(amountPaidString.value || '0')
        : amountPaid.value;

    if (cart.value.length === 0 || finalAmountPaid < total.value) return;

    try {
        await axios.post('/pos/sales', {
            items: cart.value.map((item) => ({
                id: item.id,
                quantity: item.cartQty,
            })),
            payment_method: paymentMethod.value,
            amount_paid: finalAmountPaid,
        });

        isPaymentModalOpen.value = false;
        sidebarView.value = 'cart';
        cart.value = [];
        amountPaid.value = 0;
        amountPaidString.value = '';
        alert('Sale completed successfully!');
        fetchCatalog();
    } catch (e) {
        alert('Sale failed');
        console.error(e);
    }
};

// Sync numpad input to amount string
const updateNumpadAmount = (val: string | number) => {
    amountPaidString.value = String(val);
};
</script>

<template>
    <Head title="POS" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-[calc(100vh-4rem)] overflow-hidden bg-background">
            <!-- Left: Product Catalog -->
            <div
                class="flex flex-1 flex-col gap-4 overflow-hidden border-r p-4"
            >
                <!-- Header / Controls -->
                <div class="flex items-center justify-between gap-4">
                    <div class="relative max-w-md flex-1">
                        <Search
                            class="absolute top-2.5 left-2.5 h-4 w-4 text-muted-foreground"
                        />
                        <Input
                            v-model="searchQuery"
                            type="search"
                            placeholder="Search products..."
                            class="w-full bg-background pl-9"
                        />
                    </div>

                    <!-- Mode Switcher -->
                    <div
                        class="flex items-center space-x-2 rounded-lg border bg-muted p-1"
                    >
                        <Button
                            variant="ghost"
                            size="sm"
                            class="h-8 px-2"
                            :class="{
                                'bg-background shadow-sm': !isTabletMode,
                                'text-muted-foreground': isTabletMode,
                            }"
                            @click="isTabletMode = false"
                        >
                            <Monitor class="mr-1 h-4 w-4" />
                            Desktop
                        </Button>
                        <Button
                            variant="ghost"
                            size="sm"
                            class="h-8 px-2"
                            :class="{
                                'bg-background shadow-sm': isTabletMode,
                                'text-muted-foreground': !isTabletMode,
                            }"
                            @click="isTabletMode = true"
                        >
                            <Tablet class="mr-1 h-4 w-4" />
                            Tablet
                        </Button>
                    </div>
                </div>

                <!-- Categories -->
                <div class="scrollbar-hide flex gap-2 overflow-x-auto pb-2">
                    <Button
                        v-for="cat in categories"
                        :key="cat.id"
                        :variant="
                            selectedCategory === cat.name
                                ? 'default'
                                : 'outline'
                        "
                        :size="isTabletMode ? 'lg' : 'sm'"
                        @click="selectedCategory = cat.name"
                        class="whitespace-nowrap"
                    >
                        {{ cat.name }}
                    </Button>
                </div>

                <!-- Product Grid -->
                <div class="flex-1 overflow-y-auto pr-2">
                    <div
                        v-if="isLoading"
                        class="flex h-full items-center justify-center"
                    >
                        <div
                            class="h-8 w-8 animate-spin rounded-full border-b-2 border-primary"
                        ></div>
                    </div>

                    <div
                        v-else
                        class="grid gap-4"
                        :class="
                            isTabletMode
                                ? 'grid-cols-3 md:grid-cols-4'
                                : 'grid-cols-4 md:grid-cols-5'
                        "
                    >
                        <Card
                            v-for="product in filteredProducts"
                            :key="product.id"
                            @click="addToCart(product)"
                            class="group relative cursor-pointer overflow-hidden transition-colors hover:border-primary active:scale-95"
                            :class="{
                                'cursor-not-allowed opacity-50':
                                    product.quantity <= 0,
                            }"
                        >
                            <CardContent class="flex h-full flex-col p-3">
                                <div
                                    class="relative mb-2 flex aspect-square items-center justify-center rounded-md bg-muted"
                                >
                                    <img
                                        v-if="product.image_url"
                                        :src="product.image_url"
                                        class="h-full w-full rounded-md object-cover"
                                    />
                                    <span
                                        v-else
                                        class="text-xl font-bold text-muted-foreground"
                                        >{{ product.name.charAt(0) }}</span
                                    >

                                    <Badge
                                        v-if="product.quantity <= 0"
                                        variant="destructive"
                                        class="absolute top-1 right-1 h-5 px-1 py-0 text-[10px]"
                                    >
                                        No Stock
                                    </Badge>
                                </div>
                                <div class="mt-auto">
                                    <div
                                        class="truncate text-sm font-semibold"
                                        :title="product.name"
                                    >
                                        {{ product.name }}
                                    </div>
                                    <div
                                        class="mt-1 flex items-end justify-between"
                                    >
                                        <div class="font-bold text-primary">
                                            ${{ product.price.toFixed(2) }}
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>

            <!-- Right: Cart & Checkout (Sidebar) -->
            <div
                class="flex w-[400px] flex-col border-l bg-card transition-all duration-300"
            >
                <!-- View: Cart -->
                <template v-if="sidebarView === 'cart'">
                    <div
                        class="flex h-16 items-center justify-between border-b p-4"
                    >
                        <h2 class="flex items-center gap-2 text-lg font-bold">
                            <ShoppingCart class="h-5 w-5" />
                            Current Order
                        </h2>
                        <Button
                            variant="ghost"
                            size="icon"
                            @click="cart = []"
                            :disabled="cart.length === 0"
                        >
                            <Trash2 class="h-4 w-4 text-destructive" />
                        </Button>
                    </div>

                    <div class="flex-1 space-y-3 overflow-y-auto p-4">
                        <div
                            v-if="cart.length === 0"
                            class="flex h-full flex-col items-center justify-center text-muted-foreground"
                        >
                            <ShoppingCart class="mb-2 h-12 w-12 opacity-20" />
                            <p>Cart is empty</p>
                        </div>

                        <div
                            v-for="(item, index) in cart"
                            :key="item.id"
                            class="flex items-center gap-3 rounded-lg bg-accent/30 p-2"
                        >
                            <div class="min-w-0 flex-1">
                                <div class="truncate font-medium">
                                    {{ item.name }}
                                </div>
                                <div class="text-sm text-muted-foreground">
                                    ${{ item.price.toFixed(2) }}
                                </div>
                            </div>
                            <div
                                class="flex items-center gap-1 rounded-md border bg-background"
                            >
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="h-8 w-8 rounded-none"
                                    @click="updateQuantity(index, -1)"
                                >
                                    <Minus class="h-3 w-3" />
                                </Button>
                                <span
                                    class="w-8 text-center text-sm font-medium"
                                    >{{ item.cartQty }}</span
                                >
                                <Button
                                    variant="ghost"
                                    size="icon"
                                    class="h-8 w-8 rounded-none"
                                    @click="updateQuantity(index, 1)"
                                >
                                    <Plus class="h-3 w-3" />
                                </Button>
                            </div>
                            <div class="min-w-[3rem] text-right font-bold">
                                ${{ (item.price * item.cartQty).toFixed(2) }}
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 border-t bg-muted/30 p-4">
                        <div class="space-y-1 text-sm">
                            <div
                                class="flex justify-between text-muted-foreground"
                            >
                                <span>Subtotal</span>
                                <span>${{ subtotal.toFixed(2) }}</span>
                            </div>
                            <div
                                class="flex justify-between text-muted-foreground"
                            >
                                <span>Tax (10%)</span>
                                <span>${{ tax.toFixed(2) }}</span>
                            </div>
                        </div>
                        <div
                            class="flex justify-between border-t pt-2 text-2xl font-bold"
                        >
                            <span>Total</span>
                            <span>${{ total.toFixed(2) }}</span>
                        </div>

                        <Button
                            size="lg"
                            class="h-14 w-full text-lg"
                            :disabled="cart.length === 0"
                            @click="handleCheckout"
                        >
                            {{ isTabletMode ? 'Checkout' : 'Checkout (F12)' }}
                        </Button>
                    </div>
                </template>

                <!-- View: Payment (Tablet Mode) -->
                <template v-else-if="sidebarView === 'payment'">
                    <div class="flex h-16 items-center gap-2 border-b p-4">
                        <Button
                            variant="ghost"
                            size="icon"
                            @click="sidebarView = 'cart'"
                        >
                            <ChevronLeft class="h-5 w-5" />
                        </Button>
                        <h2 class="text-lg font-bold">Payment</h2>
                    </div>

                    <div class="flex flex-1 flex-col gap-4 p-4">
                        <!-- Total Display -->
                        <div class="rounded-lg bg-primary/10 p-4 text-center">
                            <div class="text-sm text-muted-foreground">
                                Total Due
                            </div>
                            <div class="text-3xl font-bold text-primary">
                                ${{ total.toFixed(2) }}
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="grid grid-cols-2 gap-2">
                            <Button
                                variant="outline"
                                class="h-12"
                                :class="{
                                    'border-primary bg-primary/5':
                                        paymentMethod === 'cash',
                                }"
                                @click="paymentMethod = 'cash'"
                            >
                                <Banknote class="mr-2 h-4 w-4" /> Cash
                            </Button>
                            <Button
                                variant="outline"
                                class="h-12"
                                :class="{
                                    'border-primary bg-primary/5':
                                        paymentMethod === 'card',
                                }"
                                @click="paymentMethod = 'card'"
                            >
                                <CreditCard class="mr-2 h-4 w-4" /> Card
                            </Button>
                        </div>

                        <!-- Amount Display -->
                        <div class="flex flex-col gap-1">
                            <Label>Amount Tendered</Label>
                            <div
                                class="flex h-12 items-center justify-end rounded-md border bg-background p-2 text-right text-2xl font-bold"
                            >
                                {{ amountPaidString || '0' }}
                            </div>
                        </div>

                        <!-- Change Display -->
                        <div
                            class="flex items-center justify-between rounded-md bg-muted p-2"
                        >
                            <span class="font-medium">Change:</span>
                            <span
                                class="text-lg font-bold"
                                :class="
                                    change >= 0
                                        ? 'text-green-600'
                                        : 'text-red-500'
                                "
                            >
                                ${{ change.toFixed(2) }}
                            </span>
                        </div>

                        <!-- Numpad -->
                        <div class="flex-1">
                            <Numpad
                                :model-value="amountPaidString"
                                @update:model-value="updateNumpadAmount"
                                @submit="processSale"
                            />
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Desktop Payment Dialog -->
        <Dialog
            :open="isPaymentModalOpen"
            @update:open="isPaymentModalOpen = $event"
        >
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Process Payment</DialogTitle>
                    <DialogDescription>
                        Complete the transaction. Total due:
                        <span class="font-bold text-primary"
                            >${{ total.toFixed(2) }}</span
                        >
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-2 gap-4">
                        <Button
                            variant="outline"
                            class="flex h-20 flex-col gap-2"
                            :class="{
                                'border-primary bg-primary/5':
                                    paymentMethod === 'cash',
                            }"
                            @click="paymentMethod = 'cash'"
                        >
                            <Banknote class="h-6 w-6" />
                            Cash
                        </Button>
                        <Button
                            variant="outline"
                            class="flex h-20 flex-col gap-2"
                            :class="{
                                'border-primary bg-primary/5':
                                    paymentMethod === 'card',
                            }"
                            @click="paymentMethod = 'card'"
                        >
                            <CreditCard class="h-6 w-6" />
                            Card
                        </Button>
                    </div>

                    <div class="space-y-2">
                        <Label>Amount Tendered</Label>
                        <Input
                            v-model.number="amountPaid"
                            type="number"
                            step="0.01"
                            class="text-right text-lg font-bold"
                            autofocus
                            @keydown.enter="processSale"
                        />
                    </div>

                    <div
                        class="flex items-center justify-between rounded-md bg-muted p-3"
                        :class="{
                            'bg-green-100 text-green-700': change >= 0,
                            'bg-red-100 text-red-700': change < 0,
                        }"
                    >
                        <span class="font-medium">Change Due:</span>
                        <span class="text-xl font-bold"
                            >${{ change.toFixed(2) }}</span
                        >
                    </div>
                </div>

                <DialogFooter>
                    <Button
                        variant="outline"
                        @click="isPaymentModalOpen = false"
                        >Cancel</Button
                    >
                    <Button @click="processSale" :disabled="amountPaid < total"
                        >Complete Sale</Button
                    >
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
