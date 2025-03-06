<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia'; // Make sure to import Inertia
import Axios from 'axios';

const props = defineProps({
    chartType: {
        type: String,
        default: 'candlestick'
    },
    symbol: {
        type: String,
        required: true
    },
    timespan: {
        type: String,
        required: true
    },
    timemultiplier: {
        type: String,
        required: true
    }
});

// Modify changeChartType to use Inertia.visit for navigation
const changeChartType = (chartType) => {
    console.log("changing to: ", chartType);

    // Use Inertia to navigate, passing the chartType as part of the URL
    Inertia.visit(route('Home', {
        symbol: props.symbol,
        timespan: props.timespan,
        timemultiplier: props.timemultiplier,
        chartType: chartType
    }));
};
</script>

<template>
    <div class="flex items-center h-full">
        <!-- Candlestick Button -->
        <button @click="changeChartType('candlestick')" 
            :class="[props.chartType === 'candlestick' ? 'text-blue-500' : 'text-white', 'w-min h-min flex items-center py-2 justify-center hover:bg-gray-700/50 rounded text-sm']">
            <svg class="size-7" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 28" width="28" height="28" fill="currentColor">
                <path d="M17 11v6h3v-6h-3zm-.5-1h4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .5-.5z"></path>
                <path d="M18 7h1v3.5h-1zm0 10.5h1V21h-1z"></path>
                <path d="M9 8v12h3V8H9zm-.5-1h4a.5.5 0 0 1 .5.5v13a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1-.5-.5v-13a.5.5 0 0 1 .5-.5z"></path>
                <path d="M10 4h1v3.5h-1zm0 16.5h1V24h-1z"></path>
            </svg>
        </button>

        <!-- Line Chart Button -->
        <button @click="changeChartType('line')" 
            :class="[props.chartType === 'line' ? 'text-blue-500' : 'text-white', 'w-min h-min flex items-center py-2 justify-center hover:bg-gray-700/50 rounded text-sm']">
            <svg class="size-7" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 28" width="28" height="28">
                <path fill="currentColor" d="m25.39 7.31-8.83 10.92-6.02-5.47-7.16 8.56-.76-.64 7.82-9.36 6 5.45L24.61 6.7l.78.62Z"></path>
            </svg>
        </button>
    </div>
</template>
