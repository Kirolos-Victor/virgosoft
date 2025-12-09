<template>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-4 dark:text-white">Wallet</h2>

        <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
            <p class="text-sm text-gray-600 dark:text-gray-300">Balance (USD)</p>
            <p class="text-2xl font-bold dark:text-white">{{ user?.balance || '0.00000000' }}</p>
        </div>

        <h3 class="text-lg font-semibold mb-2 dark:text-white">Assets</h3>
        <div class="space-y-2">
            <div
                v-for="asset in assets"
                :key="asset.symbol"
                class="flex justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded"
            >
                <div>
                    <p class="font-semibold dark:text-white">{{ asset.symbol }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Available: {{ asset.available }}</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold dark:text-white">{{ asset.amount }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Locked: {{ asset.locked_amount }}</p>
                </div>
            </div>
            <div v-if="assets.length === 0" class="text-gray-500 text-sm">No assets</div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    userId: {
        type: Number,
        required: true
    }
});

const user = ref(null);
const assets = ref([]);

const fetchProfile = async () => {
    try {
        const response = await window.axios.get('/api/profile');
        user.value = response.data.user;
        assets.value = response.data.assets;
    } catch (err) {
        console.error('Failed to fetch profile:', err);
    }
};

onMounted(() => {
    fetchProfile();

    window.Echo.private(`user.${props.userId}`)
        .listen('OrderMatched', () => {
            fetchProfile();
        });

    window.addEventListener('order-updated', fetchProfile);
});
</script>
