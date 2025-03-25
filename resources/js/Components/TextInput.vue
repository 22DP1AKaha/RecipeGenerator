<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="w-full p-3 text-lg border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-400 bg-white text-gray-900 placeholder-gray-400 transition-all duration-200 ease-in-out"
        v-model="model"
        ref="input"
    />
</template>

<style scoped>
input {
    font-family: monospace;
    border-radius: 4px;
}
</style>
