<script setup>

</script>

<template>
    <div class="flex flex-col gap-8 w-full">
        <div class="flex flex-row w-full items-center justify-between text-white p-2 border-b border-gray-600 gap-12 w-full">
            <span class="w-max">Symbol</span>
            <span class="w-max">Last Price</span>
        </div>
        <div class="flex flex-col">
            <button
                class="flex items-center justify-between text-white p-2 border-b border-gray-600 hover:bg-gray-700/50 hover:border-opacity-0 gap-12 w-full">
                <span class="w-max">GBPUSD</span>
                <span class="w-max text-green-500">1.23456</span>
            </button>
            <button
                class="flex items-center justify-between text-white p-2 border-b border-gray-600 hover:bg-gray-700/50 hover:border-opacity-0 gap-12 w-full">
                <span class="w-max">GBPJPY</span>
                <span class="w-max text-green-500">1.23456</span>
            </button>
        </div>
    </div>
</template>