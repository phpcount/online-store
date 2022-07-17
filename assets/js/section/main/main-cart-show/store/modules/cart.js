import axios from "axios";
import { StatusCodes } from "http-status-codes";
import { unbindProperty } from "../../../../../utils/helper";
import { HydraResponse } from "../../../../../utils/hydra-response";
import { concatUrlByParams } from "../../../../../utils/url-generator";
import { API_CONFIG, API_CONFIG_PATCH } from "../../../../main/utils/config";

function getAlertStructure() {
  return {
    message: null,
    type: null,
  };
}
const state = () => ({
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
});

const getters = {
  totalPrice(state) {
    if (!state.cart.cartProducts?.length) {
      return 0;
    }

    return state.cart.cartProducts.reduce((prev, cur) => {
      return prev + cur.quantity * cur.product.price;
    }, 0);
  },
};

const actions = {
  async getCart({ state, commit }, callback) {
    const url = state.staticStore.url.apiCart;

    const result = await axios.get(url, API_CONFIG);

    if (result.status === StatusCodes.OK && result?.data) {
      const handlerHydra = new HydraResponse(result.data);

      const members = handlerHydra.getMember();

      let isSuccess = false;

      if (members.length) {
        commit("setCart", members[0]);
        isSuccess = true;
      } else {
        commit("setAlert", {
          type: "warning",
          message: "Your cart is empty",
        });
        commit("setCart", {});
      }

      if (typeof callback === "function") {
        callback(isSuccess);
      }
    }
  },

  async deleteCartProduct({ state, dispatch, commit }, cartProductId) {
    const url = concatUrlByParams(
      state.staticStore.url.apiCartProducts,
      cartProductId
    );

    const result = await axios.delete(url, API_CONFIG);

    if (result.status === StatusCodes.NO_CONTENT) {
      commit("cleanAlert");
      dispatch("getCart");
    }
  },

  async updateCartProductQuantity({ state, dispatch, commit }, params) {
    const url = concatUrlByParams(
      state.staticStore.url.apiCartProducts,
      params.cartProductId
    );

    const payload = {
      quantity: parseInt(params.quantity),
    };

    const result = await axios.patch(url, payload, API_CONFIG_PATCH);

    if (result.status === StatusCodes.OK) {
      dispatch("getCart");

      // commit('setAlert', { type: 'success', message: 'Your cart updated.' });
      commit("cleanAlert");
    }
  },

  async cleanCart({ state, dispatch, commit }) {
    const url = concatUrlByParams(state.staticStore.url.apiCart, state.cart.id);

    const result = await axios.delete(url, API_CONFIG);

    if (result.status === StatusCodes.NO_CONTENT) {
      commit("setCart", {});
      dispatch("getCart");
    }
  },

  async createOrder({ state, dispatch, commit }) {
    const url = state.staticStore.url.apiOrder;

    const payload = {
      cartId: state.cart.id,
    };

    const result = await axios.post(url, payload, API_CONFIG);

    if (result.status === StatusCodes.CREATED) {
      commit("setAlert", {
        type: "success",
        message:
          "Thank you for your purchase! Our manager will contact with you in 24 hours.",
      });
      commit("setIsSentForm", true);

      dispatch("cleanCart");
    }
  },
};

const mutations = {
  setCart(state, cart) {
    state.cart = unbindProperty(cart);
  },
  setAlert(state, data) {
    state.alert = unbindProperty(data);
  },
  cleanAlert(state) {
    state.alert = getAlertStructure();
  },
  setIsSentForm(state, val) {
    state.isSentForm = val;
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
