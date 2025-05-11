<script setup>
import { ref } from 'vue';
import SelectModal from './SelectModal.vue';

const props = defineProps({
    symbol: String
})

const isModalOpen = ref(false);


</script>

<template>
        <button class="px-2 ml-3 h-full hover:bg-gray-700/50 rounded text-sm text-white flex items-center gap-1" @click="isModalOpen = true">
            <svg class="size-5 text-white" fill="currentColor" viewBox="0 0 18 18">
                <path
                    d="M3.5 8a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM8 2a6 6 0 1 0 3.65 10.76l3.58 3.58 1.06-1.06-3.57-3.57A6 6 0 0 0 8 2Z" />
            </svg>
            <span class="font-semibold">{{props.symbol.startsWith('C:') || props.symbol.startsWith('I:') ? props.symbol.slice(2) : props.symbol}}</span>
        </button>    
        
        <SelectModal :isOpen="isModalOpen" @close="isModalOpen = false"/>
</template>
