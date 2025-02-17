<script setup>
import tradingChart from '@/Components/tradingChart.vue';

defineProps({
    symbol: {
        type: String,
        default: 'AAPL',
    },
});
</script>

<template>
    <div class="p-8">
        <tradingChart :symbol="symbol" />
    </div>
</template>