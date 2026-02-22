<template>
  <MainLayout>
      <div v-if="loading" class="loading-container">
        <div class="spinner"></div>
      </div>

      <div v-else-if="recipe" class="recipe-details">
          <h1 class="recipe-title gradient-text">{{ recipe.title }}</h1>

          <div class="recipe-info glass-card">
              <div class="info-item">
                <span class="info-icon">⏱️</span>
                <span class="info-text">{{ recipe.cooking_time }} min</span>
              </div>
              <div class="info-item">
                <span class="info-icon">⭐</span>
                <span class="info-text">{{ recipe.difficulty }}</span>
              </div>
          </div>

          <div class="rating-bar glass-card">
            <div class="stars-container">
              <span
                v-for="star in 5"
                :key="star"
                class="star"
                :class="{
                  filled: star <= hover || (!hover && star <= recipe.average_rating),
                  'user-rated': star <= recipe.user_rating
                }"
                @mouseover="hover = star"
                @mouseleave="hover = 0"
                @click="rateRecipe(star)"
              >★</span>
            </div>
            <div class="average-text">{{ recipe.average_rating.toFixed(1) }} / 5</div>
          </div>

          <div class="recipe-image-container" v-if="recipe.image">
            <img :src="recipe.image" alt="Recipe Image" class="recipe-image" />
          </div>

          <div class="description glass-card">
            <h3>Apraksts</h3>
            <p>{{ recipe.description }}</p>
          </div>

          <div class="recipe-content">
              <div class="recipe-ingredients glass-card">
                  <RecDynIngredients
                      :base-ingredients="baseIngredients"
                      :portion-sizes="portionSizes"
                      :servings="servings"
                      @serving-changed="updateServings"
                  />
              </div>

              <div class="recipe-instructions glass-card">
                  <RecDynInstructions :instructions="recipe.instructions" />
              </div>
          </div>

          <div class="action-buttons-container">
            <button
              v-if="isUserLoggedIn"
              @click="handleFavorite"
              class="glass-btn favorite-button"
              :class="{ 'favorite-button--saved': recipe.is_saved }"
            >
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="heart-icon">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
              </svg>
              {{ recipe.is_saved ? 'Saglabāts' : 'Saglabāt' }}
            </button>
            <a
              :href="`/api/recipes/${id}/pdf`"
              class="glass-btn pdf-button"
              download
            >
              📄 Lejupielādēt PDF
            </a>
            <BackButton />
          </div>
      </div>

      <div v-else-if="!recipe" class="recipe-not-found glass-card">
          <h1 class="gradient-text">Recepte nav atrasta!</h1>
          <BackButton />
      </div>
  </MainLayout>
</template>

<script>
import BackButton from "@/Components/BackButton.vue";
import RecDynIngredients from "@/Components/RecDynIngredients.vue";
import RecDynInstructions from "@/Components/RecDynInstructions.vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import axios from 'axios';
import { showToast } from '@/Composables/useToast';

