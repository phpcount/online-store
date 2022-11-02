import axios from "axios";
import { StatusCodes } from "http-status-codes";
import { unbindProperty } from "../../../../utils/helper";
import { HydraResponse } from "../../../../utils/hydra-response";
import { concatUrlByParams } from "../../../../utils/url-generator";
import { API_CONFIG, API_CONFIG_PATCH } from "../../utils/config";
import { defineStore } from "pinia"

function getAlertStructure() {
  return {
    message: null,
    type: null,
  };
}

export const useCartStore = defineStore('cart', {
  state: () => ({
    cart: {},
    staticStore: {
      url: {
        apiCart: window.staticStore.urlApiCart,
        apiCartProducts: window.staticStore.urlApiCartProducts,
        apiOrder: window.staticStore.urlApiOrder,
        viewProduct: window.staticStore.urlViewProduct,
        assetImageProducts: window.staticStore.urlAssetImageProducts,
      },
    },
    isSentForm: false,

    alert: getAlertStructure(),
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
    async getCart(callback) {
      const url = this.staticStore.url.apiCart;

      const result = await axios.get(url, API_CONFIG);

      if (result.status === StatusCodes.OK && result?.data) {
        const handlerHydra = new HydraResponse(result.data);

        const members = handlerHydra.getMember();

        let isSuccess = false;

        if (members.length) {
          this.cart =  unbindProperty(members[0]);
          isSuccess = true;
        } else {
          this.cart =  unbindProperty({});
          this.setAlert("warning", "Your cart is empty");
        }

        if (typeof callback === "function") {
          callback(isSuccess);
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
        this.getCart();
        this.setAlert(...getAlertStructure());
      }
    },

    async updateCartProductQuantity(params) {
      const url = concatUrlByParams(
        this.staticStore.url.apiCartProducts,
        params.cartProductId
      );

      const payload = {
        quantity: parseInt(params.quantity),
      };

      const result = await axios.patch(url, payload, API_CONFIG_PATCH);

      if (result.status === StatusCodes.OK) {
        this.getCart();
        this.setAlert("success", "Your cart updated.");
      }
    },

    async cleanCart() {
      const url = concatUrlByParams(this.staticStore.url.apiCart, this.cart.id);

      const result = await axios.delete(url, API_CONFIG);

      if (result.status === StatusCodes.NO_CONTENT) {
        this.cart =  unbindProperty({});
        this.getCart();
      }
    },

    async createOrder() {
      const url = this.staticStore.url.apiOrder;

      const payload = {
        cartId: this.cart.id,
      };

      const result = await axios.post(url, payload, API_CONFIG);

      if (result.status === StatusCodes.CREATED) {
        this.isSentForm = true;
        this.cleanCart();
        this.setAlert("success", "Thank you for your purchase! Our manager will contact with you in 24 hours.");
      }
    },

    setAlert(type, message) {
      this.alert = unbindProperty({ type, message })
    }
  }
})
