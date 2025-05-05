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
  }
  
  .card h1 {
    font-size: 2.5rem;
  }
  
  .card p {
    font-size: 1rem;
  }
  
  .card img {
    width: 31.25rem;
    height: 26.75rem;
    object-fit: cover;
    border-radius: 10px;
  }
  
  .card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  }
  </style>