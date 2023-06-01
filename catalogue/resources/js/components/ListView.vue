<template>
	<div>
		<Header />
		<div class="order-list">
			<router-link to="/">Go Back</router-link>
			<h2>Order List</h2>
			<table>
				<thead>
					<tr>
						<th>Recipient Name</th>
						<th>Address</th>
						<th>Phone Number</th>
						<th>Status</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="order in orders" :key="order.id">
						<td>{{ order.recipient_name }}</td>
						<td>{{ order.address }}</td>
						<td>{{ order.phone_number }}</td>
						<td>{{ order.status }}</td>
						<td>
							<button v-if="order.status === 'Submitted'" @click="cancelOrder(order.id)">Cancel</button>
						</td>
						<td>
							<button @click="goToDetails(order.id)">Details</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>

<script>
import axios from 'axios';
import Header from './Header.vue';

export default {
	components: {
		Header,
	},
	data() {
		return {
			orders: [],
		};
	},
	mounted() {
		this.fetchOrdersFromCache(); // Attempt to load data from cache first
		this.fetchOrders();
	},
	methods: {
		fetchOrdersFromCache() {
			const cachedData = localStorage.getItem('orderListData');
			if (cachedData) {
				try {
					this.orders = JSON.parse(cachedData);
				} catch (error) {
					console.error('Error parsing cached order list data:', error);
				}
			}
		},
		cacheOrders() {
			localStorage.setItem('orderListData', JSON.stringify(this.orders));
		},
		fetchOrders() {
			axios
				.get('api/get-order')
				.then(response => {
					this.orders = response.data;
					this.cacheOrders(); // Cache the fetched orders
				})
				.catch(error => {
					console.error('Error fetching orders:', error);
				});
		},
		cancelOrder(orderId) {
			const status = 'Cancelled';
			axios
				.put('api/change-status', { id: orderId, status })
				.then(response => {
					console.log('Order status updated:', response.data.message);
					const order = this.orders.find(o => o.id === orderId);
					if (order) {
						order.status = status;
					}
					this.cacheOrders(); // Update the cached order list
				})
				.catch(error => {
					console.error('Error updating order status:', error);
				});
		},
		goToDetails(orderId) {
			this.$router.push(`/details/${orderId}`);
		},
	},
};
</script>


<style src="../../css/ListView.css" scoped></style>
