import Vue from "vue";
import App from "./App.vue";
import store from "./store";

const vueMenuCartInstance = new Vue({
  el: "#app-menu-cart",
  store,
  render: (h) => h(App),
});


window.vueMenuCartInstance = {
  addCartProduct: (productData) =>
    vueMenuCartInstance.$store.dispatch("cart/addCartProduct", productData),
};
