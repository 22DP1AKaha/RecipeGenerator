<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button :type="type" class="secondary-button">
        <slot />
    </button>
</template>

<style scoped>
.secondary-button {
    display: inline-flex;
    align-items: center;
    padding: 0.6rem 1.4rem;
    font-size: 0.9rem;
    font-weight: 600;
    font-family: monospace;
    color: var(--warm-dark);
    background: rgba(255, 255, 255, 0.6);
    border: 2px solid rgba(139, 69, 19, 0.2);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.secondary-button:hover {
    background: rgba(255, 255, 255, 0.85);
    border-color: rgba(139, 69, 19, 0.4);
    transform: translateY(-1px);
}

.secondary-button:active {
    transform: translateY(0);
}

.secondary-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}
</style>
