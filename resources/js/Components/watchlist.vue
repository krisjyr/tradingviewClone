<script setup>
import { ref, onMounted } from 'vue';
import { useSymbolStore } from '../symbolStore.js';
import { Inertia } from '@inertiajs/inertia';

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

const emit = defineEmits(['open-modal']);

const openModal = () => {
    emit('open-modal');
};

// Import the store
const symbolStore = useSymbolStore();

// Fetch symbols when component is mounted
onMounted(() => {
    symbolStore.fetchSymbols();
});

const remove = (id) => {
    symbolStore.removeSymbol(id);
};

</script>

<template>
    <div class="flex flex-col gap-2 w-full h-full min-w-[200px]">
        <div class="flex flex-row items-center justify-between text-white px-2 pt-2">
            <span class="w-max text-lg font-medium">Watchlist</span>
        </div>
        <div class="flex flex-row w-full items-center justify-between text-white p-2 border-b border-gray-600 gap-4">
            <span class="w-max">Symbol</span>
            <button
                class="w-min h-min flex items-center self-end p-1 justify-center hover:bg-gray-700/50 rounded text-sm text-white"
                @click="openModal">
                <svg class="size-7 hover:text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 28"
                    width="28" height="28" fill="none">
                    <path fill="currentColor" d="M7 13h7V6h1v7h7v1h-7v7h-1v-7H7v-1z"></path>
                </svg>
            </button>
            <!-- <div class="flex flex-col items-end">
                <span class="w-max">Last Price</span>
                <span class="w-max text-xs text-gray-400">Change</span>
            </div> -->
        </div>
        <div v-if="symbolStore.loading" class="loading text-center text-gray-400">Loading...</div>
        <div v-else-if="symbolStore.symbols.length === 0" class="no-symbols text-center text-gray-400">
            No symbols saved yet.
        </div>
        <div v-else class="flex flex-col overflow-y-scroll max-h-[86vh] h-full">
            <button v-for="item in symbolStore.symbols" :key="item.id" @click="selectSymbol(item.symbol)"
                class="flex items-center justify-between text-white p-2 border-b border-gray-600 hover:bg-gray-700/20 hover:border-opacity-0 gap-4 w-full"
                :class="{ 'border-blue-500': symbolStore.selectedSymbol === item.symbol }">
                <span class="w-max font-medium">{{ item.symbol.startsWith('C:') ? item.symbol.slice(2) :
                    item.symbol }}</span>
                <div class="flex flex-col items-end">
                    <span class="w-max">{{ item.price }}</span>
                    <span class="w-max text-xs" :class="item.color">{{ item.change }}</span>
                </div>
                <span class="remove hover:text-red-500 hover:bg-gray-700/50 p-1 rounded"
                    @click.stop="remove(item.id)"><svg class="size-5" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 18 18" width="18" height="18">
                        <path fill="currentColor"
                            d="M12 4h3v1h-1.04l-.88 9.64a1.5 1.5 0 0 1-1.5 1.36H6.42a1.5 1.5 0 0 1-1.5-1.36L4.05 5H3V4h3v-.5C6 2.67 6.67 2 7.5 2h3c.83 0 1.5.67 1.5 1.5V4ZM7.5 3a.5.5 0 0 0-.5.5V4h4v-.5a.5.5 0 0 0-.5-.5h-3ZM5.05 5l.87 9.55a.5.5 0 0 0 .5.45h5.17a.5.5 0 0 0 .5-.45L12.94 5h-7.9Z">
                        </path>
                    </svg></span>
            </button>
        </div>
    </div>
</template>
