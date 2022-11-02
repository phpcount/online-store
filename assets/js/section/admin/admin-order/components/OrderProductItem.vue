<template>
  <div class="row mb-1">
    <div class="col-md-1 text-center">
      {{ rowNumber }}
    </div>
    <div class="col-md-2">
      {{ productTitle }}
    </div>
    <div class="col-md-2">
      {{ categoryTitle }}
    </div>
    <div class="col-md-2">x {{ orderProduct.quantity }}</div>
    <div class="col-md-2">${{ orderProduct.pricePerOne }}</div>
    <div class="col-md-3">
      <button class="btn btn-outline-info" @click.prevent="viewDetails">
        Details
      </button>
      <button class="btn btn-outline-danger" @click.prevent="remove">
        Remove
      </button>
    </div>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useProductsStore } from '../store/products'
import { getUrlViewProduct } from "../../../../utils/url-generator";
import { getProductInformativeTitle } from "../../../../utils/title-formatter";

export default {
  name: "OrderProductItem",
  props: {
    orderProduct: {
      type: Object,
      default: () => {},
    },
    index: {
      type: Number,
      default: 0,
    },
  },
  computed: {
    ...mapState(useProductsStore, ["staticStore"]),
    rowNumber() {
      return this.index + 1;
    },
    productTitle() {
      return getProductInformativeTitle(this.orderProduct.product);
      // return this.orderProduct.product.title;
    },
    categoryTitle() {
      return this.orderProduct.product.category.title;
    },
  },
  methods: {
    ...mapActions(useProductsStore, ["removeOrderProduct"]),
    viewDetails() {
      const url = getUrlViewProduct(
        this.staticStore.url.viewProduct,
        this.orderProduct.product.id
      );
      window.open(url, "_blank").focus();
    },
    remove() {
      this.removeOrderProduct(this.orderProduct.id);
    },
  },
};
</script>
