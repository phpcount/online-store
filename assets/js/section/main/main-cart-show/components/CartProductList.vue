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
import { mapActions, mapState } from "vuex";
import CartProductItem from "./CartProductItem.vue";

export default {
    components: { CartProductItem },
    computed: {
        ...mapState("cart", ["cart"]),
        
    }
}
</script>

<style scoped>

</style>