<script setup>
import { ref, onMounted } from 'vue';
import { createChart, CandlestickSeries } from 'lightweight-charts';
import axios from 'axios';

const props = defineProps({
    symbol: {
        type: String,
        required: true,
    },
});

const chartContainer = ref(null);

onMounted(async () => {
    if (!chartContainer.value) return;

    const chart = createChart(chartContainer.value, {
        autoSize: true,
        layout: {
            textColor: '#ffffff7F',
            background: {type: 'solid', color: '#00000000'},
        },
        grid: {
            vertLines: {
                color: '#eeeeee7F',
            },
            horzLines: {
                color: '#eeeeee7F',
            },
        },
        timeScale: {
            timeVisible: true,
            secondsVisible: false,
        },
    });

    const response = await axios.get(route('marketData.fetch', { symbol: props.symbol }));

    const data = response.data.results;

    const candlestickSeries = chart.addSeries(CandlestickSeries);
    candlestickSeries.setData(data.map(item => ({
        time: item.t / 1000, // Convert to UNIX timestamp
        open: item.o,
        high: item.h,
        low: item.l,
        close: item.c,
    })));

    chart.timeScale().fitContent();
});
</script>

<template>
    <div ref="chartContainer" class="h-full w-full min-w-full min-h-full"></div>
</template>