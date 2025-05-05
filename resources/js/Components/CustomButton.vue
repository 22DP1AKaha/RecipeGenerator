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
  padding: 1rem 3rem;
  font-size: 1rem;
  border: none;
  color: white;
  background: black;
  cursor: pointer;
  border-radius: 50px;
  transition: transform 0.3s ease;
  font-family: monospace;
  text-decoration: none;
  display: inline-block;
  text-align: center;
}

.custom-btn:hover {
  transform: scale(1.1);
}
</style>