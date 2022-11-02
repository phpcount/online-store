<template>
  <div class="row">
    <div class="col-lg-12 order-block">
      <div class="order-content">
        <div
          v-if="!isLoadedCart"
          class="spinner-grow"
          style="width: 4rem; height: 4rem"
          role="status"
        >
          <span class="sr-only">Loading...</span>
        </div>
        <alert />
        <div v-if="showCartContent">
          <cart-product-list />
          <hr />
          <cart-total-price />
          <a class="btn btn-success mb-3 text-white" @click.prevent="makeOrder">
            MAKE ORDER
          </a>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapActions, mapState } from "pinia";
import { useCartStore } from './store/cart'

import Alert from "./components/Alert.vue";
import CartProductList from "./components/CartProductList";
import CartTotalPrice from "./components/CartTotalPrice";

export default {
  components: { Alert, CartProductList, CartTotalPrice },
  data() {
    return {
      isLoadedCart: false,
    };
  },
  computed: {
    ...mapState(useCartStore, ["cart", "isSentForm"]),
    showCartContent() {
      return !this.isSentForm && Object.keys(this.cart).length;
    },
  },
  created() {
    this.isLoadedCart = false;
    this.getCart((isSuccess) => {
      if (isSuccess) {
        this.setAlert("info", "You can see your cart.");
      }

      this.isLoadedCart = true;
    });
  },
  methods: {
    ...mapActions(useCartStore, ["getCart", "createOrder", "setAlert"]),
    makeOrder() {
      this.createOrder();
    },
  },
};
</script>
