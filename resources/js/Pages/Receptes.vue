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

      <!-- Filters -->
      <div class="filters">
        <select v-model="selectedMealTime" class="filter-dropdown">
          <option value="">Visas edienreizes</option>
          <option
            v-for="time in filterOptions.mealTimes"
            :key="time"
            :value="time"
          >
            {{ time }}
          </option>
        </select>

        <select v-model="selectedNutritionType" class="filter-dropdown">
          <option value="">Visi uztura veidi</option>
          <option
            v-for="nutrition in filterOptions.nutritionTypes"
            :key="nutrition"
            :value="nutrition"
          >
            {{ nutrition }}
          </option>
        </select>

        <select v-model="selectedProteinSource" class="filter-dropdown">
          <option value="">Visi olbaltumvielu avoti</option>
          <option
            v-for="source in filterOptions.proteinSources"
            :key="source"
            :value="source"
          >
            {{ source }}
          </option>
        </select>

        <button @click="clearFilters" class="clear-filters">
          Notīrīt filtrus
        </button>
      </div>

      <!-- Preference Filter -->
      <div v-if="isUserLoggedIn" class="preference-filter">
        <label>
          <input type="checkbox" v-model="filterByPreferences" />
          Parādīt tikai manam uzturam atbilstošas receptes
        </label>
      </div>

      <!-- Loading Spinner -->
      <div v-if="loading" class="spinner"></div>

      <!-- Recipe Grid -->
      <div v-else class="recipe-grid">
        <div v-if="filteredRecipes.length === 0" class="no-results">
          Nav atrastas receptes atbilstoši filtriem!
        </div>

        <div
          v-for="recipe in filteredRecipes"
          :key="recipe.id"
          class="recipe-card"
          @click="showRecipe(recipe.id)"
        >
          <!-- Recipe Image with Rating Overlay -->
          <div class="image-container">
            <img :src="recipe.image" :alt="recipe.title" />
            <div class="rating-overlay">
              <div class="rating-stars">
                <span
                  v-for="star in 5"
                  :key="star"
                  class="star"
                  :class="{ filled: star <= recipe.average_rating }"
                >★</span>
              </div>
              <div class="rating-number">{{ recipe.average_rating.toFixed(1) }}</div>
            </div>
          </div>
          
          <h2>{{ recipe.title }}</h2>
          <div class="recipe-tags">
            <span class="tag">{{ recipe.edienreize }}</span>
            <span class="tag">{{ recipe.dietas_tips }}</span>
          </div>
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
      selectedMealTime: "",
      selectedNutritionType: "",
      selectedProteinSource: "",
      recipes: [],
      filterOptions: {
        mealTimes: [],
        nutritionTypes: [],
        proteinSources: []
      },
      loading: true,
      filterByPreferences: false,
    };
  },
  computed: {
    filteredRecipes() {
      const forbidden = this.$page.props.auth.forbidden_ingredients || [];

      return this.recipes.filter(recipe => {
        // Base filters
        const searchMatch = recipe.title.toLowerCase().includes(this.searchQuery.toLowerCase());
        const mealMatch = this.selectedMealTime ? recipe.edienreize === this.selectedMealTime : true;
        const nutritionMatch = this.selectedNutritionType ? recipe.uzturs === this.selectedNutritionType : true;
        const proteinMatch = this.selectedProteinSource ? recipe.galvenais_olbaltumvielu_avots === this.selectedProteinSource : true;

        // Dietary preference filter
        let preferenceMatch = true;
        if (this.filterByPreferences && this.isUserLoggedIn) {
          preferenceMatch = !forbidden.some(id => recipe.ingredients.includes(id));
        }

        return searchMatch && mealMatch && nutritionMatch && proteinMatch && preferenceMatch;
      });
    },
    isUserLoggedIn() {
      return this.$page.props.auth.user !== null;
    },
  },
  methods: {
    async fetchData() {
      this.loading = true;
      try {
        const [recipesResponse, filtersResponse] = await Promise.all([
          axios.get('/api/recipes', { withCredentials: true }),
          axios.get('/api/recipe-filters', { withCredentials: true })
        ]);

        this.recipes = recipesResponse.data;
        this.filterOptions = {
          mealTimes: filtersResponse.data.mealTimes,
          nutritionTypes: filtersResponse.data.nutritionTypes,
          proteinSources: filtersResponse.data.proteinSources
        };
      } catch (error) {
        console.error('Kļūda ielādējot datus:', error);
        alert('Radās kļūda ielādējot receptes. Lūdzu, pārbaudiet savienojumu un mēģiniet vēlreiz.');
      } finally {
        this.loading = false;
      }
    },
    clearFilters() {
      this.selectedMealTime = '';
      this.selectedNutritionType = '';
      this.selectedProteinSource = '';
    },
    showRecipe(id) {
      this.$inertia.visit(route('recepte', { id }));
    }
  },
  mounted() {
    this.fetchData();
  },
};
</script>

