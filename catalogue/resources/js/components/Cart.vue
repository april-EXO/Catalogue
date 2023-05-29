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
	props: {
		cartItems: {
			type: Array,
			default: () => [],
		},
	},
	methods: {
		closeCart() {
			this.$emit('close-cart');
		},
		removeItem(itemId) {
			this.$emit('remove-item', itemId);
		},
		redirectToOrder() {
			const queryParams = { cartItems: JSON.stringify(this.cartItems) };
			this.$router.push({ path: '/order', query: queryParams });
		},
	},
};
</script>
  
<style src="../../css/Cart.css" scoped></style>
  