<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
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
const CACHE_PREFIX = 'market_data_cache_';
const CACHE_TTL = 5 * 60 * 1000; // 5 minutes cache lifetime

// Check if a time interval is active
const isActive = (interval) => {
    return props.timespan === interval.timespan && props.timemultiplier === interval.timemultiplier;
};

// Generate a cache key based on parameters
const generateCacheKey = (symbol, timespan, timemultiplier) => {
    return `${CACHE_PREFIX}${symbol}_${timespan}_${timemultiplier}`;
};

// Load data from cache
const getFromCache = (cacheKey) => {
    try {
        const cachedItem = localStorage.getItem(cacheKey);
        if (!cachedItem) return null;
        
        const { data, timestamp } = JSON.parse(cachedItem);
        const now = new Date().getTime();
        
        // Check if cache is still valid
        if (now - timestamp < CACHE_TTL) {
            return data;
        } else {
            // Clean up expired cache
            localStorage.removeItem(cacheKey);
            return null;
        }
    } catch (error) {
        console.error("Error reading from cache:", error);
        return null;
    }
};

// Save data to cache
const saveToCache = (cacheKey, data) => {
    try {
        const cacheEntry = {
            data: data,
            timestamp: new Date().getTime()
        };
        localStorage.setItem(cacheKey, JSON.stringify(cacheEntry));
    } catch (error) {
        console.error("Error saving to cache:", error);
    }
};

// Last navigation timestamp to prevent rapid clicks
const lastNavigationTime = ref(0);
const NAVIGATION_COOLDOWN = 2000; // 2 seconds between navigations

// Change the time interval with throttling and caching
const changeTimeInterval = (interval) => {
    // Don't navigate if already on this interval
    if (isActive(interval)) {
        console.log("Already on this interval:", interval.label);
        return;
    }
    
    // Check if navigation is too soon after previous navigation
    const now = new Date().getTime();
    if (now - lastNavigationTime.value < NAVIGATION_COOLDOWN) {
        console.log("Navigation throttled. Please wait a moment before changing intervals again.");
        return;
    }
    
    // Update last navigation time
    lastNavigationTime.value = now;
    
    // Create the cache key for this data request
    const cacheKey = generateCacheKey(props.symbol, interval.timespan, interval.timemultiplier);
    
    console.log("Changing to:", interval.label);
    
    // Set a flag in sessionStorage to tell the destination page to check cache
    sessionStorage.setItem('check_cache_on_load', cacheKey);
    
    // Navigate using Inertia
    Inertia.visit(route('Home', {
        symbol: props.symbol,
        timespan: interval.timespan,
        timemultiplier: interval.timemultiplier
    }));
};

// Clean up old cache entries
const cleanupCache = () => {
    try {
        Object.keys(localStorage).forEach(key => {
            if (key.startsWith(CACHE_PREFIX)) {
                try {
                    const cachedItem = JSON.parse(localStorage.getItem(key));
                    const now = new Date().getTime();
                    if (now - cachedItem.timestamp > CACHE_TTL) {
                        localStorage.removeItem(key);
                    }
                } catch (e) {
                    localStorage.removeItem(key);
                }
            }
        });
    } catch (error) {
        console.error("Error cleaning cache:", error);
    }
};

// Set up cache cleanup
onMounted(() => {
    // Clean cache on component mount and periodically
    cleanupCache();
    const interval = setInterval(cleanupCache, 10 * 60 * 1000); // Clean every 10 minutes
    
    // Clean up the interval when component is unmounted
    onUnmounted(() => {
        clearInterval(interval);
    });
});
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
