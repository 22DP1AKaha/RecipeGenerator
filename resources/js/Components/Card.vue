<template>
    <div class="card glass-card">
      <div class="card-image-wrapper">
        <img :src="image" alt="Photo" class="card-image">
      </div>
      <div class="card-content">
        <h1 class="card-title">{{ title }}</h1>
        <p class="card-description">{{ description }}</p>
        <CustomButton
          :href="link"
          text="Izmēģināt"
        />
      </div>
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
  overflow: hidden;
  text-align: center;
  animation: fadeIn 0.6s ease-out;
  max-width: 420px;
  padding: 0;
}

.card-image-wrapper {
  position: relative;
  overflow: hidden;
  border-radius: var(--radius-md) var(--radius-md) 0 0;
  height: 280px;
}

.card-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.card:hover .card-image {
  transform: scale(1.1);
}

.card-content {
  padding: 2rem 1.5rem;
}

.card-title {
  font-size: 1.75rem;
  font-weight: 700;
  margin: 0 0 1rem;
  background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.card-description {
  font-size: 1rem;
  color: var(--warm-dark);
  margin-bottom: 1.5rem;
  line-height: 1.6;
  opacity: 0.9;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 768px) {
  .card {
    max-width: 360px;
  }

  .card-image-wrapper {
    height: 240px;
  }

  .card-content {
    padding: 1.5rem 1.25rem;
  }

  .card-title {
    font-size: 1.5rem;
  }

  .card-description {
    font-size: 0.95rem;
  }
}

@media (max-width: 480px) {
  .card {
    max-width: 100%;
  }

  .card-image-wrapper {
    height: 200px;
  }

  .card-content {
    padding: 1.25rem 1rem;
  }

  .card-title {
    font-size: 1.35rem;
  }

  .card-description {
    font-size: 0.9rem;
  }
}
</style>
