<template>
	<div class="container">
		<router-link to="/listview">Go Back</router-link>
		<h2>Order Details</h2>
		<div v-if="order">
			<table>
				<tbody>
					<tr>
						<td>Order ID:</td>
						<td>{{ order.id }}</td>
					</tr>
					<tr>
						<td>Recipient Name:</td>
						<td>{{ order.recipient_name }}</td>
					</tr>
					<tr>
						<td>Address:</td>
						<td>{{ order.address }}</td>
					</tr>
					<tr>
						<td>Phone Number:</td>
						<td>{{ order.phone_number }}</td>
					</tr>
					<tr>
						<td>Items:</td>
						<td>
							<ul>
								<li v-for="item in order.items" :key="item.id">
									{{ item.name }} - Quantity: {{ item.quantity }}
								</li>
							</ul>
						</td>
					</tr>
					<tr>
						<td>Status:</td>
						<td>{{ order.status }}</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div v-else>
			<p>Loading order details...</p>
		</div>
	</div>
</template>
  

<script>
import axios from 'axios';

export default {
	data() {
		return {
			order: null,
		};
	},
	mounted() {
		const orderId = this.$route.params.id;
		this.fetchOrder(orderId);
	},
	methods: {
		fetchOrder(orderId) {
			axios
				.get(`/api/get-order-w-ID/${orderId}`)
				.then((response) => {
					const orderData = response.data;
					const items = JSON.parse(orderData.items); //parse json to match items
					orderData.items = items.map((item) => ({
						name: item.name,
						quantity: item.quantity,
						id: item.id,
					}));
					this.order = orderData;
				})
				.catch((error) => {
					console.log(error.response.data);
				});
		},
	},
};
</script>
  
<style src="../../css/Details.css" scoped></style>