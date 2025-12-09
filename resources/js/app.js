import './bootstrap';
import { createApp } from 'vue';
import OrderForm from './components/OrderForm.vue';
import Orderbook from './components/Orderbook.vue';
import WalletOverview from './components/WalletOverview.vue';
import UserOrders from './components/UserOrders.vue';

const app = createApp({});

app.component('order-form', OrderForm);
app.component('orderbook', Orderbook);
app.component('wallet-overview', WalletOverview);
app.component('user-orders', UserOrders);

app.mount('#app');
