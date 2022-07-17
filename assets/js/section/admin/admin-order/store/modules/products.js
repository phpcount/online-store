import axios from "axios";
import { StatusCodes } from "http-status-codes";
import { unbindProperty } from "../../../../../utils/helper";
import { handlerHydraResponse } from "../../../../../utils/hydra-response";
import {
  concatUrlByParams,
  getUrlProductsByCategory,
} from "../../../../../utils/url-generator";
import { API_CONFIG } from "../../../../main/utils/config";

const state = () => ({
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
});

const getters = {
  freeCategoryProducts(state) {
    return state.categoryProducts.filter(
      (item) => !state.busyProductsIds[item.uuid]
    );
  },
};

const actions = {
  async getOrderProducts({ commit, state }) {
    const url = concatUrlByParams(
      state.staticStore.url.apiOrder,
      state.staticStore.orderId
    );

    const result = await axios.get(url, API_CONFIG);

    if (result.status == StatusCodes.OK && result?.data) {
      commit("setOrderProducts", result.data.orderProducts);
      commit("setBusyProductsIds");
    }
  },
  async getCategories({ commit, state }) {
    const url = state.staticStore.url.apiCategory;

    const result = await axios.get(url, API_CONFIG);

    if (result.status == StatusCodes.OK && result?.data) {
      const handlerHydra = handlerHydraResponse(result.data);
      commit("setCategories", handlerHydra.getMember());
    }
  },
  async getProductsByCategory({ commit, state }) {
    const url = getUrlProductsByCategory(
      state.staticStore.url.apiProduct,
      state.newOrderProduct.categoryId,
      1,
      state.productCountLimit
    );

    const result = await axios.get(url, API_CONFIG);

    if (result.status == StatusCodes.OK && result?.data) {
      const handlerHydra = handlerHydraResponse(result.data);
      commit("setCategoryProducts", handlerHydra.getMember());
    }
  },
  async addNewOrderProduct({ state, dispatch }) {
    const url = state.staticStore.url.apiOrderProduct;

    const data = {
      appOrder: "/api/orders/" + state.staticStore.orderId,
      product: "/api/products/" + state.newOrderProduct.productId,
      quantity: parseInt(state.newOrderProduct.quantity),
      pricePerOne: state.newOrderProduct.pricePerOne,
    };

    console.log({ data });
    const result = await axios.post(url, data, API_CONFIG);

    if (result.status == StatusCodes.CREATED && result?.data) {
      // const handlerHydra = handlerHydraResponse(result.data);
      // const members = handlerHydra.getMember();
      dispatch("getOrderProducts");
    }
  },
  async removeOrderProduct({ state, dispatch }, orderProductId) {
    const url = concatUrlByParams(
      state.staticStore.url.apiOrderProduct,
      orderProductId
    );

    const result = await axios.delete(url, API_CONFIG);

    if (result.status === StatusCodes.NO_CONTENT) {
      console.info("Deleted");
      dispatch("getOrderProducts");
    }

    return result;
  },
};

const mutations = {
  setCategories(state, categories) {
    state.categories = categories;
  },
  setCategoryProducts(state, categoryProducts) {
    state.categoryProducts = categoryProducts;
  },
  setNewProductInfo(state, formData) {
    state.newOrderProduct = unbindProperty(formData);
  },
  setOrderProducts(state, orderProducts) {
    state.orderProducts = orderProducts;
  },
  setBusyProductsIds(state) {
    // state.busyProductsIds = state.orderProducts.map(item => item.product.id);
    state.busyProductsIds = state.orderProducts.reduce(
      (obj, item) => ((obj[item.product.uuid] = item.product.id), obj),
      {}
    );
  },
  pushCategoryProduct(state, newCategoryProduct) {
    state.categoryProducts.push(newCategoryProduct);
  },
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
