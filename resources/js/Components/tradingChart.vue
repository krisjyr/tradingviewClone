<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { createChart, CandlestickSeries, LineSeries } from 'lightweight-charts';
import axios from 'axios';
import SelectModal from './SelectModal.vue';

const props = defineProps({
    symbol: {
        type: String,
        required: true,
    },
    timespan: {
        type: String,
        default: 'day',
    },
    timemultiplier: {
        type: String,
        default: '1',
    },
    chartType: {
        type: String,
        default: 'candlestick',
    }
});

const isModalOpen = ref(false);

let lableTime = ref(null);
const chartContainer = ref(null);
const data = ref([]);
let chart = null;
let candlestickSeries = null;
let lineSeries = null;

const openLast = computed(() => (data.value.length ? data.value[data.value.length - 1].o : "-"));
const closeLast = computed(() => (data.value.length ? data.value[data.value.length - 1].c : "-"));
const highLast = computed(() => (data.value.length ? data.value[data.value.length - 1].h : "-"));
const lowLast = computed(() => (data.value.length ? data.value[data.value.length - 1].l : "-"));


const updateLabelTime = () => {
    switch (props.timespan) {
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
};

const CACHE_PREFIX = 'market_data_cache_';
const CACHE_TTL = 60 * 60 * 1000; // 60 minutes cache lifetime

// Generate a cache key based on parameters (same as in TimeInterval.vue)
const generateCacheKey = (symbol, timespan, timemultiplier) => {
    return `${CACHE_PREFIX}${symbol}_${timespan}_${timemultiplier}`;
};

// Modified fetchData function with caching
const fetchData = async () => {
    try {
        // Generate cache key for this request
        const cacheKey = generateCacheKey(
            props.symbol, 
            props.timespan, 
            props.timemultiplier
        );
        
        // Check if we should use cache
        let cachedData = null;
        
        try {
            const cachedItem = localStorage.getItem(cacheKey);
            if (cachedItem) {
                const { data, timestamp } = JSON.parse(cachedItem);
                const now = new Date().getTime();
                
                // Check if cache is still valid
                if (now - timestamp < CACHE_TTL) {
                    cachedData = data;
                }
            }
        } catch (err) {
            console.error("Error reading from cache:", err);
        }
        
        // Use cached data if available
        if (cachedData) {
            console.log("Using cached data for:", cacheKey);
            data.value = cachedData;
            updateChart();
            return;
        }
        
        // If no valid cache, fetch fresh data
        console.log("Fetching fresh data for:", cacheKey);
        const response = await axios.get(route('marketData.fetch', {
            symbol: props.symbol,
            timespan: props.timespan,
            timemultiplier: props.timemultiplier
        }));
                
        if (response.data.results) {
            // Store the data in localStorage cache
            try {
                localStorage.setItem(cacheKey, JSON.stringify({
                    data: response.data.results,
                    timestamp: new Date().getTime()
                }));
            } catch (err) {
                console.error("Error saving to cache:", err);
            }
            
            data.value = response.data.results;
            updateChart();
        } else {
            console.error("No data returned from API:", response.data);
        }
    } catch (error) {
        console.error("Error fetching market data:", error);
    }
};



const updateChart = () => {
    if (!chart || !data.value.length) return;
    
    if (candlestickSeries) {
        chart.removeSeries(candlestickSeries);
        candlestickSeries = null;
    }
    
    if (lineSeries) {
        chart.removeSeries(lineSeries);
        lineSeries = null;
    }
    
    if (props.chartType === 'candlestick') {
        candlestickSeries = chart.addSeries(CandlestickSeries, { 
            upColor: '#26a69a', 
            downColor: '#ef5350', 
            borderVisible: false, 
            wickUpColor: '#26a69a', 
            wickDownColor: '#ef5350' 
        });
        
        candlestickSeries.setData(data.value.map(item => ({
            time: item.t / 1000,
            open: item.o,
            high: item.h,
            low: item.l,
            close: item.c,
        })));
    } else if (props.chartType === 'line') {
        lineSeries = chart.addSeries(LineSeries, {
            color: '#2962FF',
            lineWidth: 2,
        });
        
        lineSeries.setData(data.value.map(item => ({
            time: item.t / 1000,
            value: item.c,
        })));
    }
    
    chart.timeScale().fitContent();
};

// The existing watcher, now using the cached fetchData
watch(() => [props.symbol, props.timespan, props.timemultiplier, props.chartType], () => {
    updateLabelTime();
    fetchData();
});

onMounted(() => {
    if (!chartContainer.value) return;
    
    chart = createChart(chartContainer.value, {
        autoSize: false,
        layout: {
            textColor: '#ffffff7F',
            background: { type: 'solid', color: '#0a0a0a' },
        },
        grid: {
            vertLines: { color: '#4f4f4f7F' },
            horzLines: { color: '#4f4f4f7F' },
        },
        timeScale: { 
            timeVisible: true, 
            secondsVisible: false 
        },
    });

    const checkCacheKey = sessionStorage.getItem('check_cache_on_load');
    if (checkCacheKey) {
        // Clear the flag
        sessionStorage.removeItem('check_cache_on_load');
        
        // Try to load from cache
        try {
            const cachedItem = localStorage.getItem(checkCacheKey);
            if (cachedItem) {
                const { data: cachedData, timestamp } = JSON.parse(cachedItem);
                const now = new Date().getTime();
                
                // Check if cache is still valid
                if (now - timestamp < CACHE_TTL) {
                    console.log("Loading from cache on initial render");
                    data.value = cachedData;
                    updateChart();
                    // Skip the initial API call since we have data
                    return;
                }
            }
        } catch (err) {
            console.error("Error loading from cache on mount:", err);
        }
    }
    
    updateLabelTime();
    fetchData();
    
    // Handle resize
    const resizeObserver = new ResizeObserver(() => {
        chart.applyOptions({ 
            width: chartContainer.value.clientWidth, 
            height: chartContainer.value.clientHeight 
        });
    });
    
    resizeObserver.observe(chartContainer.value);
    
    // Cleanup on unmount
    return () => {
        if (chart) {
            chart.remove();
            chart = null;
        }
        resizeObserver.disconnect();
    };
});
</script>

<template>
    <div class="h-full w-full flex flex-col rounded-md">
        <div ref="chartContainer" class="rounded-md w-full h-full relative">
            <div class="absolute top-1 left-1 bg-black bg-opacity-75 z-50 flex items-center justify-center rounded gap-3">
                <div
                    class="flex items-center space-x-1 text-body p-[0.15rem] rounded border border-black border-opacity-0 hover:bg-opacity-100 hover:bg-[#0f0f0f] hover:border-gray-400 hover:border-opacity-100">
                    <button class="text-gray-400 hover:bg-gray-700 rounded-sm px-1" @click="isModalOpen = true"> {{ props.symbol.startsWith('C:') || props.symbol.startsWith('I:') ? props.symbol.slice(2) : props.symbol }} </button>
                    <span class="text-gray-400 select-none">•</span>
                    <span class="text-gray-400 rounded-sm px-1"> {{ props.timemultiplier }}{{ lableTime }}</span>
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

    <SelectModal :isOpen="isModalOpen" @close="isModalOpen = false"/>
</template>