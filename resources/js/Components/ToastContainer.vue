<template>
  <div class="toast-container">
    <TransitionGroup name="toast">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        class="toast-item"
        :class="'toast-' + toast.type"
        @click="removeToast(toast.id)"
      >
        <span class="toast-icon">{{ toast.type === 'success' ? '\u2713' : '\u2715' }}</span>
        <span class="toast-message">{{ toast.message }}</span>
      </div>
    </TransitionGroup>
  </div>
</template>

<script>
import { useToast } from '@/Composables/useToast';

export default {
  name: 'ToastContainer',
  setup() {
    const { toasts, removeToast } = useToast();
    return { toasts, removeToast };
  },
};
</script>

<style scoped>
.toast-container {
  position: fixed;
  top: 80px;
  right: 20px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  pointer-events: none;
}

.toast-item {
  pointer-events: auto;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.9rem 1.5rem;
  border-radius: 12px;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  min-width: 280px;
  max-width: 400px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
}

.toast-success {
  background: rgba(255, 255, 255, 0.85);
  border: 1px solid rgba(76, 175, 80, 0.3);
  color: #2e7d32;
}

.toast-error {
  background: rgba(255, 255, 255, 0.85);
  border: 1px solid rgba(230, 57, 70, 0.3);
  color: #c62828;
}

.toast-icon {
  font-size: 1.1rem;
  flex-shrink: 0;
}

.toast-message {
  flex: 1;
}

.toast-enter-active {
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.toast-leave-active {
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(80px);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(80px);
}

.toast-move {
  transition: transform 0.3s ease;
}

@media (max-width: 480px) {
  .toast-container {
    top: 70px;
    right: 10px;
    left: 10px;
  }

  .toast-item {
    min-width: auto;
    max-width: none;
    font-size: 0.9rem;
    padding: 0.75rem 1rem;
  }
}
</style>
