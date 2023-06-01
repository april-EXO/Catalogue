<template>
	<div class="cart">
		<h2 class="cart-title">Cart</h2>
		<table class="cart-items-table">
			<tbody>
				<tr v-for="item in cartItems" :key="item.id" class="cart-item">
					<td>{{ item.name }} ({{ item.quantity }})</td>
					<td><button class="remove-button" @click="removeItem(item.id)">Remove</button></td>
				</tr>
			</tbody>
		</table>
		<div class="cart-actions">
			<button class="cart-button cart-close" @click="closeCart">Close</button>
			<button class="cart-button cart-order" @click="redirectToOrder">Order Now</button>
		</div>
	</div>
</template>
  
<script>
export default {
	props: { //data passed from catalogue
		cartItems: {
			type: Array,
			default: () => [], //if parent comp do not have value for cartItems, default will be empty
		},
	},
	methods: {
		closeCart() {
			this.$emit('close-cart'); //emit event in catalouge.vue
		},
		removeItem(itemId) {
			this.$emit('remove-item', itemId); //emit event in catalouge.vue
		},
		redirectToOrder() { //go order page
			const queryParams = { cartItems: JSON.stringify(this.cartItems) }; //parse cart items to json
			this.$router.push({ path: '/order', query: queryParams });//passing json string form cart items to order page
		},
	},
};
</script>
  
<style src="../../css/Cart.css" scoped></style>
  