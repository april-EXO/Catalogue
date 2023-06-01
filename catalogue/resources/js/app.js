import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';

import Catalogue from './components/Catalogue.vue';
import Cart from './components/Cart.vue';
import Order from './components/Order.vue';
import ListView from './components/ListView.vue';
import Details from './components/Details.vue';

const app = createApp({});

app.component('catalogue', Catalogue);
app.component('cart', Cart);
app.component('order', Order);
app.component('list-view', ListView);
app.component('details', Details);

const routes = [
	{ path: '/', component: Catalogue },
	{ path: '/cart', component: Cart },
	{ path: '/order', component: Order },
	{ path: '/listview', component: ListView },
	{ path: '/details/:id', component: Details },
];

const router = createRouter({
	history: createWebHistory(),
	routes,
});

app.use(router);

app.mount('#app');
