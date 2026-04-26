<template>
  <MainLayout>
    <div class="recipe-page">
      <div class="hero-section">
        <h1 class="gradient-text">Recepšu Meklētājs</h1>
        <p class="hero-subtitle">Meklē, filtrē un atklāj jaunas receptes</p>
      </div>

      <input
        v-model="searchQuery"
        type="text"
        :placeholder="filterLabels.searchPlaceholder"
        class="search-bar"
      />

      <div class="filters">
        <select v-model="selectedMealTime" class="filter-dropdown">
          <option value="">{{ filterLabels.allMealTimes }}</option>
          <option v-for="time in filterOptions.mealTimes" :key="time" :value="time">{{ time }}</option>
        </select>

        <select v-model="selectedNutritionType" class="filter-dropdown">
          <option value="">{{ filterLabels.allNutritionTypes }}</option>
          <option v-for="nutrition in filterOptions.nutritionTypes" :key="nutrition" :value="nutrition">{{ nutrition }}</option>
        </select>

        <select v-model="selectedProteinSource" class="filter-dropdown">
          <option value="">{{ filterLabels.allProteinSources }}</option>
          <option v-for="source in filterOptions.proteinSources" :key="source" :value="source">{{ source }}</option>
        </select>

        <button @click="clearFilters" class="clear-filters">{{ filterLabels.clearFilters }}</button>
      </div>

      <div v-if="hasFavorites" class="favorites-toggle">
        <button @click="toggleFavorites" class="filter-chip" :class="{ 'filter-chip--active': showFavoritesOnly }">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="chip-heart" :class="{ filled: showFavoritesOnly }">
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
          </svg>
          {{ showFavoritesOnly ? 'Rādīt visas receptes' : 'Rādīt tikai favorītus' }}
        </button>
      </div>

      <div class="sorting">
        <select v-model="sortBy" class="sort-dropdown">
          <option value="">{{ filterLabels.sortBy }}</option>
          <option v-for="option in sortOptions" :key="option.value" :value="option.value">{{ option.label }}</option>
        </select>

        <select v-model="sortDirection" class="sort-dropdown">
          <option v-for="direction in sortDirections" :key="direction.value" :value="direction.value">{{ direction.label }}</option>
        </select>
      </div>

      <div v-if="hasPreferences" class="preference-filter">
        <label>
          <input type="checkbox" v-model="filterByPreferences" />
          Parādīt tikai manam uzturam atbilstošas receptes
        </label>
      </div>

      <div v-if="loading" class="spinner"></div>

      <div v-else class="recipe-grid">
        <div v-if="recipes.length === 0" class="no-results">
          Nav atrastas receptes atbilstoši filtriem!
        </div>

        <div
          v-for="recipe in recipes"
          :key="recipe.id"
          class="recipe-card"
          @click="showRecipe(recipe.id)"
          @mouseenter="hoveredId = recipe.id"
          @mouseleave="hoveredId = null"
        >
          <div class="image-container">
            <img :src="recipe.image" :alt="recipe.title" :class="{ 'img-dimmed': hoveredId === recipe.id }" />

            <div class="favorite-heart" @click.stop="handleFavorite(recipe, $event)" :class="{ saved: recipe.is_saved }">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
              </svg>
            </div>

            <div class="rating-overlay" :class="{ 'rating-overlay--visible': hoveredId === recipe.id }">
              <div class="rating-stars">
                <span v-for="star in 5" :key="star" class="star" :class="{ filled: star <= recipe.average_rating }">★</span>
              </div>
              <div class="rating-number">{{ recipe.average_rating.toFixed(1) }}</div>
            </div>
          </div>

          <h2>{{ recipe.title }}</h2>
          <div class="recipe-tags">
            <span class="tag">{{ recipe.meal_time }}</span>
            <span class="tag">{{ recipe.diet_type }}</span>
          </div>
        </div>
      </div>

      <div v-if="!loading && total > perPage" class="pagination-container">
        <div class="pg-nav">
          <button class="pg-btn pg-btn--arrow" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1" aria-label="Iepriekšējā lapa">&#8249;</button>

          <template v-for="(page, index) in paginationPages" :key="index">
            <button
              v-if="page !== '...'"
              class="pg-btn"
              :class="{ 'pg-btn--active': page === currentPage }"
              @click="goToPage(page)"
              :aria-current="page === currentPage ? 'page' : undefined"
            >{{ page }}</button>
            <span v-else class="pg-ellipsis">&#x2026;</span>
          </template>

          <button class="pg-btn pg-btn--arrow" @click="goToPage(currentPage + 1)" :disabled="currentPage === lastPage" aria-label="Nākamā lapa">&#8250;</button>
        </div>

        <div class="pagination-info">
          {{ currentPage }} / {{ lastPage }} &nbsp;·&nbsp; {{ total }} receptes
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from "@/Layouts/MainLayout.vue";
import { showToast } from '@/Composables/useToast';
import axios from 'axios';
import { ref, computed, watch, onMounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';

const page = usePage();

const searchQuery = ref('');
const searchTimeout = ref(null);
const selectedMealTime = ref('');
const selectedNutritionType = ref('');
const selectedProteinSource = ref('');
const hoveredId = ref(null);
const recipes = ref([]);
const filterOptions = ref({ mealTimes: [], nutritionTypes: [], proteinSources: [] });
const sortOptions = ref([]);
const sortDirections = ref([]);
const filterLabels = ref({});
const loading = ref(true);
const filterByPreferences = ref(false);
const showFavoritesOnly = ref(false);
const sortBy = ref('');
const sortDirection = ref('asc');
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);
const perPage = ref(12);

