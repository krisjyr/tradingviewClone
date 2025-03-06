<script setup>
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia'; // Import Inertia for navigation

const props = defineProps({
    timespan: {
        type: String,
        default: 'day',
    },
    timemultiplier: {
        type: String,
        default: '1',
    },
    symbol: {
        type: String,
        required: true
    }
});

// Define time interval options
const timeIntervals = [
    { label: 'D', timespan: 'day', timemultiplier: '1' },
    { label: 'W', timespan: 'week', timemultiplier: '1' },
    { label: 'M', timespan: 'month', timemultiplier: '1' },
    { label: '3M', timespan: 'month', timemultiplier: '3' },
    { label: '6M', timespan: 'month', timemultiplier: '6' },
    { label: '12M', timespan: 'month', timemultiplier: '12' }
];

// Check if a time interval is active
const isActive = (interval) => {
    return props.timespan === interval.timespan && props.timemultiplier === interval.timemultiplier;
};

// Change the time interval and refresh using Inertia.js
const changeTimeInterval = (interval) => {
    console.log("Changing to:", interval);

    // Use Inertia to update the URL and component state
    Inertia.visit(route('Home', {
        symbol: props.symbol,
        timespan: interval.timespan,
        timemultiplier: interval.timemultiplier
    }));
};
</script>

<template>
    <div class="flex items-center h-full">
        <!-- Time Interval Buttons -->
        <button
            v-for="interval in timeIntervals"
            :key="`${interval.timespan}-${interval.timemultiplier}`"
            class="w-min h-full flex items-center py-2 px-2 justify-center hover:bg-gray-700/50 rounded text-sm"
            :class="{ 'text-blue-500': isActive(interval), 'text-white': !isActive(interval) }"
            @click="changeTimeInterval(interval)"
        >
            {{ interval.label }}
        </button>
    </div>
</template>
