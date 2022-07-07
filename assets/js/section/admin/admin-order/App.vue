<template>
    <div class="table-additional-selection">
            <order-product-add/>
        <hr/>
            <order-product-item
              v-for="(orderProduct, index) in orderProducts"
              :key="orderProduct.id"
              :order-product="orderProduct"
              :index="index"
            />
        <hr/>
        <order-total-price-block/>
    </div>
</template>
<script>

import { mapActions, mapState } from "vuex";
import OrderProductAdd from './components/OrderProductAdd.vue';
import OrderProductItem from "./components/OrderProductItem.vue";
import OrderTotalPriceBlock from './components/OrderTotalPriceBlock.vue';

export default {
    components: { OrderProductItem, OrderProductAdd, OrderTotalPriceBlock },
    created() {
        // console.log('create', ORDER_PRODUCTS);

        // const pageData = JSON.parse(document.getElementById("pageData").innerText);
        // console.log('create', pageData);

        // console.log('window.staticStore', window.staticStore);
        this.getCategories();
        this.getOrderProducts();
    },
    computed: {
        ...mapState("products", ["orderProducts"]),
    },
    methods: {
        ...mapActions("products", ["getCategories", "getOrderProducts"])
    }
}
</script>
