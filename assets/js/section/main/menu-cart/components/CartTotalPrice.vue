<template>
    <div class="mb-2">
        <span>
            Total: <span class="total-price">{{ totalPrice }}</span>
        </span>
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
export default {
    computed: {
        ...mapGetters("cart", ["totalPrice"])
    }
}
</script>

<style scoped>

</style>