<script setup>
import { defineProps, watch } from 'vue';
import tradingChart from '@/Components/tradingChart.vue';
import avatar from '@/Components/avatar.vue';
import drawingToolbar from '@/Components/drawingToolbar.vue';
import symbolSearch from '@/Components/symbolSearch.vue';
import timeInterval from '@/Components/timeInterval.vue';
import chartStyles from '@/Components/chartStyles.vue';
import watchlist from '@/Components/watchlist.vue';

const props = defineProps({
    symbol: {
        type: String,
        default: 'AAPL',
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

watch(() => {
    console.log('chartPage.vue ', props.symbol, props.timespan, props.timemultiplier, props.chartType);
});
</script>

<template>
    <div class="h-screen bg-gray-800 text-white overflow-hidden">
        <!-- Layout Sensor (Hidden) -->
        <div id="layout-size-sensor" class="fixed inset-0 pointer-events-none invisible"></div>

        <!-- Main Layout Container -->
        <div class="flex flex-col h-full gap-1">
            <!-- Top Bar -->
            <div class="flex items-center justify-start bg-[#0f0f0f] h-min px-2 py-1">
                <avatar />
                <symbolSearch :symbol="symbol" />
                <div class="w-[1px] h-[65%] mx-2 bg-gray-700"></div>
                <timeInterval :timespan="timespan" :timemultiplier="timemultiplier" :symbol="symbol"    />
                <div class="w-[1px] h-[65%] mx-2 bg-gray-700"></div>
                <chartStyles :chartType="chartType" :symbol="symbol" :timespan="timespan" :timemultiplier="timemultiplier" />
            </div>

            <!-- Main Content Area -->
            <div class="flex flex-1 gap-1">
                <!-- Left Sidebar (Drawing Tools) -->
                <div class="bg-[#0f0f0f] p-2 rounded-tr-md">
                    <drawingToolbar />
                </div>

                <!-- Chart Container -->
                <div class="flex-1 w-full h-full bg-[#0f0f0f] rounded-md">
                    <tradingChart 
                        :symbol="symbol" 
                        :timespan="timespan" 
                        :timemultiplier="timemultiplier" 
                        :chartType="chartType" 
                    />
                </div>

                <!-- Right Sidebar (Watchlist) -->
                <div class="bg-[#0f0f0f] p-2 rounded-tl-md">
                    <watchlist />
                </div>
            </div>
        </div>
    </div>
</template>