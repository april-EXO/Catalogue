<template>
	<header class="header">
		<h1 class="title">April Catalogue</h1>
	</header>
</template>
  
<script>
export default {
	name: 'Header',
};
</script>
  
<style scoped>
.header {
	display: flex;
	align-items: center;
	justify-content: center;
	padding: 20px;
	background-color: #f9f9f9;
	width: 100%;
	margin-left: -20px;
	margin-right: -20px;
}

.title {
	font-family: 'cursive', sans-serif;
	font-size: 24px;
	font-weight: bold;
	color: #333;
}
</style>
  