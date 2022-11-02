<template>
  <div class="actions">
    <a :href="staticStore.url.viewCart" class="btn btn-success"> View cart </a>
    <a :href="'#'" class="btn btn-cancel mt-2" @click.prevent.stop="cleanCart">
      Checkout
    </a>
  </div>
</template>

<script>
import { mapActions, mapState } from "pinia";
import { useCartStore } from '../store/cart'

export default {
  computed: {
    ...mapState(useCartStore, ["staticStore"]),
  },
  methods: {
    ...mapActions(useCartStore, ["cleanCart"]),
  },
};
</script>

<style scoped></style>
