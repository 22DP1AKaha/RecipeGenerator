<template>
  <MainLayout>
      <div v-if="loading" class="spinner"></div>

      <div v-else-if="recipe" class="recipe-details">
          <h1>{{ recipe.nosaukums }}</h1>
          <div class="recipe-info">
              <p>⏱️ Gatavošana: {{ recipe.gatavosanas_laiks }} min</p>
              <p>⭐ Sarežģītība: {{ recipe.grutibas_pakape }}</p>
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
      },
  },
  data() {
      return {
          servings: 1,
          portionSizes: ["1 porcija", "2 porcijas", "3 porcijas", "4 porcijas", "5 porcijas"],
          recipe: null,
          baseIngredients: [],
          loading: true,
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

@media (max-width: 600px) {
  .recipe-content {
      flex-direction: column;
      gap: 20px;
  }

  .recipe-ingredients {
      max-width: 100%;
  }
}
</style>