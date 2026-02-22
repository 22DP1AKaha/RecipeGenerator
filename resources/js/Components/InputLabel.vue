<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="input-label">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>

<style scoped>
.input-label {
    display: block;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--warm-dark);
    font-family: monospace;
}
</style>