const isUserLoggedIn = computed(() => page.props.auth.user !== null);
const hasFavorites = computed(() => isUserLoggedIn.value && page.props.auth.has_favorites);
const hasPreferences = computed(() => isUserLoggedIn.value && page.props.auth.has_preferences);

const paginationPages = computed(() => {
    const pages = [];
    const maxVisible = 7;
    const current = currentPage.value;
    const last = lastPage.value;

    if (last <= maxVisible) {
        for (let i = 1; i <= last; i++) pages.push(i);
    } else {
        pages.push(1);
        if (current <= 3) {
            for (let i = 2; i <= Math.min(5, last - 1); i++) pages.push(i);
            if (last > 5) pages.push('...');
        } else if (current >= last - 2) {
            pages.push('...');
            for (let i = Math.max(2, last - 4); i < last; i++) pages.push(i);
        } else {
            pages.push('...');
            for (let i = current - 1; i <= current + 1; i++) pages.push(i);
            pages.push('...');
        }
        pages.push(last);
    }
    return pages;
});

async function fetchRecipes(pg = 1) {
    loading.value = true;
    try {
        const params = {
            sort_by: sortBy.value,
            sort_direction: sortDirection.value,
            page: pg,
            per_page: perPage.value,
        };
        if (searchQuery.value) params.search = searchQuery.value;
        if (selectedMealTime.value) params.meal_time = selectedMealTime.value;
        if (selectedNutritionType.value) params.nutrition = selectedNutritionType.value;
        if (selectedProteinSource.value) params.protein_source = selectedProteinSource.value;
        if (filterByPreferences.value) params.filter_by_preferences = true;
        if (showFavoritesOnly.value) params.favorites_only = true;

        const response = await axios.get('/api/recipes', { params, withCredentials: true });
        recipes.value = response.data.data;

        const meta = response.data.meta;
        if (meta) {
            currentPage.value = meta.current_page;
            lastPage.value = meta.last_page;
            total.value = meta.total;
            perPage.value = meta.per_page;
        }
    } finally {
        loading.value = false;
    }
}

async function fetchStaticData() {
    const [filtersRes, configRes] = await Promise.all([
        axios.get('/api/recipe-filters', { withCredentials: true }),
        axios.get('/api/config', { withCredentials: true }),
    ]);
    filterOptions.value = {
        mealTimes: filtersRes.data.mealTimes,
        nutritionTypes: filtersRes.data.nutritionTypes,
        proteinSources: filtersRes.data.proteinSources,
    };
    sortOptions.value = configRes.data.sortOptions;
    sortDirections.value = configRes.data.sortDirections;
    filterLabels.value = configRes.data.filterLabels;
}

