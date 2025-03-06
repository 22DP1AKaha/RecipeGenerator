<template>
    <div class="ingredients">
      <h2>Sastāvdaļas:</h2>
      <select 
        v-model="selectedServings" 
        id="portion-size" 
        @change="$emit('serving-changed', selectedServings)"
      >
        <option v-for="portion in portionSizes" :key="portion" :value="portion">
          {{ portion }}
        </option>
      </select>
  
      <ul>
        <li v-for="(ingredient, index) in adjustedIngredients" :key="index">
          {{ ingredient.display }} 
        </li>
      </ul>
    </div>
  </template>
  
  <script>
  export default {
    props: {
      baseIngredients: Array,
      portionSizes: Array,
      servings: Number
    },
    data() {
      return {
        // Initialize selectedServings to the first element, e.g., "1 porcija"
        selectedServings: this.portionSizes.length > 0 ? this.portionSizes[0] : ""
      };
    },
    computed: {
      adjustedIngredients() {
        // Convert selectedServings (e.g., "1 porcija") to a number by extracting the digits
        const servingsNumber = parseInt(this.selectedServings);
        return this.baseIngredients.map(ingredient => {
          const parsed = this.parseDaudzums(ingredient.originalDaudzums);
          const newAmount = parsed.amount * servingsNumber;
          
          return {
            ...ingredient,
            display: `${this.formatAmount(newAmount)}${parsed.unit} ${ingredient.nosaukums}`
          };
        });
      }
    },
    methods: {
      parseDaudzums(daudzums) {
        const matches = daudzums.match(/(\d+\.?\d*)(\D*)/);
        return {
          amount: matches ? parseFloat(matches[1]) : 1,
          unit: matches ? matches[2].trim() : ''
        };
      },
      formatAmount(amount) {
        return amount % 1 === 0 ? amount.toString() : amount.toFixed(1);
      }
    }
  };
  </script>
  
  <style scoped>
  * {
    font-family: monospace;
  }
  
  
  
  /* Portion size dropdown styling */
  #portion-size {
    padding: 8px 12px;
    border: 2px solid dimgray;
    border-radius: 5px;
    background-color: white;
    font-size: 16px;
    cursor: pointer;
    margin-top: 1px;
    margin-bottom: 1px;
    width: 100%;
    max-width: 200px;
  }
  
  #portion-size:focus {
    outline: none;
    border-color: #d93d00;
  }
  
  ul {
    list-style-type: none;
    padding: 0;
  }
  
  li {
    font-size: 16px;
    margin-bottom: 5px;
  }
  </style>
  