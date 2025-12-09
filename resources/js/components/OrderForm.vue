<template>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-4 dark:text-white">Place Order</h2>

        <form @submit.prevent="submitOrder">
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2 dark:text-gray-200">Symbol</label>
                <input
                    v-model="form.symbol"
                    type="text"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    required
                />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2 dark:text-gray-200">Side</label>
                <div class="flex gap-4">
                    <button
                        type="button"
                        @click="form.side = 'buy'"
                        :class="form.side === 'buy' ? 'bg-green-500 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                        class="flex-1 py-2 rounded-lg font-semibold"
                    >
                        BUY
                    </button>
                    <button
                        type="button"
                        @click="form.side = 'sell'"
                        :class="form.side === 'sell' ? 'bg-red-500 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-white'"
                        class="flex-1 py-2 rounded-lg font-semibold"
                    >
                        SELL
                    </button>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2 dark:text-gray-200">Price</label>
                <input
                    v-model="form.price"
                    type="number"
                    step="0.00000001"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    required
                />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2 dark:text-gray-200">Amount</label>
                <input
                    v-model="form.amount"
                    type="number"
                    step="0.00000001"
                    class="w-full px-3 py-2 border rounded-lg dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    required
                />
            </div>

            <div class="mb-4 p-3 bg-gray-100 dark:bg-gray-700 rounded">
                <p class="text-sm dark:text-gray-200">Total: <span class="font-bold">{{ totalVolume }}</span></p>
            </div>

            <button
                type="submit"
                :disabled="loading"
                class="w-full py-3 bg-blue-500 text-white rounded-lg font-semibold hover:bg-blue-600 disabled:opacity-50"
            >
                {{ loading ? 'Placing Order...' : 'Place Order' }}
            </button>

            <div v-if="error" class="mt-4 p-3 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 rounded">
                {{ error }}
            </div>

            <div v-if="success" class="mt-4 p-3 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 rounded">
                {{ success }}
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    initialSymbol: {
        type: String,
        default: 'BTC'
    }
});

const form = ref({
    symbol: props.initialSymbol,
    side: 'buy',
    price: '',
    amount: ''
});

const loading = ref(false);
const error = ref('');
const success = ref('');

const totalVolume = computed(() => {
    const total = (parseFloat(form.value.price) || 0) * (parseFloat(form.value.amount) || 0);
    return total.toFixed(8);
});

const submitOrder = async () => {
    loading.value = true;
    error.value = '';
    success.value = '';

    try {
        const response = await window.axios.post('/api/orders', form.value);
        success.value = response.data.message;

        form.value.price = '';
        form.value.amount = '';

        window.dispatchEvent(new CustomEvent('order-created'));
    } catch (err) {
        error.value = err.response?.data?.error || 'Failed to place order';
    } finally {
        loading.value = false;
    }
};
</script>
