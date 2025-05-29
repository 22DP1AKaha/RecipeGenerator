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

      <!-- Favorites Toggle -->
      <div v-if="isUserLoggedIn" class="favorites-toggle">
        <button 
          @click="toggleFavorites"
          :class="{ active: showFavoritesOnly }"
          class="favorites-button"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="star-icon">
            <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
          </svg>
          {{ showFavoritesOnly ? 'Rādīt visas receptes' : 'Rādīt tikai favorītus' }}
        </button>
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
            
            <!-- Heart Icon for Favorites -->
            <div 
              class="favorite-heart" 
              @click.stop="handleFavorite(recipe, $event)"
              :class="{ saved: recipe.is_saved }"
            >
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
              </svg>
            </div>
            
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
      showFavoritesOnly: false, // New toggle state
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
        
        // Favorite recipes filter
        const favoriteMatch = this.showFavoritesOnly ? recipe.is_saved : true;

        return searchMatch && mealMatch && nutritionMatch && proteinMatch && preferenceMatch && favoriteMatch;
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
      this.showFavoritesOnly = false; // Also reset favorites toggle
    },
    toggleFavorites() {
      this.showFavoritesOnly = !this.showFavoritesOnly;
    },
    async handleFavorite(recipe, event) {
      event.stopPropagation();
      
      try {
        if (recipe.is_saved) {
          // Remove from favorites
          await axios.delete(`/favorites/${recipe.id}`, {
            withCredentials: true
          });
          recipe.is_saved = false;
          
          // If we're in favorites-only mode and this was the last favorite, reset the filter
          if (this.showFavoritesOnly && !this.recipes.some(r => r.is_saved)) {
            this.showFavoritesOnly = false;
          }
        } else {
          // Add to favorites
          await axios.post('/favorites', {
            receptes_id: recipe.id
          }, {
            withCredentials: true
          });
          recipe.is_saved = true;
        }
      } catch (error) {
          if (error.response && error.response.status === 401) {
                  // Unauthenticated - redirect to login
                  window.location.href = '/ienakt';
          } else {
              console.error('Kļūda apstrādājot favorītu:', error);
              
              // Detailed error message
              let errorMsg = 'Radās kļūda. Lūdzu, mēģiniet vēlreiz.';
              if (error.response) {
                // Server responded with error status
                errorMsg = error.response.data.error || 
                          error.response.data.message || 
                          `Servera kļūda: ${error.response.status}`;
              } else if (error.request) {
                // No response received
                errorMsg = 'Nav savienojuma ar serveri. Pārbaudiet savienojumu.';
              }
              else if (error.message) {
                // General error message
                errorMsg = error.message;
              }
        
              alert(errorMsg);
          }
        }
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

.favorites-toggle {
  margin: 1.5rem 0;
  display: flex;
  justify-content: center;
}

/* Neumorphic pill button */
.favorites-button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.7rem 1.8rem;
  font-size: 1rem;
  font-weight: 500;
  color: #4a3f35;                       /* dark charcoal text */
  background: #ede5db;                  /* soft almond */
  border-radius: 50px;
  border: none;
  box-shadow: 
    6px 6px 12px rgba(0,0,0,0.1),      /* bottom-right shadow */
   -6px -6px 12px rgba(255,255,255,0.7);/* top-left highlight */
  cursor: pointer;
  transition: box-shadow 0.2s ease, transform 0.15s ease;
}

.favorites-button .star-icon {
  width: 20px;
  height: 20px;
  fill: #7a6a5d;                       /* muted brown icon */
  transition: fill 0.2s ease;
}

/* Hover: lift slightly */
.favorites-button:hover {
  box-shadow: 
    4px 4px 8px rgba(0,0,0,0.08),
   -4px -4px 8px rgba(255,255,255,0.8);
  transform: translateY(-1px);
}

/* Active / toggled: pressed-in effect */
.favorites-button:active,
.favorites-button.active {
  box-shadow: inset 4px 4px 8px rgba(0,0,0,0.1),
              inset -4px -4px 8px rgba(255,255,255,0.7);
  transform: none;
  
  color: #b4835f;                     /* warm taupe text */
}

.favorites-button:active .star-icon,
.favorites-button.active .star-icon {
  fill: #b4835f;                      /* taupe icon */
}

/* Slight shrink on click for tactile feedback */
.favorites-button:active {
  transform: scale(0.97);
}

/* Responsive tweaks */
@media (max-width: 768px) {
  .favorites-button {
    padding: 0.6rem 1.4rem;
    font-size: 0.9rem;
  }
  .favorites-button .star-icon {
    width: 18px;
    height: 18px;
  }
}

@media (max-width: 480px) {
  .favorites-button {
    width: 100%;
    max-width: 280px;
    justify-content: center;
  }
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

/* Heart Icon Styles */
.favorite-heart {
  position: absolute;
  top: 10px;
  right: 10px;
  z-index: 20;
  cursor: pointer;
  background: rgba(255, 255, 255, 0.7);
  border-radius: 50%;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: all 0.3s ease;
  backdrop-filter: blur(2px);
  -webkit-backdrop-filter: blur(2px);
}

.favorite-heart svg {
  width: 20px;
  height: 20px;
  fill: #555;
  transition: fill 0.3s ease;
}

/* Show heart on hover */
.recipe-card:hover .favorite-heart {
  opacity: 1;
}

/* Saved state - always visible and red */
.favorite-heart.saved {
  opacity: 1;
  background: rgba(255, 255, 255, 0.9);
}

.favorite-heart.saved svg {
  fill: #e74c3c;
}

/* Hover effects */
.favorite-heart:hover {
  background: rgba(255, 255, 255, 0.9);
  transform: scale(1.15);
}

.favorite-heart:hover svg {
  fill: #e74c3c;
}

.favorite-heart.saved:hover svg {
  fill: #c0392b;
}


@media (hover: none) {
  .favorite-heart {
    opacity: 1;
    background: rgba(255, 255, 255, 0.8);
  }
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
  transition: opacity 0.3s ease;
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
  
  .favorite-heart {
    width: 36px;
    height: 36px;
  }
  
  .favorite-heart svg {
    width: 22px;
    height: 22px;
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