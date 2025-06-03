<template>
    <div class="card">
      <img :src="image" alt="Photo"><br>
      <h1>{{ title }}</h1>
      <p>{{ description }}</p><br>
      <CustomButton 
        :href="link"
        text="Izmēģināt"
      />
    </div>
  </template>
  
  <script setup>
  import CustomButton from '@/Components/CustomButton.vue';
  
  defineProps({
    image: {
      type: String,
      required: true,
    },
    title: {
      type: String,
      required: true,
    },
    description: {
      type: String,
      required: true,
    },
    link: {
      type: String,
      required: true,
    },
  });
  </script>
  
  <style scoped>
.card {
  font-family: monospace;
  padding: 1.5rem;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  overflow: visible;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border-radius: 10px;
  position: relative;
  animation: fadeIn 0.5s ease-in;
  background: linear-gradient(135deg, #FFF5E1, #FFE4B5);
  text-align: center;
}

/* Title & Description */
.card h1 {
  font-size: 2rem;
  margin: 1rem 0 0.5rem;
}

.card p {
  font-size: 1rem;
  margin-bottom: 1rem;
}

/* Image adjustments */
.card img {
  width: 100%;
  max-width: 400px;
  height: auto;
  object-fit: cover;
  border-radius: 10px;
  margin-bottom: 1rem;
}

.card:hover {
  transform: translateY(-10px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

/* Fade-in animation */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Medium screens (≤768px) */
@media (max-width: 768px) {
  .card {
    padding: 1.2rem;
  }

  .card h1 {
    font-size: 1.75rem;
  }

  .card p {
    font-size: 0.95rem;
  }

  .card img {
    max-width: 300px;
  }
}

/* Small screens (≤480px) */
@media (max-width: 480px) {
  .card {
    padding: 1rem;
  }

  .card h1 {
    font-size: 1.5rem;
  }

  .card p {
    font-size: 0.9rem;
  }

  .card img {
    max-width: 100%;
  }
}
</style>