export default {
  components: {
      MainLayout,
      RecDynIngredients,
      RecDynInstructions,
      BackButton
  },
  props: {
      id: {
          type: [String, Number],
          required: true,
      }
  },
  data() {
      return {
          servings: 1,
          portionSizes: [],
          recipe: null,
          baseIngredients: [],
          loading: true,
          hover: 0,
      };
  },
  computed: {
      isUserLoggedIn() {
          return this.$page.props.auth.user !== null;
      },
  },
  methods: {
      async fetchConfig() {
          const response = await axios.get('/api/config');
          this.portionSizes = response.data.portionSizes.map(p => p.label);
      },
      async fetchRecipe() {
          try {
              const response = await axios.get(`/api/recipes/${this.id}`);
              this.recipe = response.data.data;
              this.baseIngredients = this.recipe.ingredients.map(ing => ({
                  id: ing.id,
                  originalDaudzums: ing.quantity,
                  name: ing.name
              }));
          } finally {
              this.loading = false;
          }
      },
      updateServings(newServings) {
          this.servings = parseInt(newServings);
      },
      async rateRecipe(star) {
          try {
              const response = await axios.post('/ratings', {
                  recipe_id: this.recipe.id,
                  rating: star,
                  comment: null
              }, {
                  withCredentials: true
              });

              this.recipe.user_rating = star;
              this.recipe.average_rating = response.data.average;
              showToast('Vērtējums saglabāts!', 'success');
          } catch (error) {
              if (error.response && error.response.status === 401) {
                  window.location.href = '/ienakt';
              } else {
                  console.error('Rating failed:', error);
                  let errorMessage = 'Vērtējums neizdevās. Lūdzu mēģiniet vēlreiz.';

                  if (error.response?.data?.message) {
                      errorMessage = error.response.data.message;
                  } else if (error.message) {
                      errorMessage = error.message;
                  }

                  showToast(errorMessage, 'error');
              }
          }
      },
      async handleFavorite() {
          try {
              if (this.recipe.is_saved) {
                  const response = await axios.delete(`/favorites/${this.recipe.id}`, {
                      withCredentials: true
                  });
                  this.recipe.is_saved = false;
                  this.$page.props.auth.has_favorites = response.data.has_favorites;
                  showToast('Recepte noņemta no favorītiem', 'success');
              } else {
                  const response = await axios.post('/favorites', {
                      recipe_id: this.recipe.id
                  }, {
                      withCredentials: true
                  });
                  this.recipe.is_saved = true;
                  this.$page.props.auth.has_favorites = response.data.has_favorites;
                  showToast('Recepte pievienota favorītiem!', 'success');
              }
          } catch (error) {
              if (error.response && error.response.status === 401) {
                  window.location.href = '/ienakt';
              } else {
                  let errorMsg = 'Radās kļūda. Lūdzu, mēģiniet vēlreiz.';
                  if (error.response) {
                      errorMsg = error.response.data.error || error.response.data.message || `Servera kļūda: ${error.response.status}`;
                  } else if (error.request) {
                      errorMsg = 'Nav savienojuma ar serveri. Pārbaudiet savienojumu.';
                  } else if (error.message) {
                      errorMsg = error.message;
                  }
                  showToast(errorMsg, 'error');
              }
          }
      },
  },
  created() {
      this.fetchConfig();
      this.fetchRecipe();
  }
};
</script>

<style scoped>
.loading-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 50vh;
}

.recipe-details {
  max-width: 900px;
  margin: 0 auto;
  padding: 2rem 1rem;
  animation: fadeIn 0.6s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.recipe-title {
  text-align: center;
  font-size: 2.5rem;
  font-weight: 800;
  margin-bottom: 1.5rem;
}

.recipe-info {
  display: flex;
  justify-content: center;
  gap: 2rem;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
}

.info-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.info-icon {
  font-size: 1.5rem;
}

.info-text {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--warm-dark);
}

.recipe-image-container {
  margin: 1.5rem 0;
  display: flex;
  justify-content: center;
}

.recipe-image {
  width: 100%;
  max-width: 500px;
  border-radius: var(--radius-lg);
  box-shadow: 0 8px 24px rgba(255, 107, 53, 0.2);
  transition: transform 0.3s ease;
}

.recipe-image:hover {
  transform: scale(1.02);
}

.description {
  padding: 1.5rem;
  margin-bottom: 1.5rem;
  text-align: left;
}

.description h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--warm-dark);
  margin-bottom: 1rem;
}

.description p {
  font-size: 1.05rem;
  line-height: 1.6;
  color: var(--warm-dark);
  margin: 0;
}

.recipe-content {
  display: flex;
  justify-content: space-between;
  text-align: left;
  margin-top: 1.5rem;
  gap: 2rem;
}

.recipe-ingredients {
  flex: 1;
  min-width: 280px;
  padding: 1.5rem;
}

.recipe-instructions {
  flex: 2;
  min-width: 0;
  padding: 1.5rem;
}

:deep(.ingredient-row) {
  display: flex;
  gap: 1rem;
  margin-bottom: 0.75rem;
  align-items: baseline;
}

