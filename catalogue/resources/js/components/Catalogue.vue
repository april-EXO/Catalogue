<template>
	<div class="catalogue-page">
		<Header />
		<div class="upper-section">
			<div class="search-container">
				<button @click="toggleCart" class="cart-button">
					Cart <span class="cart-count">{{ cartItems.length }}</span>
				</button>
				<button @click="redirectToOrderList" class="cart-button">Order(s)</button>
				<input type="text" v-model="searchQuery" @input="searchProducts" placeholder="Search products" />
				<select v-model="order" @change="toggleOrder">
					<option value="default" selected disabled>Default</option>
					<option value="asc">Ascending (A-Z)</option>
					<option value="desc">Descending (Z-A)</option>
				</select>
				<button @click="addToCart" class="cart-button">Add to Cart</button>
				<span v-if="hasDeliveryOrder"><img class="delivery-icon" src="./bell.png" alt="Delivery Icon"
						@click="redirectToOrderList" /></span>
			</div>

		</div>
		<table class="catalogue-table">
			<thead>
				<tr>
					<th>#</th>
					<th>Item Name</th>
					<th></th>
					<th>Quantity</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(item, index) in displayedItems" :key="item.id" class="item-row">
					<td>{{ (currentPage - 1) * pageSize + index + 1 }}</td>
					<td>{{ item.name }}</td>
					<td>
						<label>
							<input type="checkbox" v-model="item.checked" @change="toggleQuantitySelector(item)" />
						</label>
					</td>
					<td v-if="item.checked">
						<div class="quantity-input">
							<button @click="decrementQuantity(item)">-</button>
							<input type="number" v-model="item.quantity" min="1" max="99" />
							<button @click="incrementQuantity(item)">+</button>
						</div>
					</td>
					<td v-else></td>
				</tr>
			</tbody>
		</table>
		<div class="pagination">
			<button :disabled="currentPage === 1" @click="prevPage">Previous</button>
			<span class="pagination-info">{{ currentPage }} / {{ totalPages }}</span>
			<button :disabled="currentPage === totalPages" @click="nextPage">Next</button>
		</div>
		<Cart :cartItems="cartItems" @remove-item="removeFromCart" v-if="showCart" @close-cart="toggleCart" />
	</div>
</template>

  
<script>
import axios from 'axios';
import Header from './Header.vue';
import Cart from './Cart.vue';

