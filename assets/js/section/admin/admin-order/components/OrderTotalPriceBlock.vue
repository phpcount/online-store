<template>
  <div class="row mb-1">
    <div class="col-md-2 text-right font-weight-bold">Total price:</div>
    <div class="col-md-10">
      <span class="font-weight-bold"> ${{ totalPrice }} </span>
    </div>
  </div>
</template>

<script>
import { mapState } from "pinia";
import { useProductsStore } from '../store/products'
export default {
  computed: {
    ...mapState(useProductsStore, ["orderProducts"]),
    totalPrice() {
      return this.orderProducts.reduce(
        (prev, current) => prev + current.quantity * current.pricePerOne,
        0
      );
    },
  },
};
</script>

<style scoped></style>
