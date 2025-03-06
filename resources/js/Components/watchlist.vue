<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const watchlistItems = ref([
    { symbol: 'AAPL', price: '185.52', change: '+0.24%', color: 'text-green-500' },
    { symbol: 'MSFT', price: '420.71', change: '+0.41%', color: 'text-green-500' },
    { symbol: 'GOOGL', price: '156.28', change: '-0.12%', color: 'text-red-500' },
    { symbol: 'AMZN', price: '178.75', change: '+1.35%', color: 'text-green-500' },
    { symbol: 'TSLA', price: '247.36', change: '-2.11%', color: 'text-red-500' },
    { symbol: 'META', price: '476.54', change: '+0.89%', color: 'text-green-500' },
    { symbol: 'NVDA', price: '126.58', change: '+1.23%', color: 'text-green-500' },
    { symbol: 'C:GBPUSD', price: '1.23456', change: '+0.05%', color: 'text-green-500' },
    { symbol: 'C:GBPJPY', price: '187.456', change: '-0.12%', color: 'text-red-500' },
]);

const selectSymbol = (symbol) => {
    // Fetch the current URL params safely
    const params = new URLSearchParams(window.location.search);
    
    Inertia.get(route('Home'), {
        symbol: symbol,
        timespan: params.get('timespan') || 'day',
        timemultiplier: params.get('timemultiplier') || '1',
        chartType: params.get('chartType') || 'candlestick'
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['symbol', 'timespan', 'timemultiplier', 'chartType']
    });
};
</script>

<template>
    <div class="flex flex-col gap-2 w-full">
        <div class="flex flex-row w-full items-center justify-between text-white p-2 border-b border-gray-600 gap-4">
            <span class="w-max">Symbol</span>
            <div class="flex flex-col items-end">
                <span class="w-max">Last Price</span>
                <span class="w-max text-xs text-gray-400">Change</span>
            </div>
        </div>
        <div class="flex flex-col overflow-y-auto max-h-96">
            <button
                v-for="item in watchlistItems"
                :key="item.symbol"
                @click="selectSymbol(item.symbol)"
                class="flex items-center justify-between text-white p-2 border-b border-gray-600 hover:bg-gray-700/50 hover:border-opacity-0 gap-4 w-full">
                <span class="w-max font-medium">{{ item.symbol }}</span>
                <div class="flex flex-col items-end">
                    <span class="w-max">{{ item.price }}</span>
                    <span class="w-max text-xs" :class="item.color">{{ item.change }}</span>
                </div>
            </button>
        </div>
    </div>
</template>