export default {
	components: {
		Header,
		Cart,
	},
	data() {
		return {
			items: [],
			displayedItems: [],
			currentPage: 1,
			totalPages: 0,
			pageSize: 10,
			searchQuery: '',
			order: 'default',
			showCart: false,
			cartItems: [],
			hasDeliveryOrder: false,
		};
	},

	mounted() {
		this.fetchProductsFromCache(); // get products data from cache 
		this.fetchCartFromCache(); // get cart items data from cache
		this.fetchProducts();
		this.fetchOrderFromCache(); // get order data from cache
	},

	methods: {

		fetchProductsFromCache() { //get Product list from cache
			const cachedData = localStorage.getItem('catalogueData');
			if (cachedData) {
				try {
					const { items, cartItems } = JSON.parse(cachedData);
					this.items = items;
					this.cartItems = cartItems;
					this.totalPages = Math.ceil(this.items.length / this.pageSize);
					this.updateDisplayedItems();
				} catch (error) {
					console.error('Error parsing cached data:', error);
				}
			}
		},

		fetchOrderFromCache() { //get cached In delivery order validity
			const cachedData = localStorage.getItem('orderData');
			if (cachedData) {
				try {
					const orders = JSON.parse(cachedData);
					this.addStatusToItems(orders);
					this.hasDeliveryOrder = orders.some(order => order.status === 'In Delivery');
				} catch (error) {
					console.error('Error parsing cached order data:', error);
				}
			}
		},


		fetchProducts() { //fetch products & orders from API & db
			axios
				.get('products') //get product list from the given api
				.then((response) => {
					const products = response.data;
					this.items = products.map((item) => ({
						...item, //copy existing properties
						checked: false,
						quantity: 0,
					}));
					this.totalPages = Math.ceil(products.length / this.pageSize);
					this.updateDisplayedItems();
					this.cacheData(); // Cache the fetched data
				})
				.catch((error) => {
					console.error('Error fetching data:', error);
				});

			axios
				.get('/api/get-order') //get from database - order to check any In Delivery item
				.then(response => {
					const orders = response.data;
					this.addStatusToItems(orders);
					this.cacheOrderData(orders); // Cache the fetched order data
					this.hasDeliveryOrder = orders.some(order => order.status === 'In Delivery');
				})
				.catch(error => {
					console.error('Error fetching order data:', error);
				});
		},

		cacheData() { //store products and cart item to cache
			const dataToCache = {
				items: this.items,
				cartItems: this.cartItems,
			};
			localStorage.setItem('catalogueData', JSON.stringify(dataToCache));
		},

		fetchCartFromCache() { //get cached cartItems
			const cartData = localStorage.getItem('cartItems');
			if (cartData) {
				this.cartItems = JSON.parse(cartData);
			}
		},

		cacheCartData() {
			localStorage.setItem('cartItems', JSON.stringify(this.cartItems));
		},

		cacheOrderData(orders) {
			localStorage.setItem('orderData', JSON.stringify(orders));
		},

		updateDisplayedItems() {
			const startIndex = (this.currentPage - 1) * this.pageSize;
			const endIndex = startIndex + this.pageSize;
			this.displayedItems = this.items.slice(startIndex, endIndex); //extract subset of items
		},

		prevPage() {
			if (this.currentPage > 1) {
				this.currentPage--;
				this.updateDisplayedItems();
			}
		},

		nextPage() {
			if (this.currentPage < this.totalPages) {
				this.currentPage++;
				this.updateDisplayedItems();
			}
		},

		toggleOrder() { //sorting items alphabetical order
			if (this.order === 'asc') {
				this.items.sort((a, b) => a.name.localeCompare(b.name));
			} else if (this.order === 'desc') {
				this.items.sort((a, b) => b.name.localeCompare(a.name));
			}
			this.currentPage = 1;
			this.updateDisplayedItems();
		},

		searchProducts() {
			clearTimeout(this.searchTimeout); //clear previous search timeout 300ms
			this.searchTimeout = setTimeout(() => {
				if (this.searchQuery) {
					const filteredItems = this.items.filter((item) =>
						item.name.toLowerCase().includes(this.searchQuery.toLowerCase())
					);
					this.totalPages = Math.ceil(filteredItems.length / this.pageSize);
					this.currentPage = 1;
					this.displayedItems = filteredItems.slice(0, this.pageSize);
				} else {
					this.totalPages = Math.ceil(this.items.length / this.pageSize);
					this.currentPage = 1;
					this.updateDisplayedItems();
				}
			}, 300);
		},

		incrementQuantity(item) { // + button
			if (item.quantity < 99) {
				item.quantity++;
			}
		},

		decrementQuantity(item) { // - button
			if (item.quantity > 1) {
				item.quantity--;
			}
		},

		addToCart() {
			const selectedItems = this.items.filter(item => item.checked && item.quantity > 0);
			const newCartItems = selectedItems.map(item => ({
				id: item.id,
				name: item.name,
				quantity: item.quantity,
			}));
			this.cartItems = [...this.cartItems, ...newCartItems]; //add new items to old added items
			selectedItems.forEach(item => {
				item.checked = false;
				item.quantity = 0;
			}); //reset the checked item
			this.cacheCartData(); // cache the updated cart data
			console.log(this.cartItems);
		},

		removeFromCart(itemId) {
			this.cartItems = this.cartItems.filter((item) => item.id !== itemId);
			this.cacheData(); // update the cached cart data
			this.cacheOrderData(this.orders); // update the cached order data
			console.log(this.cartItems);
		},


		toggleCart() {
			this.showCart = !this.showCart;
		},

		toggleQuantitySelector(item) {
			if (!item.checked) {
				item.quantity = 0;
			} else {
				item.quantity = 1;
			}
		},

		redirectToOrderList() {
			this.$router.push('/listview');
		},

		addStatusToItems(orders) {
			for (const item of this.items) {
				const orderWithStatus = orders.find((order) => order.itemId === item.id && order.status === 'In Delivery');
				item.hasDeliveryIcon = !!orderWithStatus;
			}
		},

	},
};
</script>
  
  
  
  
<style src="../../css/Catalogue.css" scoped></style>
