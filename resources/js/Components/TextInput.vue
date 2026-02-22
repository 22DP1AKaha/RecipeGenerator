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
        class="text-input"
        v-model="model"
        ref="input"
    />
</template>

<style scoped>
.text-input {
    width: 100%;
    padding: 0.8rem;
    border: 2px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    font-family: monospace;
    margin-top: 0.5rem;
    background: rgba(255, 255, 255, 0.7);
    color: var(--warm-dark);
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    box-sizing: border-box;
}

.text-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.12);
}
</style>
