<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);
const showSlot = ref(props.show);

watch(
    () => props.show,
    () => {
        if (props.show) {
            document.body.style.overflow = 'hidden';
            showSlot.value = true;
        } else {
            document.body.style.overflow = '';
            setTimeout(() => {
                showSlot.value = false;
            }, 200);
        }
    },
);

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape') {
        e.preventDefault();
        if (props.show) {
            close();
        }
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="modal-enter-active"
            enter-from-class="modal-enter-from"
            enter-to-class="modal-enter-to"
            leave-active-class="modal-leave-active"
            leave-from-class="modal-leave-from"
            leave-to-class="modal-leave-to"
        >
            <div v-if="show" class="modal-overlay" @click="close">
                <div class="modal-box" @click.stop>
                    <slot v-if="showSlot" />
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style>
.modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    background: rgba(0, 0, 0, 0.5);
}

.modal-box {
    background: linear-gradient(135deg, #FFF5E1, #FFE4B5);
    border-radius: 16px;
    box-shadow: 0 20px 60px rgba(139, 69, 19, 0.25);
    width: 100%;
    max-width: 480px;
    max-height: 90vh;
    overflow-y: auto;
    border: 1px solid rgba(255, 255, 255, 0.5);
}

.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.25s ease;
}

.modal-enter-active .modal-box,
.modal-leave-active .modal-box {
    transition: opacity 0.25s ease, transform 0.25s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-from .modal-box,
.modal-leave-to .modal-box {
    opacity: 0;
    transform: scale(0.95) translateY(-8px);
}

.modal-enter-to,
.modal-leave-from {
    opacity: 1;
}

.modal-enter-to .modal-box,
.modal-leave-from .modal-box {
    opacity: 1;
    transform: scale(1) translateY(0);
}

@media (max-width: 480px) {
    .modal-overlay {
        padding: 1rem;
        align-items: flex-end;
    }

    .modal-box {
        max-width: 100%;
        border-radius: 16px 16px 0 0;
        max-height: 85vh;
    }
}
</style>
