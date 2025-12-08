<template>
  <MainLayout>
    <div class="recipe-page">
      <h1>Recepšu Meklētājs</h1>

      <!-- Search Bar -->
      <input
        v-model="searchQuery"
        type="text"
        :placeholder="config.filterLabels.searchPlaceholder"
        class="search-bar"
      />

      <!-- Filters -->
      <div class="filters">
        <select v-model="selectedMealTime" class="filter-dropdown">
          <option value="">{{ config.filterLabels.allMealTimes }}</option>
          <option
            v-for="time in filterOptions.mealTimes"
            :key="time"
            :value="time"
          >
            {{ time }}
          </option>
        </select>

        <select v-model="selectedNutritionType" class="filter-dropdown">
          <option value="">{{ config.filterLabels.allNutritionTypes }}</option>
          <option
            v-for="nutrition in filterOptions.nutritionTypes"
            :key="nutrition"
            :value="nutrition"
          >
            {{ nutrition }}
          </option>
        </select>

        <select v-model="selectedProteinSource" class="filter-dropdown">
          <option value="">{{ config.filterLabels.allProteinSources }}</option>
          <option
            v-for="source in filterOptions.proteinSources"
            :key="source"
            :value="source"
          >
            {{ source }}
          </option>
        </select>

        <button @click="clearFilters" class="clear-filters">
          {{ config.filterLabels.clearFilters }}
        </button>
      </div>

      <div class="sorting">
        <select v-model="sortBy" class="sort-dropdown">
          <option value="">{{ config.filterLabels.sortBy }}</option>
          <option
            v-for="option in config.sortOptions"
            :key="option.value"
            :value="option.value"
          >
            {{ option.label }}
          </option>
        </select>

        <select v-model="sortDirection" class="sort-dropdown">
          <option
            v-for="direction in config.sortDirections"
            :key="direction.value"
            :value="direction.value"
          >
            {{ direction.label }}
          </option>
        </select>
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

      <!-- Pagination Controls -->
      <div v-if="!loading && total > perPage && !hasClientSideFilters" class="pagination-container">
        <nav aria-label="Recipe pagination">
          <ul class="pagination glass-pagination">
            <!-- Previous Button -->
            <li class="page-item" :class="{ disabled: currentPage === 1 }">
              <button
                class="page-link glass-page-link"
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                aria-label="Previous page"
              >
                ‹ Iepriekšējā
              </button>
            </li>

            <!-- Page Numbers -->
            <li
              v-for="(page, index) in paginationPages"
              :key="index"
              class="page-item"
              :class="{ active: page === currentPage, disabled: page === '...' }"
            >
              <button
                v-if="page !== '...'"
                class="page-link glass-page-link"
                @click="goToPage(page)"
                :aria-label="`Go to page ${page}`"
                :aria-current="page === currentPage ? 'page' : undefined"
              >
                {{ page }}
              </button>
              <span v-else class="page-link glass-page-link disabled-ellipsis">...</span>
            </li>

            <!-- Next Button -->
            <li class="page-item" :class="{ disabled: currentPage === lastPage }">
              <button
                class="page-link glass-page-link"
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === lastPage"
                aria-label="Next page"
              >
                Nākamā ›
              </button>
            </li>
          </ul>

          <!-- Page Info -->
          <div class="pagination-info">
            Lapa {{ currentPage }} no {{ lastPage }} (Kopā: {{ total }} receptes)
          </div>
        </nav>
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
      config: {
        sortOptions: [],
        sortDirections: [],
        filterLabels: {}
      },
      loading: true,
      filterByPreferences: false,
      showFavoritesOnly: false,
      sortBy: '',
      sortDirection: 'asc',
      currentPage: 1,
      lastPage: 1,
      total: 0,
      perPage: 12,
    };
  },
  computed: {
    filteredRecipes() {
      // Only apply client-side search filter
      if (!this.searchQuery) {
        return this.recipes;
      }

      return this.recipes.filter(recipe => {
        return recipe.title.toLowerCase().includes(this.searchQuery.toLowerCase());
      });
    },
    isUserLoggedIn() {
      return this.$page.props.auth.user !== null;
    },
    hasClientSideFilters() {
      return this.searchQuery !== '';
    },
    paginationPages() {
      const pages = [];
      const maxVisible = 7; // Show up to 7 page numbers
      const current = this.currentPage;
      const last = this.lastPage;

      if (last <= maxVisible) {
        // Show all pages if total is less than max visible
        for (let i = 1; i <= last; i++) {
          pages.push(i);
        }
      } else {
        // Always show first page
        pages.push(1);

        if (current <= 3) {
          // Near the beginning
          for (let i = 2; i <= Math.min(5, last - 1); i++) {
            pages.push(i);
          }
          if (last > 5) {
            pages.push('...');
          }
        } else if (current >= last - 2) {
          // Near the end
          pages.push('...');
          for (let i = Math.max(2, last - 4); i < last; i++) {
            pages.push(i);
          }
        } else {
          // In the middle
          pages.push('...');
          for (let i = current - 1; i <= current + 1; i++) {
            pages.push(i);
          }
          pages.push('...');
        }

        // Always show last page
        pages.push(last);
      }

      return pages;
    },
  },
  methods: {
    async fetchData(page = 1) {
      this.loading = true;
      try {
        const params = {
          sort_by: this.sortBy,
          sort_direction: this.sortDirection,
          page: page,
          per_page: this.perPage
        };

        // Add dropdown filters to backend request
        if (this.selectedMealTime) {
          params.meal_time = this.selectedMealTime;
        }
        if (this.selectedNutritionType) {
          params.nutrition = this.selectedNutritionType;
        }
        if (this.selectedProteinSource) {
          params.protein_source = this.selectedProteinSource;
        }

        // Add dietary preferences filter to backend
        if (this.filterByPreferences) {
          params.filter_by_preferences = true;
        }

        // Add favorites filter to backend
        if (this.showFavoritesOnly) {
          params.favorites_only = true;
        }

        const [recipesResponse, filtersResponse, configResponse] = await Promise.all([
          axios.get('/api/recipes', { params, withCredentials: true }),
          axios.get('/api/recipe-filters', { withCredentials: true }),
          axios.get('/api/config', { withCredentials: true })
        ]);

        this.recipes = recipesResponse.data.data;

        // Laravel ResourceCollection puts pagination in 'meta' object
        const meta = recipesResponse.data.meta;
        if (meta) {
          this.currentPage = meta.current_page;
          this.lastPage = meta.last_page;
          this.total = meta.total;
          this.perPage = meta.per_page;
        }

        this.filterOptions = {
          mealTimes: filtersResponse.data.mealTimes,
          nutritionTypes: filtersResponse.data.nutritionTypes,
          proteinSources: filtersResponse.data.proteinSources
        };
        this.config = {
          sortOptions: configResponse.data.sortOptions,
          sortDirections: configResponse.data.sortDirections,
          filterLabels: configResponse.data.filterLabels
        };
      } finally {
        this.loading = false;
      }
    },
    goToPage(page) {
      if (page >= 1 && page <= this.lastPage) {
        this.fetchData(page);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    },
    clearFilters() {
      this.selectedMealTime = '';
      this.selectedNutritionType = '';
      this.selectedProteinSource = '';
      this.filterByPreferences = false;
      this.showFavoritesOnly = false;
      this.fetchData(1); // Reset to page 1 and reload
    },
    toggleFavorites() {
      this.showFavoritesOnly = !this.showFavoritesOnly;
      this.fetchData(1); // Trigger server-side filter
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
            recipe_id: recipe.id
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
  watch:  {
    sortBy() {
      this.fetchData(1);
    },
    sortDirection() {
      this.fetchData(1);
    },
    selectedMealTime() {
      this.fetchData(1);
    },
    selectedNutritionType() {
      this.fetchData(1);
    },
    selectedProteinSource() {
      this.fetchData(1);
    },
    filterByPreferences() {
      this.fetchData(1);
    }
  }
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
  color: #4a3f35;
  background: #ede5db;
  border-radius: 50px;
  border: none;
  box-shadow:
    6px 6px 12px rgba(0,0,0,0.1),
   -6px -6px 12px rgba(255,255,255,0.7);
  cursor: pointer;
  transition: box-shadow 0.2s ease, transform 0.15s ease;
}

.favorites-button .star-icon {
  width: 20px;
  height: 20px;
  fill: #7a6a5d;
  transition: fill 0.2s ease;
}

.favorites-button:hover {
  box-shadow:
    4px 4px 8px rgba(0,0,0,0.08),
   -4px -4px 8px rgba(255,255,255,0.8);
  transform: translateY(-1px);
}

.favorites-button:active,
.favorites-button.active {
  box-shadow: inset 4px 4px 8px rgba(0,0,0,0.1),
              inset -4px -4px 8px rgba(255,255,255,0.7);
  transform: none;
  color: #b4835f;
}

.favorites-button:active .star-icon,
.favorites-button.active .star-icon {
  fill: #b4835f;
}

.favorites-button:active {
  transform: scale(0.97);
}

.sorting {
  display: flex;
  gap: 1rem;
  justify-content: center;
  margin: 1rem 0;
  flex-wrap: wrap;
}

.sort-dropdown {
  padding: 0.5rem;
  font-size: 1rem;
  border: 2px solid #ccc;
  border-radius: 5px;
  background-color: white;
  cursor: pointer;
  min-width: 160px;
}

.recipe-page {
  font-family: monospace;
  text-align: center;
  padding: 1.5rem;
}

.recipe-page h1 {
  font-size: 2rem;
  margin-bottom: 1rem;
  color: #4a3f35;
}

.search-bar {
  width: 50%;
  max-width: 400px;
  padding: 0.8rem;
  font-size: 1.1rem;
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
  min-width: 160px;
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

/* Grid of recipe cards */
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
  height: 160px;
  overflow: hidden;
  border-radius: 8px;
  margin-bottom: 0.5rem;
}

.image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Heart Icon */
.favorite-heart {
  position: absolute;
  top: 8px;
  right: 8px;
  z-index: 20;
  cursor: pointer;
  background: rgba(255, 255, 255, 0.7);
  border-radius: 50%;
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: all 0.3s ease;
  backdrop-filter: blur(2px);
  -webkit-backdrop-filter: blur(2px);
}

.favorite-heart svg {
  width: 18px;
  height: 18px;
  fill: #555;
  transition: fill 0.3s ease;
}

.recipe-card:hover .favorite-heart {
  opacity: 1;
}

.favorite-heart.saved {
  opacity: 1;
  background: rgba(255, 255, 255, 0.9);
}

.favorite-heart.saved svg {
  fill: #e74c3c;
}

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

/* Rating overlay */
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
  pointer-events: none;
  transition: opacity 0.3s ease;
}

.rating-stars {
  display: flex;
  margin-bottom: 5px;
}

.star {
  font-size: 18px;
  color: #ddd;
  margin: 0 1px;
}

.star.filled {
  color: #ffd700;
}

.rating-number {
  color: white;
  font-size: 16px;
  font-weight: bold;
  text-shadow: 0 1px 2px rgba(0,0,0,0.8);
}

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

/* Medium screens (≤1024px) */
@media (max-width: 1024px) {
  .recipe-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* Small screens (≤600px) */
@media (max-width: 600px) {
  .recipe-page {
    padding: 1rem;
  }

  .recipe-page h1 {
    font-size: 1.75rem;
  }

  .search-bar {
    width: 80%;
    max-width: none;
    font-size: 1rem;
  }

  .filters {
    flex-direction: column;
    gap: 0.8rem;
  }

  .filter-dropdown {
    width: 100%;
    font-size: 0.95rem;
  }

  .clear-filters {
    width: 100%;
  }

  .sorting {
    flex-direction: column;
    gap: 0.8rem;
  }

  .sort-dropdown {
    width: 100%;
    font-size: 0.95rem;
  }

  .recipe-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
    margin-top: 1.5rem;
  }

  .recipe-card {
    width: 100%;
    max-width: 300px;
  }

  .favorite-heart {
    width: 32px;
    height: 32px;
  }

  .favorite-heart svg {
    width: 16px;
    height: 16px;
  }

  .rating-overlay .star {
    font-size: 16px;
  }

  .rating-overlay .rating-number {
    font-size: 14px;
  }
}

/* Spinner */
.spinner {
  border: 4px solid #f3f3f3;
  border-top: 4px solid #FFE4B5;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  animation: spin 1s linear infinite;
  margin: 2rem auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Filter Info */
.filter-info {
  text-align: center;
  padding: 1rem 2rem;
  margin-top: 2rem;
  font-size: 0.95rem;
  color: var(--warm-dark);
  background: var(--glass-bg);
  backdrop-filter: blur(var(--glass-blur));
  -webkit-backdrop-filter: blur(var(--glass-blur));
  border-radius: var(--radius-lg);
  border: 1px solid var(--glass-border);
  box-shadow: 0 4px 16px rgba(255, 107, 53, 0.15);
}

/* Pagination Styles */
.pagination-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  margin-top: 3rem;
  padding-bottom: 2rem;
  animation: fadeIn 0.6s ease-out;
}

.glass-pagination {
  display: flex;
  gap: 0.5rem;
  list-style: none;
  padding: 0;
  margin: 0;
  flex-wrap: wrap;
  justify-content: center;
}

.page-item {
  display: inline-block;
}

.glass-page-link {
  background: var(--glass-bg);
  backdrop-filter: blur(var(--glass-blur));
  -webkit-backdrop-filter: blur(var(--glass-blur));
  border: 1px solid var(--glass-border);
  border-radius: var(--radius-md);
  padding: 0.6rem 1rem;
  min-width: 45px;
  color: var(--warm-dark);
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 8px rgba(255, 107, 53, 0.1);
  font-size: 0.95rem;
  text-align: center;
  border: none;
}

.glass-page-link:hover:not(:disabled):not(.disabled-ellipsis) {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(255, 107, 53, 0.3);
}

.page-item.active .glass-page-link {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  box-shadow: 0 4px 16px rgba(255, 107, 53, 0.4);
  transform: scale(1.05);
}

.disabled-ellipsis {
  cursor: default;
  background: transparent;
  box-shadow: none;
  border: none;
}

.page-item.disabled .glass-page-link {
  opacity: 0.4;
  cursor: not-allowed;
  background: rgba(200, 200, 200, 0.2);
}

.glass-page-link:disabled {
  opacity: 0.4;
  cursor: not-allowed;
  background: rgba(200, 200, 200, 0.2);
}

.pagination-info {
  text-align: center;
  color: var(--warm-dark);
  font-size: 0.95rem;
  font-weight: 500;
  opacity: 0.8;
}

@media (max-width: 768px) {
  .pagination-container {
    margin-top: 2rem;
    padding-bottom: 1.5rem;
  }

  .glass-pagination {
    gap: 0.4rem;
  }

  .glass-page-link {
    padding: 0.5rem 0.75rem;
    min-width: 40px;
    font-size: 0.9rem;
  }

  .pagination-info {
    font-size: 0.85rem;
  }
}

@media (max-width: 480px) {
  .glass-pagination {
    gap: 0.3rem;
  }

  .glass-page-link {
    padding: 0.4rem 0.6rem;
    min-width: 35px;
    font-size: 0.85rem;
  }

  .pagination-info {
    font-size: 0.8rem;
    padding: 0 1rem;
  }
}
</style>
