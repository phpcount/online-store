<template>
  <div class="mb-2">
    <span>
      Total: <span class="total-price">{{ totalPrice }}</span>
    </span>
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useCartStore } from '../store/cart'
export default {
  computed: {
    ...mapState(useCartStore, ["totalPrice"]),
  },
};
</script>

<style scoped></style>
