<template>
  <tr>
    <td class="product-col">
      <div class="text-center">
        <figure>
          <a :href="urlShowProduct" target="_blank">
            <img
              :src="getUrlProductImage(productImage)"
              :alt="cartProduct.product.title"
            />
          </a>
        </figure>
        <div class="product-title">
          <a :href="urlShowProduct" target="_blank">
            {{ cartProduct.product.title }}
          </a>
        </div>
      </div>
    </td>
    <td class="price-col">${{ cartProduct.product.price }}</td>
    <td class="quantity-col">
      <input
        v-model="quantity"
        type="number"
        class="form-control"
        min="1"
        step="1"
        data-decimals="0"
        required
        @focusout="updateQuantity"
      />
    </td>
    <td class="total-col">${{ productPrice }}</td>
    <td class="remove-col">
      <a
        href="#"
        class="btn btn-outline-danger rounded-circle btn-remove"
        title="Remove product"
        @click="removeCartProduct"
        >X</a
      >
    </td>
  </tr>
</template>

<script>
import { mapActions, mapState } from "vuex";
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
  data() {
    return {
      quantity: 1,
    };
  },
  computed: {
    ...mapState("cart", ["staticStore"]),
    urlShowProduct() {
      return getUrlViewProduct(
        this.staticStore.url.viewProduct,
        this.cartProduct.product.uuid
      );
    },
    productPrice() {
      return this.quantity * this.cartProduct.product.price;
    },
    productImage() {
      const productImages = this.cartProduct.product.productImages;

      return Array.isArray(productImages) && productImages.length
        ? productImages[0]
        : null;
    },
  },
  created() {
    this.quantity = this.cartProduct.quantity;
  },
  methods: {
    ...mapActions("cart", ["deleteCartProduct", "updateCartProductQuantity"]),
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
    updateQuantity() {
      this.updateCartProductQuantity({
        cartProductId: this.cartProduct.id,
        quantity: this.quantity,
      });
    },
  },
};
</script>

<style scoped>
.price-col {
  width: 115px;
}
.quantity-col {
  width: 115px;
}
.total-col {
  min-width: 115px;
}
</style>
