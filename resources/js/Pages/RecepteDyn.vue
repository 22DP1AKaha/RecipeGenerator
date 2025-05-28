<template>
  <MainLayout>
      <div v-if="loading" class="spinner"></div>

      <div v-else-if="recipe" class="recipe-details">
          <h1>{{ recipe.nosaukums }}</h1>
          <div class="recipe-info">
              <p>⏱️ Gatavošana: {{ recipe.gatavosanas_laiks }} min</p>
              <p>⭐ Sarežģītība: {{ recipe.grutibas_pakape }}</p>
          </div>

          <div class="rating-bar">
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
            <div class="average-text">{{ recipe.average_rating.toFixed(1) }} / 5</div>
          </div>

          <img :src="recipe.attels" alt="Recipe Image" class="recipe-image" v-if="recipe.attels" />

          <p class="description"><strong>Apraksts:</strong> {{ recipe.apraksts }}</p>

          <div class="recipe-content">
              <div class="recipe-ingredients">
                  <RecDynIngredients
                      :base-ingredients="baseIngredients"
                      :portion-sizes="portionSizes"
                      :servings="servings"
                      @serving-changed="updateServings"
                  />
              </div>

              <div class="recipe-instructions">
                  <RecDynInstructions :instructions="recipe.instructions" />
              </div>
          </div>

          <BackButton />
      </div>

      <div v-else-if="!recipe" class="recipe-not-found">
          <h1>Recepte nav atrasta!</h1>
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
          portionSizes: ["1 porcija", "2 porcijas", "3 porcijas", "4 porcijas", "5 porcijas"],
          recipe: null,
          baseIngredients: [],
          loading: true,
          hover: 0,
      };
  },
  methods: {
      async fetchRecipe() {
          try {
              const response = await axios.get(`/api/recipes/${this.id}`);
              this.recipe = response.data;
              this.baseIngredients = response.data.ingredients.map(ing => ({
                  id: ing.id,
                  originalDaudzums: ing.daudzums,
                  nosaukums: ing.nosaukums
              }));
          } catch (error) {
              console.error("Error fetching recipe:", error);
          } finally {
              this.loading = false;
          }
      },
      updateServings(newServings) {
          this.servings = parseInt(newServings);
      },
      async rateRecipe(star) {
          try {
              // Submit rating directly
              const response = await axios.post('/ratings', {
                  receptes_id: this.recipe.id,
                  vertejums: star,
                  komentars: null // Explicitly set to null
              }, {
                  withCredentials: true
              });
              
              // Update the recipe data with new ratings
              this.recipe.user_rating = star;
              this.recipe.average_rating = response.data.average;
          } catch (error) {
              if (error.response && error.response.status === 401) {
                  // Unauthenticated - redirect to login
                  window.location.href = '/ienakt';
              } else {
                  console.error('Rating failed:', error);
                  let errorMessage = 'Vērtējums neizdevās. Lūdzu mēģiniet vēlreiz.';
                  
                  if (error.response?.data?.message) {
                      errorMessage = error.response.data.message;
                  } else if (error.message) {
                      errorMessage = error.message;
                  }
                  
                  alert(errorMessage);
              }
          }
      }
  },
  created() {
      this.fetchRecipe();
  }
};
</script>

<style scoped>
* {
  font-family: monospace;
}

.recipe-details {
  max-width: 700px;
  margin: auto;
  padding: 20px;
  text-align: center;
}

.recipe-info {
  font-size: 18px;
  margin: 10px 0;
}

.recipe-image {
  width: 100%;
  max-width: 400px;
  border-radius: 10px;
  margin: 20px 0;
}

.description {
  font-size: 18px;
  margin-bottom: 15px;
}

.recipe-content {
  display: flex;
  justify-content: space-between;
  text-align: left;
  margin-top: 20px;
  gap: 40px;
}

.recipe-ingredients {
  flex: 1;
  max-width: 300px;
}

.recipe-instructions {
  flex: 2;
  min-width: 0;
}

/* Jaunie stili sastāvdaļām */
:deep(.ingredient-row) {
  display: flex;
  gap: 15px;
  margin-bottom: 8px;
  align-items: baseline;
}

:deep(.ingredient-quantity) {
  flex: 0 0 70px;
  text-align: right;
  font-weight: bold;
  white-space: nowrap;
}

:deep(.ingredient-name) {
  flex: 1;
  word-break: break-word;
  hyphens: auto;
}

:deep(ul) {
  padding-left: 0;
  margin-top: 15px;
}

:deep(li) {
  list-style: none;
  padding: 3px 0;
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

.rating-bar {
  display: flex;
  align-items: center;
  gap: 5px;
  justify-content: center;
  margin: 15px 0;
}

.star {
  font-size: 24px;
  color: #ccc;
  cursor: pointer;
  transition: color 0.2s;
}

.star.filled {
  color: gold;
}

.star.user-rated {
  color: orange;
}

.average-text {
  margin-left: 10px;
  font-size: 14px;
  font-weight: bold;
}

@media (max-width: 600px) {
  .recipe-content {
      flex-direction: column;
      gap: 20px;
  }

  .recipe-ingredients {
      max-width: 100%;
  }
  
  .rating-bar {
      flex-wrap: wrap;
      justify-content: center;
  }
  
  .average-text {
      width: 100%;
      text-align: center;
      margin-top: 10px;
  }
}
</style>