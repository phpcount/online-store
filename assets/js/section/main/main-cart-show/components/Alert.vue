<template>
  <div v-if="alert.message" :class="alertClass">
    {{ alert.message }}
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useCartStore } from '../store/cart'
export default {
  computed: {
    ...mapState(useCartStore, ["alert"]),
    alertClass() {
      return `alert alert-${this.alert.type}`;
    },
  },
};
</script>

<style scoped></style>
