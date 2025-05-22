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
            <option v-for="time in filterOptions.mealTimes" :key="time" :value="time">{{ time }}</option>
          </select>

          <select v-model="selectedNutritionType" class="filter-dropdown">
            <option value="">Visi uztura veidi</option>
            <option v-for="nutrition in filterOptions.nutritionTypes" :key="nutrition" :value="nutrition">{{ nutrition }}</option>
          </select>

          <select v-model="selectedProteinSource" class="filter-dropdown">
            <option value="">Visi olbaltumvielu avoti</option>
            <option v-for="source in filterOptions.proteinSources" :key="source" :value="source">{{ source }}</option>
          </select>

          <button @click="clearFilters" class="clear-filters">Notīrīt filtrus</button>
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
          <div 
            v-if="filteredRecipes.length === 0"
            class="no-results"
          >
            Nav atrastas receptes atbilstoši filtriem!
          </div>
          
          <div
            v-for="recipe in filteredRecipes"
            :key="recipe.id"
            class="recipe-card"
            @click="showRecipe(recipe.id)"
          >
            <img :src="recipe.image" :alt="recipe.title" />
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
      return this.recipes.filter(recipe => {
        // Base filters
        const searchMatch = recipe.title.toLowerCase().includes(this.searchQuery.toLowerCase());
        const mealMatch = this.selectedMealTime ? recipe.edienreize === this.selectedMealTime : true;
        const nutritionMatch = this.selectedNutritionType ? recipe.uzturs === this.selectedNutritionType : true;
        const proteinMatch = this.selectedProteinSource ? 
          recipe.galvenais_olbaltumvielu_avots === this.selectedProteinSource : true;

        // Dietary preference filter
        let preferenceMatch = true;
        if (this.filterByPreferences && this.isUserLoggedIn) {
          const user = this.$page.props.auth.user;
          const forbiddenIngredients = this.getForbiddenIngredients(user);
          const recipeIngredients = recipe.ingredients; // Directly use the array
          preferenceMatch = !this.hasForbiddenIngredients(recipeIngredients, forbiddenIngredients);
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
      try {
        const [recipesResponse, filtersResponse] = await Promise.all([
          axios.get('http://localhost:8000/api/recipes'),
          axios.get('http://localhost:8000/api/recipe-filters')
        ]);
        
        this.recipes = recipesResponse.data;
        this.filterOptions = {
          mealTimes: filtersResponse.data.mealTimes,
          nutritionTypes: filtersResponse.data.nutritionTypes,
          proteinSources: filtersResponse.data.proteinSources
        };
      } catch (error) {
        console.error('Kļūda ielādējot datus:', error);
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
    },
    getForbiddenIngredients(user) {
      return [
        ...user.dietas_ierobezojumi.flatMap(d => 
          d.restricted_ingredients.map(i => i.id)
        ),
        ...user.alergijas.flatMap(a => 
          a.allergic_ingredients.map(i => i.id)
        )
      ];
    },
    hasForbiddenIngredients(recipeIngredients, forbidden) {
      return recipeIngredients.some(id => forbidden.includes(id));
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

/* Existing styles remain unchanged */
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

.recipe-card img {
  width: 100%;
  height: 180px;
  object-fit: cover;
  border-radius: 8px;
  margin-bottom: 0.5rem;
}

.recipe-card h2 {
  font-size: 1.5rem;
  margin: 0.5rem 0;
}

.recipe-tags {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  flex-wrap: wrap;
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