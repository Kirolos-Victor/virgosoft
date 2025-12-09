<template>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-4 dark:text-white">My Orders</h2>

        <div class="space-y-2">
            <div
                v-for="order in orders"
                :key="order.id"
                class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded"
            >
                <div>
                    <p class="font-semibold dark:text-white">{{ order.symbol }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        <span :class="order.side === 'buy' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                            {{ order.side.toUpperCase() }}
                        </span>
                        @ {{ order.price }}
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-sm dark:text-white">
                        {{ order.filled_amount }} / {{ order.amount }}
                    </p>
                    <p class="text-xs" :class="getStatusClass(order.status)">
                        {{ getStatusText(order.status) }}
                    </p>
                </div>

                <button
                    v-if="order.status === 1"
                    @click="cancelOrder(order.id)"
                    class="px-3 py-1 bg-red-500 text-white text-sm rounded hover:bg-red-600"
                >
                    Cancel
                </button>
            </div>
            <div v-if="orders.length === 0" class="text-gray-500 text-sm">No orders</div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const orders = ref([]);

const fetchOrders = async () => {
    try {
        const response = await window.axios.get('/api/user/orders');
        orders.value = response.data;
    } catch (err) {
        console.error('Failed to fetch orders:', err);
    }
};

const cancelOrder = async (orderId) => {
    if (!confirm('Are you sure you want to cancel this order?')) return;

    try {
        await window.axios.delete(`/api/orders/${orderId}`);
        await fetchOrders();
        window.dispatchEvent(new CustomEvent('order-updated'));
    } catch (err) {
        alert(err.response?.data?.error || 'Failed to cancel order');
    }
};

const getStatusText = (status) => {
    const statuses = {
        1: 'Open',
        2: 'Filled',
        3: 'Cancelled'
    };
    return statuses[status] || 'Unknown';
};

const getStatusClass = (status) => {
    const classes = {
        1: 'text-blue-600 dark:text-blue-400',
        2: 'text-green-600 dark:text-green-400',
        3: 'text-gray-600 dark:text-gray-400'
    };
    return classes[status] || '';
};

onMounted(() => {
    fetchOrders();

    window.addEventListener('order-created', fetchOrders);
    window.addEventListener('order-updated', fetchOrders);
});
</script>
