<template>
    <div class="dropdown cart-dropdown">
        <a 
          :href="staticStore.url.viewCart" 
          class="cart-dropdown-btn-toggle">
            <i class="fas fa-shopping-cart"></i>
            <span class="count">{{ countCartProducts }}</span>
        </a>

        <div class="dropdown-menu cart-dropdown-window">
            <cart-product-list/>
            <cart-total-price/>
            <div v-if="countCartProducts">
                <cart-actions/>
            </div>
            <div v-else class="text-center">
                Your cart is empty
            </div>
            
        </div>
    </div>
</template>
<script>

import { mapActions, mapMutations, mapState } from "vuex";
import CartTotalPrice from "./components/CartTotalPrice.vue";
import CartActions from "./components/CartActions.vue";
import CartProductList from './components/CartProductList.vue';

export default {
    components: { CartTotalPrice, CartActions, CartProductList },
    created() {
        this.getCart();
    },
    data() {
        return {
            
        }
    },
    computed: {
        ...mapState("cart", ["staticStore", "cart"]),
        countCartProducts() {
            if (!this.cart.cartProducts) {
                return 0;
            }

            return this.cart.cartProducts.length;
        }
    },
    methods: {
        ...mapActions("cart", ["getCart"])
    }
}
</script>

<style scoped>

</style>