<template>
  <table class="table table-cart table-mobile main-cart-show">
    <thead>
      <th>Product</th>
      <th>Price</th>
      <th>Quantity</th>
      <th>Total</th>
      <th></th>
    </thead>
    <tbody>
      <cart-product-item
        v-for="cartProduct in cart.cartProducts"
        :key="cartProduct.id"
        :cart-product="cartProduct"
      />
    </tbody>
  </table>
</template>

<script>
import { mapState } from "pinia";
import { useCartStore } from '../store/cart';
import CartProductItem from "./CartProductItem.vue";

export default {
  components: { CartProductItem },
  computed: {
    ...mapState(useCartStore, ["cart"]),
  },
};
</script>

<style scoped></style>
