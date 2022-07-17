<template>
  <div class="row mb-2">
    <div class="col-md-2">
      <select
        v-model="form.categoryId"
        name="add_product_category_select"
        class="form-control"
        @change="getProducts"
      >
        <option value="" disabled>- choooce option -</option>
        <option
          v-for="category in categories"
          :key="category.id"
          :value="category.id"
        >
          {{ category.title }}
        </option>
      </select>
    </div>
    <div v-if="showAfterSelectCategory" class="col-md-3">
      <select
        v-model="form.productId"
        name="add_product_product_select"
        class="form-control"
      >
        <option value="" disabled>- choooce option -</option>
        <option
          v-for="categoryProduct in freeCategoryProducts"
          :key="categoryProduct.id"
          :value="categoryProduct.uuid"
        >
          {{ productTitle(categoryProduct) }}
        </option>
      </select>
    </div>
    <div v-if="showAfterSelectProduct" class="col-md-2">
      <input
        v-model="form.quantity"
        type="number"
        class="form-control"
        placeholder="quantity"
        min="1"
        :max="productQuantityMax"
      />
    </div>
    <div v-if="showAfterSelectProduct" class="col-md-2">
      <input
        v-model="form.pricePerOne"
        type="number"
        class="form-control"
        placeholder="price per one"
        step="0.01"
        min="1"
        :max="productPriceMax"
      />
    </div>
    <div v-if="showAfterSelectProduct" class="col-md-3">
      <button class="btn btn-outline-info" @click.prevent="viewDetails">
        Details
      </button>
      <button class="btn btn-outline-success" @click.prevent="addProduct">
        Add
      </button>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters, mapMutations, mapState } from "vuex";
import { unbindProperty } from "../../../../utils/helper";
import { getUrlViewProduct } from "../../../../utils/url-generator";
import { getProductInformativeTitle } from "../../../../utils/title-formatter";

export default {
  props: {},
  data() {
    return {
      form: {
        category: null,
        productId: null,
        quantity: null,
        pricePerOne: null,
      },
      selectedProduct: {},
    };
  },
  computed: {
    ...mapState("products", ["staticStore", "categories", "categoryProducts"]),
    ...mapGetters("products", ["freeCategoryProducts"]),

    showAfterSelectCategory() {
      return this.form.categoryId;
    },
    showAfterSelectProduct() {
      return this.showAfterSelectCategory && this.form.productId;
    },
    productQuantityMax() {
      return parseInt(this.getSelectedProduct().quantity) ?? "";
    },
    productPriceMax() {
      return this.getSelectedProduct().price ?? "";
    },
  },
  methods: {
    ...mapActions("products", ["getProductsByCategory", "addNewOrderProduct"]),
    ...mapMutations("products", ["setNewProductInfo"]),
    productTitle(product) {
      return getProductInformativeTitle(product);
    },
    getProducts() {
      this.setNewProductInfo(this.form);
      this.getProductsByCategory();
    },
    viewDetails() {
      const url = getUrlViewProduct(
        this.staticStore.url.viewProduct,
        this.form.productId
      );
      window.open(url, "_blank").focus();
    },
    addProduct() {
      this.setNewProductInfo(this.form);
      this.addNewOrderProduct();
      this.resetFormData();
    },
    resetFormData() {
      this.form = unbindProperty({});
    },
    getSelectedProduct() {
      if (!this.form.productId) {
        return;
      }

      if (this.selectedProduct.uuid === this.form.productId) {
        return this.selectedProduct;
      }

      this.selectedProduct = this.freeCategoryProducts.find(
        (item) => item.uuid === this.form.productId
      );

      return this.selectedProduct;
    },
  },
};
</script>