:deep(.ingredient-quantity) {
  flex: 0 0 80px;
  text-align: right;
  font-weight: 700;
  white-space: nowrap;
  color: var(--primary-color);
}

:deep(.ingredient-name) {
  flex: 1;
  word-break: break-word;
  hyphens: auto;
  color: var(--warm-dark);
}

:deep(ul) {
  padding-left: 0;
  margin-top: 1rem;
}

:deep(li) {
  list-style: none;
  padding: 0.5rem 0;
}

.spinner {
  border: 4px solid rgba(255, 107, 53, 0.1);
  border-top: 4px solid var(--primary-color);
  border-radius: 50%;
  width: 48px;
  height: 48px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.rating-bar {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem;
  margin-bottom: 1.5rem;
}

.stars-container {
  display: flex;
  gap: 0.5rem;
}

.star {
  font-size: 2rem;
  color: rgba(255, 107, 53, 0.2);
  cursor: pointer;
  transition: all 0.2s ease;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.star:hover {
  transform: scale(1.2);
}

.star.filled {
  color: var(--secondary-color);
}

.star.user-rated {
  color: var(--primary-color);
  text-shadow: 0 2px 8px rgba(255, 107, 53, 0.5);
}

.average-text {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--warm-dark);
}

.action-buttons-container {
  margin-top: 2rem;
  display: flex;
  gap: 1rem;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
}

.pdf-button {
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.8rem 1.8rem;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.pdf-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(255, 107, 53, 0.4);
}

.favorite-button {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.8rem 1.8rem;
  font-size: 1rem;
  background: linear-gradient(135deg, rgba(255,255,255,0.3), rgba(255,255,255,0.15));
  border: 1px solid rgba(255, 255, 255, 0.4);
  color: var(--warm-dark);
}

.favorite-button:hover {
  background: linear-gradient(135deg, rgba(231, 76, 60, 0.15), rgba(231, 76, 60, 0.05));
  border-color: rgba(231, 76, 60, 0.4);
  color: #c0392b;
  box-shadow: 0 8px 24px rgba(231, 76, 60, 0.2);
}

.favorite-button--saved {
  background: linear-gradient(135deg, rgba(231, 76, 60, 0.2), rgba(192, 57, 43, 0.1));
  border-color: rgba(231, 76, 60, 0.5);
  color: #c0392b;
}

.favorite-button--saved:hover {
  background: linear-gradient(135deg, rgba(231, 76, 60, 0.3), rgba(192, 57, 43, 0.15));
}

.heart-icon {
  width: 18px;
  height: 18px;
  fill: currentColor;
  transition: transform 0.2s ease;
}

.favorite-button:hover .heart-icon {
  transform: scale(1.2);
}

.recipe-not-found {
  max-width: 600px;
  margin: 4rem auto;
  padding: 3rem 2rem;
  text-align: center;
}

.recipe-not-found h1 {
  font-size: 2rem;
  margin-bottom: 2rem;
}

@media (max-width: 768px) {
  .recipe-title {
    font-size: 2rem;
  }

  .recipe-info {
    gap: 1.5rem;
    padding: 1.25rem;
  }

  .info-icon {
    font-size: 1.25rem;
  }

  .info-text {
    font-size: 1rem;
  }

  .recipe-content {
    flex-direction: column;
    gap: 1.5rem;
  }

  .recipe-ingredients {
    min-width: 100%;
  }

  .description h3 {
    font-size: 1.3rem;
  }

  .description p {
    font-size: 1rem;
  }
}

@media (max-width: 480px) {
  .recipe-details {
    padding: 1.5rem 0.75rem;
  }

  .recipe-title {
    font-size: 1.75rem;
  }

  .recipe-info {
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
  }

  .rating-bar {
    padding: 1.25rem;
  }

  .star {
    font-size: 1.75rem;
  }

  .average-text {
    font-size: 1rem;
  }

  .recipe-ingredients,
  .recipe-instructions {
    padding: 1.25rem;
  }

  .recipe-not-found {
    padding: 2rem 1.5rem;
  }

  .recipe-not-found h1 {
    font-size: 1.75rem;
  }
}
</style>
