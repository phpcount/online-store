import axios from "axios";
import { StatusCodes } from "http-status-codes";
import { unbindProperty } from "../../../../utils/helper";
import { HydraResponse } from "../../../../utils/hydra-response";
import { concatUrlByParams } from "../../../../utils/url-generator";
import { API_CONFIG, API_CONFIG_PATCH } from "../../utils/config";
import { defineStore } from "pinia"

export const useCartStore = defineStore('cart', {
  state: () => ({
    cart: {},

    staticStore: {
      url: {
        apiCart: window.staticStore.urlApiCart,
        apiCartProducts: window.staticStore.urlApiCartProducts,
        apiProduct: window.staticStore.urlApiProduct,
        viewCart: window.staticStore.urlViewCart,
        viewProduct: window.staticStore.urlViewProduct,
        assetImageProducts: window.staticStore.urlAssetImageProducts,
      },
    },
  }),
  getters: {
    totalPrice(state) {
      if (!state.cart.cartProducts?.length) {
        return 0;
      }

      return state.cart.cartProducts.reduce((prev, cur) => {
        return prev + cur.quantity * cur.product.price;
      }, 0);
    },
  },
  actions: {
    async getCart() {
      const url = this.staticStore.url.apiCart;

      const result = await axios.get(url, API_CONFIG);

      if (result.status === StatusCodes.OK && result?.data) {
        const handlerHydra = new HydraResponse(result.data);
        const members = handlerHydra.getMember();

        if (members.length) {
          this.cart = unbindProperty(members[0]);
        } else {
          this.createCart();
        }
      }
    },

    async deleteCartProduct(cartProductId) {
      const url = concatUrlByParams(
        this.staticStore.url.apiCartProducts,
        cartProductId
      );

      const result = await axios.delete(url, API_CONFIG);

      if (result.status === StatusCodes.NO_CONTENT) {
        this.getCart()
      }
    },

    async cleanCart() {
      const url = concatUrlByParams(this.staticStore.url.apiCart, this.cart.id);

      const result = await axios.delete(url, API_CONFIG);

      if (result.status === StatusCodes.NO_CONTENT) {
        this.cart = {};
        this.createCart();
      }
    },

    async addCartProduct(productData) {
      if (!productData.quantity) {
        productData.quantity = 1;
      }

      const existCartProduct = this.cart.cartProducts.find(
        (item) => item.product.uuid === productData.uuid
      );

      if (existCartProduct) {
        this.addExistCartProduct({
          cartProductId: existCartProduct.id,
          quantity: existCartProduct.quantity + productData.quantity,
        });
      } else {
        this.addNewCartProduct(productData);
      }
    },

    async addNewCartProduct(productData) {
      const url = this.staticStore.url.apiCartProducts;

      const data = {
        cart: `${this.staticStore.url.apiCart}/${this.cart.id}`,
        product: `${this.staticStore.url.apiProduct}/${productData.uuid}`,
        quantity: productData.quantity,
      };

      const result = await axios.post(url, data, API_CONFIG);

      if (result.status === StatusCodes.CREATED && result?.data) {
        this.getCart();
      }
    },
    async addExistCartProduct(cartProductData) {
      const url = concatUrlByParams(
        this.staticStore.url.apiCartProducts,
        cartProductData.cartProductId
      );

      const data = {
        quantity: cartProductData.quantity,
      };

      const result = await axios.patch(url, data, API_CONFIG_PATCH);

      if (result.status === StatusCodes.OK && result?.data) {
        this.getCart();
      }
    },
    async createCart() {
      const url = this.staticStore.url.apiCart;
      const data = {};
      const result = await axios.post(url, data, API_CONFIG);

      if (result.status === StatusCodes.CREATED && result?.data) {
        this.getCart();
      }
    },
  },
})
