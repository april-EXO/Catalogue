<template>
	<div class="order">
		<router-link to="/">Go Back</router-link>
		<h2>Order Details</h2>
		<h3>Cart Items</h3>
		<div class="flex-container">
			<form @submit.prevent="submitOrder">
				<div class="cart-items">
					<div class="cart-items-content">
						<table class="cart-items-table">
							<tbody>
								<tr v-for="item in cartItems" :key="item.id">
									<td>{{ item.name }}</td>
									<td>{{ item.quantity }}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div class="cart-items-counter">Total Items: {{ getTotalItems() }}</div>

				<div class="form-group">
					<table class="form-table">
						<tr>
							<td><label for="name">Name</label></td>
							<td><input type="text" id="name" v-model="name" placeholder="Enter your name" required></td>
						</tr>
						<tr>
							<td><label for="address">Address</label></td>
							<td><input type="text" id="address" v-model="address" placeholder="Enter your address" required>
							</td>
						</tr>
						<tr>
							<td><label for="phone">Phone Number</label></td>
							<td><input type="text" id="phone" v-model="phone" placeholder="Enter your phone number"
									required></td>
						</tr>
						<tr>
							<td colspan="2"><button type="submit" class="submit-order" :disabled="!isFormValid">Submit
									Order</button></td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</template>
  
<script>
import axios from 'axios';

export default {
	data() {
		return {
			name: '',
			address: '',
			phone: '',
			cartItems: [],
			status: 'Submitted',
		};
	},

	mounted() {
		this.parseCartItems();
	},

	methods: {
		parseCartItems() {
			const cartItemsStr = this.$route.query.cartItems;
			if (cartItemsStr) {
				try {
					this.cartItems = JSON.parse(cartItemsStr);
				} catch (error) {
					console.error('Error parsing cart items:', error);
				}
			}
		},

		isFormValid() {
			// Form validation logic
			return this.name.trim() !== '' && this.address.trim() !== '' && this.phone.trim() !== '';
		},

		getTotalItems() {
			let total = 0;
			for (const item of this.cartItems) {
				total += item.quantity;
			}
			return total;
		},

		submitOrder() {
			if (!this.isFormValid()) {
				return;
			}

			const orderData = {
				name: this.name,
				address: this.address,
				phone: this.phone,
				cartItems: this.cartItems,
				status: this.status,
			};

			axios.post('api/submit-order', orderData)
				.then(response => {
					console.log(response.data);
					this.$router.push('/');
				})
				.catch(error => {
					console.error('Error submitting order:', error);
				});

		},

	},
};
</script>
  
  
<style src="../../css/Order.css" scoped></style>
  