function goToPage(pg) {
    if (pg >= 1 && pg <= lastPage.value) {
        fetchRecipes(pg);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function clearFilters() {
    clearTimeout(searchTimeout.value);
    searchQuery.value = '';
    selectedMealTime.value = '';
    selectedNutritionType.value = '';
    selectedProteinSource.value = '';
    filterByPreferences.value = false;
    showFavoritesOnly.value = false;
    fetchRecipes(1);
}

function toggleFavorites() {
    showFavoritesOnly.value = !showFavoritesOnly.value;
    fetchRecipes(1);
}

async function handleFavorite(recipe, event) {
    event.stopPropagation();
    try {
        if (recipe.is_saved) {
            const response = await axios.delete(`/favorites/${recipe.id}`, { withCredentials: true });
            recipe.is_saved = false;
            page.props.auth.has_favorites = response.data.has_favorites;
            showToast('Recepte noņemta no favorītiem', 'success');
            if (showFavoritesOnly.value && !recipes.value.some(r => r.is_saved)) {
                showFavoritesOnly.value = false;
                fetchRecipes(1);
            }
        } else {
            const response = await axios.post('/favorites', { recipe_id: recipe.id }, { withCredentials: true });
            recipe.is_saved = true;
            page.props.auth.has_favorites = response.data.has_favorites;
            showToast('Recepte pievienota favorītiem!', 'success');
        }
    } catch (error) {
        if (error.response?.status === 401) {
            window.location.href = '/ienakt';
        } else {
            const errorMsg = error.response?.data?.error
                ?? error.response?.data?.message
                ?? (error.request ? 'Nav savienojuma ar serveri. Pārbaudiet savienojumu.' : error.message)
                ?? 'Radās kļūda. Lūdzu, mēģiniet vēlreiz.';
            showToast(errorMsg, 'error');
        }
    }
}

function showRecipe(id) {
    router.visit(route('recepte', { id }));
}

watch(searchQuery, () => {
    clearTimeout(searchTimeout.value);
    searchTimeout.value = setTimeout(() => fetchRecipes(1), 400);
});

watch([selectedMealTime, selectedNutritionType, selectedProteinSource, sortBy, sortDirection, filterByPreferences], () => {
    fetchRecipes(1);
});

onMounted(() => {
    fetchStaticData();
    fetchRecipes();
});
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

.filter-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.45rem 1rem;
  border-radius: 20px;
  border: 1.5px solid rgba(255, 107, 53, 0.25);
  background: transparent;
  color: var(--warm-dark);
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.filter-chip:hover {
  border-color: var(--primary-color);
  background: rgba(255, 107, 53, 0.05);
  color: var(--primary-color);
}

.filter-chip--active {
  background: rgba(255, 107, 53, 0.12);
  border-color: var(--primary-color);
  color: var(--primary-color);
}

.chip-heart {
  width: 14px;
  height: 14px;
  fill: none;
  stroke: currentColor;
  stroke-width: 2;
  flex-shrink: 0;
}

.chip-heart.filled {
  fill: var(--primary-color);
  stroke: var(--primary-color);
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
  text-align: center;
  padding: 1.5rem;
}

.hero-section {
  text-align: center;
  margin-bottom: 2rem;
}

.hero-section .gradient-text {
  font-size: 2.5rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
}

.hero-subtitle {
  font-size: 1.1rem;
  color: var(--warm-dark);
  opacity: 0.8;
  font-weight: 400;
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
  transition: filter 0.3s ease;
}

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

.rating-overlay--visible {
  opacity: 1;
}

.img-dimmed {
  filter: brightness(0.7);
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
  .recipe-page {
    padding: 1rem;
  }

  .hero-section .gradient-text {
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

.pagination-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  margin-top: 3rem;
  padding-bottom: 2rem;
  animation: fadeIn 0.4s ease-out;
}

.pg-nav {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  flex-wrap: wrap;
  justify-content: center;
}

.pg-btn {
  min-width: 38px;
  height: 38px;
  border-radius: 19px;
  border: 1.5px solid rgba(255, 107, 53, 0.2);
  background: rgba(255, 255, 255, 0.5);
  color: var(--warm-dark);
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.18s ease;
  padding: 0 0.75rem;
  line-height: 1;
}

.pg-btn:hover:not(:disabled) {
  background: var(--primary-color);
  border-color: var(--primary-color);
  color: white;
  transform: translateY(-1px);
  box-shadow: 0 3px 10px rgba(255, 107, 53, 0.3);
}

.pg-btn--active {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  border-color: transparent;
  color: white;
  box-shadow: 0 3px 12px rgba(255, 107, 53, 0.35);
}

.pg-btn--arrow {
  font-size: 1.3rem;
  font-weight: 400;
}

.pg-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
  transform: none;
}

.pg-ellipsis {
  color: var(--warm-dark);
  opacity: 0.5;
  padding: 0 0.2rem;
  font-size: 1rem;
  line-height: 38px;
  user-select: none;
}

.pagination-info {
  text-align: center;
  color: var(--warm-dark);
  font-size: 0.85rem;
  opacity: 0.65;
}

@media (max-width: 480px) {
  .pg-btn {
    min-width: 34px;
    height: 34px;
    font-size: 0.85rem;
    padding: 0 0.6rem;
  }
}
</style>