<style scoped>
/* Add preference filter styles */
.preference-filter {
  margin: 1rem 0;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
}

.preference-filter label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 1rem;
  color: #666;
}

.preference-filter input[type="checkbox"] {
  width: 1.2rem;
  height: 1.2rem;
}

.recipe-page {
  font-family: monospace;
  text-align: center;
  padding: 2rem;
}

h1 {
  font-size: 2.5rem;
  margin-bottom: 1rem;
}

.search-bar {
  width: 50%;
  padding: 0.8rem;
  font-size: 1.2rem;
  border: 2px solid #ccc;
  border-radius: 8px;
  outline: none;
  margin-bottom: 1rem;
}

.filters {
  margin: 1rem 0;
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

.filter-dropdown {
  padding: 0.5rem;
  font-size: 1rem;
  border: 2px solid #ccc;
  border-radius: 5px;
  background-color: white;
  cursor: pointer;
  min-width: 200px;
}

.filter-dropdown:hover {
  border-color: #888;
}

.clear-filters {
  padding: 0.5rem 1rem;
  background-color: #f44336;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.clear-filters:hover {
  background-color: #d32f2f;
}

.recipe-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  justify-items: center;
  margin-top: 2rem;
}

.no-results {
  grid-column: 1 / -1;
  font-size: 1.2rem;
  color: #666;
  padding: 2rem;
}

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

.image-container {
  position: relative;
  width: 100%;
  height: 180px;
  overflow: hidden;
  border-radius: 8px;
  margin-bottom: 0.5rem;
}

.image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Default style for rating overlay - hidden */
.rating-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  opacity: 0;
  background: rgba(0, 0, 0, 0.5);
  border-radius: 8px;
  pointer-events: none; /* Allow clicking through the overlay */
}

.rating-stars {
  display: flex;
  margin-bottom: 5px;
}

.star {
  font-size: 20px;
  color: #ddd;
  margin: 0 1px;
}

.star.filled {
  color: #ffd700;
}

.rating-number {
  color: white;
  font-size: 18px;
  font-weight: bold;
  text-shadow: 0 1px 2px rgba(0,0,0,0.8);
}

/* Computer-only hover effects */
@media (hover: hover) and (pointer: fine) {
  .image-container img {
    transition: filter 0.3s ease;
  }
  
  .recipe-card:hover .image-container img {
    filter: brightness(0.7);
  }
  
  .recipe-card:hover .rating-overlay {
    opacity: 1;
  }
}

.recipe-tags {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  flex-wrap: wrap;
  margin-top: 0.5rem;
}

.tag {
  background-color: #FFE4B5;
  padding: 0.3rem 0.8rem;
  border-radius: 15px;
  font-size: 0.9rem;
}

@media (max-width: 1024px) {
  .recipe-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 600px) {
  .recipe-grid {
    grid-template-columns: 1fr;
  }
  
  .filters {
    flex-direction: column;
  }
  
  .filter-dropdown {
    width: 100%;
  }
  
  .search-bar {
    width: 80%;
  }
  
  .recipe-card {
    width: 100%;
    max-width: 300px;
  }
}

.spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #FFE4B5;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1s linear infinite;
  margin: 2rem auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>