<template>
    <MainLayout>
      <div v-if="recipe" class="recipe-details">
        <h1>{{ recipe.nosaukums }}</h1>
        <div class="recipe-info">
          <p>⏱️ Gatavošana: {{ recipe.gatavosanas_laiks }} min</p>
          <p>⭐ Sarežģītība: {{ recipe.grutibas_pakape }}</p>
        </div>
  
        <img :src="recipe.attels" alt="Recipe Image" class="recipe-image" v-if="recipe.attels" />
  
        <p class="description"><strong>Apraksts:</strong> {{ recipe.apraksts }}</p>
  
        <div class="recipe-content">
          <RecDynIngredients
            :base-ingredients="baseIngredients"
            :portion-sizes="portionSizes"
            :servings="servings"
            @serving-changed="updateServings"
          />
          <RecDynInstructions :instructions="recipe.instructions" />
        </div>
  
        <BackButton />
      </div>
  
      <div v-else class="recipe-not-found">
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
        // Portion sizes as strings that will display in the select box
        portionSizes: ["1 porcija", "2 porcijas", "3 porcijas", "4 porcijas", "5 porcijas"],
        recipe: null,
        baseIngredients: [],
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
            // Removed "piezimes" mapping since it doesn't exist in your table
          }));
        } catch (error) {
          console.error("Error fetching recipe:", error);
        }
      },
      updateServings(newServings) {
        // Extract number from string like "2 porcijas"
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
    gap: 170px;

  }
  
  @media (max-width: 600px) {
    .recipe-content {
      flex-direction: column;
    }
  }
  </style>
  