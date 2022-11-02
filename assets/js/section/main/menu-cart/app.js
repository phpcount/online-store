import { createApp } from "vue";
import { createPinia } from 'pinia'
import App from "./App.vue";
import { useCartStore } from "./store/cart"

const pinia = createPinia()
const vueMenuCartInstance = createApp(App)
  .use(pinia)
  .mount("#app-menu-cart");


window.vueMenuCartInstance = {
  addCartProduct: (productData) => useCartStore().addCartProduct(productData)
};
