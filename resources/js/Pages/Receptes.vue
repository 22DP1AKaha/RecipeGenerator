<template>
    <MainLayout>
      <div class="recipe-page">
        <h1>Recepšu Meklētājs</h1>
  
        <!-- Search Bar -->
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Meklēt recepti..."
          class="search-bar"
        />
  
        <!-- Loading Spinner -->
        <div v-if="loading" class="spinner"></div>
  
        <!-- Recipe Grid -->
        <div v-else class="recipe-grid">
          <div
            v-for="recipe in filteredRecipes"
            :key="recipe.id"
            class="recipe-card"
            @click="showRecipe(recipe.id)"
          >
            <img :src="recipe.image" :alt="recipe.title" />
            <h2>{{ recipe.title }}</h2>
          </div>
        </div>
      </div>
    </MainLayout>
  </template>
  
  <script>
  import MainLayout from "@/Layouts/MainLayout.vue";
import axios from 'axios';
  
  export default {
    name: 'Receptes',
    components: {
      MainLayout,
    },
    data() {
      return {
        searchQuery: "",
        recipes: [],
        loading: true, // Sākumā ielādējas
      };
    },
    computed: {
      filteredRecipes() {
        return this.recipes.filter((recipe) =>
          recipe.title.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      },
    },
    methods: {
      showRecipe(id) {
        console.log("Navigating to recipe with ID:", id);
        this.$inertia.visit(`/recepte/${id}`);
      },
      async fetchRecipes() {
        try {
          const response = await axios.get('http://localhost:8000/api/recipes');
          this.recipes = response.data;
        } catch (error) {
          console.error('Error fetching recipes:', error);
        } finally {
          this.loading = false; // Ielāde pabeigta
        }
      },
    },
    mounted() {
      this.fetchRecipes();
    },
  };
  </script>
  
  <style scoped>
  .recipe-page {
    font-family: monospace;
    text-align: center;
    padding: 2rem;
  }
  
  h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
  }
  
  /* Search Bar */
  .search-bar {
    width: 50%;
    padding: 0.8rem;
    font-size: 1.2rem;
    border: 2px solid #ccc;
    border-radius: 8px;
    outline: none;
  }
  
  /* Recipe Grid */
  .recipe-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    justify-items: center;
    margin-top: 2rem;
  }
  
  /* Responsive Grid */
  @media (max-width: 1024px) {
    .recipe-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }
  
  @media (max-width: 600px) {
    .recipe-grid {
      grid-template-columns: repeat(1, 1fr);
    }
  }
  
  /* Recipe Cards */
  .recipe-card {
    font-family: monospace;
    background: linear-gradient(135deg, #FFF5E1, #FFE4B5);
    padding: 1rem;
    width: 250px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    text-align: center;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  
  .recipe-card:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  }
  
  .recipe-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 8px;
  }
  
  .recipe-card h2 {
    font-size: 1.5rem;
    margin-top: 0.5rem;
  }
  </style>
  