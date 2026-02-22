import { reactive } from 'vue';

const state = reactive({
    toasts: [],
});

let nextId = 0;

export function showToast(message, type = 'success', duration = 3000) {
    const id = nextId++;
    state.toasts.push({ id, message, type });

    setTimeout(() => {
        removeToast(id);
    }, duration);
}

export function removeToast(id) {
    const index = state.toasts.findIndex(t => t.id === id);
    if (index !== -1) {
        state.toasts.splice(index, 1);
    }
}

export function useToast() {
    return {
        toasts: state.toasts,
        showToast,
        removeToast,
    };
}
