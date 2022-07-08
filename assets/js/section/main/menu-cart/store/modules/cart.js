import axios from "axios";
import { StatusCodes } from "http-status-codes";
import { unbindProperty } from "../../../../../utils/helper";
import { handlerHydraResponse, HydraResponse } from "../../../../../utils/hydra-response";
import { concatUrlByParams, getUrlProductsByCategory } from "../../../../../utils/url-generator";
import { API_CONFIG, API_CONFIG_PATCH } from "../../../../main/utils/config";


const state = () => ({
    cart: {},

    staticStore: {
        url: {
            apiCart: window.staticStore.urlApiCart,
            apiCartProducts: window.staticStore.urlApiCartProducts,
            apiProduct: window.staticStore.urlApiProduct,
            viewCart: window.staticStore.urlViewCart,
            viewProduct: window.staticStore.urlViewProduct,
            assetImageProducts: window.staticStore.urlAssetImageProducts
        }
    },
});

const getters = {
    totalPrice(state) {
        if (!state.cart.cartProducts?.length) {
            return 0;
        }

        return state.cart.cartProducts.reduce((prev, cur) => {
            return prev + (cur.quantity * cur.product.price);
        }, 0);
    }
}; 

const actions = {
    async getCart({ state, commit, dispatch }) {
        const url = state.staticStore.url.apiCart;

        const result = await axios.get(url, API_CONFIG);

        if (result.status === StatusCodes.OK && result?.data) {
            const handlerHydra = new HydraResponse(result.data);
            const members = handlerHydra.getMember();
            
            if (members.length) {
                commit('setCart', members[0]);
            } else {
                dispatch('createCart');
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
            dispatch('getCart');
        }
    },

    async cleanCart({ state, commit, dispatch }) {
        const url = concatUrlByParams(
            state.staticStore.url.apiCart,
            state.cart.id
        );

        const result = await axios.delete(url, API_CONFIG);

        if (result.status === StatusCodes.NO_CONTENT) {
            commit('setCart', {});
            dispatch('createCart');
        }
    },

    async addCartProduct({ state, dispatch }, productData) {
        if (!productData.quantity) {
            productData.quantity = 1;
        }

        const existCartProduct = state.cart.cartProducts.find(
            item => item.product.uuid === productData.uuid
        );

        if (existCartProduct) {
            dispatch('addExistCartProduct', {
                cartProductId: existCartProduct.id,
                quantity: existCartProduct.quantity + productData.quantity 
            });
        } else {
            dispatch('addNewCartProduct', productData);
        }
    },

    async addNewCartProduct({ state, dispatch }, productData) {
        const url = state.staticStore.url.apiCartProducts;

        const data = {
            cart: `${state.staticStore.url.apiCart}/${state.cart.id}`,
            product: `${state.staticStore.url.apiProduct}/${productData.uuid}`,
            quantity: productData.quantity
        };

        const result = await axios.post(url, data, API_CONFIG);

        if (result.status === StatusCodes.CREATED && result?.data) {
            dispatch('getCart');
        }
    },
    async addExistCartProduct({ state, dispatch }, cartProductData) {
        const url = concatUrlByParams(
            state.staticStore.url.apiCartProducts,
            cartProductData.cartProductId
        );

        const data = {
            quantity: cartProductData.quantity
        };

        const result = await axios.patch(url, data, API_CONFIG_PATCH);

        if (result.status === StatusCodes.OK && result?.data) {
            dispatch('getCart');
        }
    },
    async createCart({ state, dispatch }) {
        const url = state.staticStore.url.apiCart;
        const data = {};
        const result = await axios.post(url, data, API_CONFIG);

        if (result.status === StatusCodes.CREATED && result?.data) {
            dispatch('getCart');
        }

    }
};

const mutations = {
    setCart(state, cart) {
        state.cart = unbindProperty(cart);
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}