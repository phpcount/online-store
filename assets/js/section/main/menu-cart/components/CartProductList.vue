<template>
  <div class="products">
    <cart-product-item
      v-for="cartProduct in cart.cartProducts"
      :key="cartProduct.id"
      :cart-product="cartProduct"
    />
  </div>
</template>

<script>
import { mapState } from "vuex";
import CartProductItem from "./CartProductItem.vue";

export default {
  components: { CartProductItem },
  computed: {
    ...mapState("cart", ["cart"]),
  },
};
</script>
