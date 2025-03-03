<script setup>
import { ref, onMounted, computed } from 'vue';
import { createChart, CandlestickSeries } from 'lightweight-charts';
import axios from 'axios';

const props = defineProps({
    symbol: {
        type: String,
        required: true,
    },
    timespan: {
        type: String,
        default: 'minute',
    },
    timemultiplier: {
        type: String,
        default: '5',
    },
});

let lableTime = ref(null);

switch (props.timespan) {
    case 'hour':
        lableTime.value = 'H';
        break;
    case 'day':
        lableTime.value = 'D';
        break;
    case 'week':
        lableTime.value = 'W';
        break;
    case 'month':
        lableTime.value = 'M';
        break;
    default:
        lableTime.value = '';
}

const chartContainer = ref(null);
const data = ref([]);

const openLast = computed(() => (data.value.length ? data.value[data.value.length - 1].o : "-"));
const closeLast = computed(() => (data.value.length ? data.value[data.value.length - 1].c : "-"));
const highLast = computed(() => (data.value.length ? data.value[data.value.length - 1].h : "-"));
const lowLast = computed(() => (data.value.length ? data.value[data.value.length - 1].l : "-"));

onMounted(async () => {
    if (!chartContainer.value) return;

    const chart = createChart(chartContainer.value, {
        autoSize: false,
        layout: {
            textColor: '#ffffff7F',
            background: { type: 'solid', color: '#0a0a0a' },
        },
        grid: {
            vertLines: { color: '#4f4f4f7F' },
            horzLines: { color: '#4f4f4f7F' },
        },
        timeScale: { timeVisible: true, secondsVisible: false },
    });

    try {
        const response = await axios.get(route('marketData.fetch', { symbol: props.symbol, timespan: props.timespan, timemultiplier: props.timemultiplier }));
        data.value = response.data.results; // ✅ Update reactive `data.value`
        console.log("Fetched Data:", data.value);
    } catch (error) {
        console.error("Error fetching market data:", error);
    }


    if (data.value.length) {
        const candlestickSeries = chart.addSeries(CandlestickSeries, { 
            upColor: '#26a69a', downColor: '#ef5350', borderVisible: false, 
            wickUpColor: '#26a69a', wickDownColor: '#ef5350' 
        });
        candlestickSeries.setData(data.value.map(item => ({
            time: item.t / 1000,
            open: item.o,
            high: item.h,
            low: item.l,
            close: item.c,
        })));

        chart.timeScale().fitContent();
    }
});
</script>

<template>
    <div class="h-full w-full flex flex-col rounded-md">
        <div ref="chartContainer" class="rounded-md w-full h-full relative">
            <div class="absolute top-1 left-1 bg-black bg-opacity-75 z-50 flex items-center justify-center rounded gap-3">
                <div
                    class="flex items-center space-x-1 text-body p-[0.15rem] rounded border border-black border-opacity-0 hover:bg-opacity-100 hover:bg-[#0f0f0f] hover:border-gray-400 hover:border-opacity-100">
                    <button class="text-gray-400 hover:bg-gray-700 rounded-sm px-1"> {{ props.symbol }} </button>
                    <span class="text-gray-400 select-none">•</span>
                    <button class="text-gray-400 hover:bg-gray-700 rounded-sm px-1"> {{ props.timemultiplier }}{{ lableTime }}</button>
                    <span class="text-gray-400 select-none">•</span>
                    <span class="text-gray-400 select-none">FXCM</span>
                </div>
                <div v-if="data.length" class="flex items-center space-x-2 text-sm p-[0.15rem]">
                    <span class="text-gray-400 select-none flex gap-[2px]">O<span class="text-violet-800">{{ openLast }}</span></span>
                    <span class="text-gray-400 select-none flex gap-[2px]">H<span class="text-violet-800">{{ highLast }}</span></span>
                    <span class="text-gray-400 select-none flex gap-[2px]">L<span class="text-violet-800">{{ lowLast }}</span></span>
                    <span class="text-gray-400 select-none flex gap-[2px]">C<span class="text-violet-800">{{ closeLast }}</span></span>
                </div>
            </div>
        </div>
    </div>
</template>
