<template>
  <div class="product">
    <div class="product-details">
      <h4 class="product-title">
        <a :href="urlShowProduct" target="_blank">
          {{ cartProduct.product.title }}
        </a>
      </h4>

      <span class="product-info">
        <span class="product-quantity">
          {{ cartProduct.quantity }}
        </span>
        X ${{ cartProduct.product.price }}
      </span>
    </div>
    <figure class="product-image-container">
      <a :href="urlShowProduct" target="_blank">
        <img
          :src="getUrlProductImage(productImage)"
          :alt="cartProduct.product.title"
          class="product-image"
        />
      </a>
    </figure>
    <a
      href="#"
      class="btn btn-remove"
      title="Remove product"
      @click="removeCartProduct"
      >X</a
    >
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useCartStore } from '../store/cart'
import {
  concatUrlByParams,
  getUrlViewProduct,
} from "../../../../utils/url-generator";

export default {
  props: {
    cartProduct: {
      type: Object,
      default: () => {},
    },
  },
  computed: {
    ...mapState(useCartStore, ["staticStore"]),
    urlShowProduct() {
      return getUrlViewProduct(
        this.staticStore.url.viewProduct,
        this.cartProduct.product.uuid
      );
    },
    productImage() {
      const productImages = this.cartProduct.product.productImages;

      return Array.isArray(productImages) && productImages.length
        ? productImages[0]
        : null;
    },
  },
  methods: {
    ...mapActions(useCartStore, ["deleteCartProduct"]),
    removeCartProduct() {
      this.deleteCartProduct(this.cartProduct.id);
    },
    getUrlProductImage(productImage) {
      return concatUrlByParams(
        this.staticStore.url.assetImageProducts,
        this.cartProduct.product.id,
        productImage.filenameSmall
      );
    },
  },
};
</script>
