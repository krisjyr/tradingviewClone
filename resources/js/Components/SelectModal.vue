<script setup>
import axios from 'axios';
import { ref } from 'vue';
import { Inertia } from '@inertiajs/inertia'; 

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false
    },
})

const searchQuery = ref('');
const searchResults = ref([]);

const emit = defineEmits(['close']);

const close = () => {
    emit('close');
    searchQuery.value = '';
    searchResults.value = [];
}

const search = () => {
    if (searchQuery.value.length < 3) {
        return;
    }

    axios.get(`/search/${searchQuery.value}`).then(response => {
        searchResults.value = response.data;

        console.log('Search results:', searchResults.value);
    }).catch(error => {
        console.error('Error fetching search results:', error);
    })
};

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

    close();
};
</script>

<template>
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" v-if="isOpen">
        <div class="bg-[#0f0f0f] rounded shadow-lg w-full max-w-md mx-4">
            <!-- Dialog Header -->
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-700">
                <div id="title_symbolSearch" class="font-medium text-gray-200 truncate text-lg">
                    Symbol Search
                </div>
                <button class="p-2 rounded-md hover:bg-gray-700 focus:outline-none"
                    data-name="close" type="button" @click="close">
                    <span class="sr-only">Close menu</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 18" width="18" height="18"
                        class="text-gray-400">
                        <path stroke="currentColor" stroke-width="1.2" d="m1.5 1.5 15 15m0-15-15 15"></path>
                    </svg>
                </button>
            </div>

            <!-- Search Input -->
            <div class="">
                <div class="flex flex-row items-center border-b border-gray-700 p-2">
                    <span class=" text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 28" width="20" height="20" fill="none">
                            <path stroke="currentColor" d="M17.4 17.5a7 7 0 1 0-4.9 2c1.9 0 3.64-.76 4.9-2zm0 0l5.1 5">
                            </path>
                        </svg>
                    </span>
                    <input type="text"
                        class="w-full bg-transparent p-2 focus:outline-none focus:ring-0"
                        autocomplete="off" role="searchbox" data-role="search" placeholder="Search"
                        v-model="searchQuery" @keyup.enter="search">
                </div>

                <!-- Results container would go here -->
                <div class="px-2 py-4">
                    <div v-if="searchQuery.length < 3" class="text-gray-500 text-sm text-center">
                        Please enter at least 3 characters to search.
                    </div>
                    <div v-else-if="searchResults.error" class="text-red-500 text-sm">
                        {{ searchResults.error }}
                    </div>
                    <div class="overflow-y-scroll max-h-[50vh] h-full">
                        <button v-for="item in searchResults.results" :key="item" @click="selectSymbol(item.ticker)"
                            class="flex items-center justify-between text-white p-2 border-b border-gray-600 hover:bg-gray-700/50 hover:border-opacity-0 w-full">
                            <span class="w-max font-medium">{{ item.ticker.startsWith('C:') ||
                                item.ticker.startsWith('I:') ? item.ticker.slice(2) :
                                item.ticker }}</span>
                            <div class="flex flex-col items-end min-w-0 flex-shrink-0">
                                <span
                                    class="text-xs px-2 py-1 rounded bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                    {{ item.market.charAt(0).toUpperCase() +
                                        item.market.slice(1) }} </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate max-w-[150px]"> {{
                                    item.name }}</span>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>