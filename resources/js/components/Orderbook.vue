<template>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-4 dark:text-white">Order Book - {{ symbol }}</h2>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <h3 class="text-lg font-semibold mb-2 text-green-600 dark:text-green-400">Buy Orders</h3>
                <div class="space-y-1">
                    <div v-for="order in buyOrders" :key="order.price" class="flex justify-between text-sm">
                        <span class="text-green-600 dark:text-green-400">{{ order.price }}</span>
                        <span class="dark:text-gray-300">{{ order.total_amount }}</span>
                    </div>
                    <div v-if="buyOrders.length === 0" class="text-gray-500 text-sm">No buy orders</div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold mb-2 text-red-600 dark:text-red-400">Sell Orders</h3>
                <div class="space-y-1">
                    <div v-for="order in sellOrders" :key="order.price" class="flex justify-between text-sm">
                        <span class="text-red-600 dark:text-red-400">{{ order.price }}</span>
                        <span class="dark:text-gray-300">{{ order.total_amount }}</span>
                    </div>
                    <div v-if="sellOrders.length === 0" class="text-gray-500 text-sm">No sell orders</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    symbol: {
        type: String,
        default: 'BTC'
    }
});

const buyOrders = ref([]);
const sellOrders = ref([]);

const fetchOrderbook = async () => {
    try {
        const response = await window.axios.get('/api/orders', {
            params: { symbol: props.symbol }
        });
        buyOrders.value = response.data.buy_orders;
        sellOrders.value = response.data.sell_orders;
    } catch (err) {
        console.error('Failed to fetch orderbook:', err);
    }
};

onMounted(() => {
    fetchOrderbook();

    window.Echo.channel(`symbol.${props.symbol}`)
        .listen('OrderMatched', () => {
            fetchOrderbook();
        });

    window.addEventListener('order-created', fetchOrderbook);
});
</script>
