<template>
  <a v-if="href" :href="href" class="custom-btn">
    {{ text }}
  </a>
  <button v-else :type="type" class="custom-btn">
    {{ text }}
  </button>
</template>

<script setup>
defineProps({
  text: {
    type: String,
    default: "Izmēģināt"
  },
  type: {
    type: String,
    default: "button"
  },
  href: {
    type: String,
    default: null
  }
});
</script>

<style scoped>
.custom-btn {
  padding: 0.875rem 2.5rem;
  font-size: 1.05rem;
  border: none;
  color: white;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  cursor: pointer;
  border-radius: var(--radius-md);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  text-decoration: none;
  display: inline-block;
  text-align: center;
  font-weight: 600;
  box-shadow: 0 4px 16px rgba(255, 107, 53, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.2);
  letter-spacing: 0.5px;
}

.custom-btn:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 24px rgba(255, 107, 53, 0.4);
  background: linear-gradient(135deg, #FF7A4D, #FFA035);
  color: white;
}

.custom-btn:active {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(255, 107, 53, 0.35);
}
</style>