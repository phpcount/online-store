import axios from "axios";
import { StatusCodes } from "http-status-codes";
import { unbindProperty } from "../../../../utils/helper";
import { handlerHydraResponse } from "../../../../utils/hydra-response";
import {
  concatUrlByParams,
  getUrlProductsByCategory,
} from "../../../../utils/url-generator";
import { API_CONFIG } from "../../../main/utils/config";
import { defineStore } from "pinia"


export const useProductsStore = defineStore('products', {
  state: () => ({
    categories: [],
    categoryProducts: [],
    orderProducts: [],
    busyProductsIds: {},
    newOrderProduct: {
      categoryId: null,
      productId: null,
      quantity: null,
      pricePerOne: null,
    },

    staticStore: {
      orderId: window.staticStore.orderId,
      url: {
        apiOrder: window.staticStore.urlApiOrder,
        viewProduct: window.staticStore.urlViewProduct,
        apiProduct: window.staticStore.urlApiProduct,
        apiOrderProduct: window.staticStore.urlApiOrderProduct,
        apiCategory: window.staticStore.urlApiCategory,
      },
    },

    productCountLimit: 25,
  }),

  getters: {
    freeCategoryProducts(state) {
      return state.categoryProducts.filter(
        (item) => !state.busyProductsIds[item.uuid]
      );
    },
  },
  actions: {
    async getOrderProducts() {
      const url = concatUrlByParams(
        this.staticStore.url.apiOrder,
        this.staticStore.orderId
      );

      const result = await axios.get(url, API_CONFIG);

      if (result.status == StatusCodes.OK && result?.data) {
        this.setOrderProducts(result.data.orderProducts);
        this.setBusyProductsIds();
      }
    },
    async getCategories() {
      const url = this.staticStore.url.apiCategory;

      const result = await axios.get(url, API_CONFIG);

      if (result.status == StatusCodes.OK && result?.data) {
        const handlerHydra = handlerHydraResponse(result.data);
        this.setCategories(handlerHydra.getMember());
      }
    },
    async getProductsByCategory() {
      const url = getUrlProductsByCategory(
        this.staticStore.url.apiProduct,
        this.newOrderProduct.categoryId,
        1,
        this.productCountLimit
      );

      const result = await axios.get(url, API_CONFIG);

      if (result.status == StatusCodes.OK && result?.data) {
        const handlerHydra = handlerHydraResponse(result.data);
        this.setCategoryProducts(handlerHydra.getMember());
      }
    },
    async addNewOrderProduct() {
      const url = this.staticStore.url.apiOrderProduct;

      const data = {
        appOrder: "/api/v1/orders/" + this.staticStore.orderId,
        product: "/api/v1/products/" + this.newOrderProduct.productId,
        quantity: parseInt(this.newOrderProduct.quantity),
        pricePerOne: Number(this.newOrderProduct.pricePerOne).toString(),
      };

      console.log(data);
      const result = await axios.post(url, data, API_CONFIG);

      if (result.status == StatusCodes.CREATED && result?.data) {
        // const handlerHydra = handlerHydraResponse(result.data);
        // const members = handlerHydra.getMember();
        this.getOrderProducts();
      }
    },
    async removeOrderProduct(orderProductId) {
      const url = concatUrlByParams(
        this.staticStore.url.apiOrderProduct,
        orderProductId
      );

      const result = await axios.delete(url, API_CONFIG);

      if (result.status === StatusCodes.NO_CONTENT) {
        console.info("Deleted");
        this.getOrderProducts();
      }

      return result;
    },
    setCategories(categories) {
      this.categories = categories;
    },
    setCategoryProducts(categoryProducts) {
      this.categoryProducts = categoryProducts;
    },
    setNewProductInfo(formData) {
      this.newOrderProduct = unbindProperty(formData);
    },
    setOrderProducts(orderProducts) {
      this.orderProducts = orderProducts;
    },
    setBusyProductsIds() {
      this.busyProductsIds = this.orderProducts.reduce(
        (obj, item) => ((obj[item.product.uuid] = item.product.id), obj),
        {}
      );
    },
    pushCategoryProduct(newCategoryProduct) {
      this.categoryProducts.push(newCategoryProduct);
    }
  }
})
