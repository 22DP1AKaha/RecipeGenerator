<template>
  <MainLayout>
    <div class="ai-recipe-page">
      <h1>AI Receptes Ģenerators</h1>
      
      <!-- Ingredient Selection Section -->
      <div class="selection-container">
        <div class="ingredient-window">
          <div v-if="loading" class="loading">Ielādējam sastāvdaļas...</div>
          
          <div v-else class="ingredient-categories">
            <div 
              v-for="(ingredients, category) in ingredientCategories" 
              :key="category"
              class="category-card"
            >
              <h3>{{ category }}</h3>
              <div class="ingredient-list">
                <button
                  v-for="ingredient in ingredients"
                  :key="ingredient.id"
                  @click="toggleIngredient(ingredient)"
                  :class="{
                    selected: isSelected(ingredient),
                    'ingredient-btn': true
                  }"
                >
                  {{ ingredient.nosaukums }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Section -->
        <div class="action-section">
          <button 
            @click="generateRecipe" 
            :disabled="selectedIngredients.length === 0 || generating"
            class="generate-btn"
          >
            <span v-if="generating">Ģenerējam...</span>
            <span v-else>Ģenerēt Recepti</span>
          </button>
          <button 
            @click="clearSelection"
            :disabled="selectedIngredients.length === 0"
            class="clear-btn"
          >
            Notīrīt izvēli
          </button>
        </div>
      </div>

      <!-- Recipe Results -->
      <div class="recipe-results">
        
        
        <div v-if="generatedRecipe" class="recipe-output">
          <h2 class="recipe-title">{{ recipeTitle }}</h2>
          
          <div class="recipe-section">
            <h3>Sastāvdaļas:</h3>
            <ul class="ingredients-list">
              <li v-for="(item, index) in recipeIngredients" :key="index">{{ item }}</li>
            </ul>
          </div>
          
          <div class="recipe-section">
            <h3>Pagatavošana:</h3>
            <ol class="instructions-list">
              <li v-for="(step, index) in recipeInstructions" :key="index">{{ step }}</li>
            </ol>
          </div>

          <div class="ai-warning">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="warning-icon">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
            <p>Šī recepte ir ģenerēta, izmantojot mākslīgo intelektu. Lūdzu, pārbaudiet recepti pirms tās pagatavošanas.</p>
          </div>
        </div>
        
        <div v-else-if="error" class="error-message">
          {{ error }}
        </div>

        <div v-else-if="generating" class="spinner"></div>
        
        <div v-else class="placeholder">
          <div class="placeholder-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="ai-icon">
              <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
            </svg>
          </div>
          <p>Izvēlieties sastāvdaļas un nospiediet "Ģenerēt Recepti"</p>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue';
import axios from "axios";

export default {
  name: "AIreceptes",
  components: {
    MainLayout
  },
  data() {
    return {
      ingredientCategories: {},
      selectedIngredients: [],
      generatedRecipe: "",
      loading: true,
      generating: false,
      error: "",
      recipeTitle: "",
      recipeIngredients: [],
      recipeInstructions: []
    };
  },
  mounted() {
    this.fetchIngredients();
  },
  methods: {
    async fetchIngredients() {
      try {
        const response = await axios.get("/api/ingredients");
        this.ingredientCategories = response.data;
      } catch (error) {
        console.error("Error fetching ingredients:", error);
        this.error = "Neizdevās ielādēt sastāvdaļas";
      } finally {
        this.loading = false;
      }
    },

    toggleIngredient(ingredient) {
      const exists = this.selectedIngredients.some(i => i.id === ingredient.id);
      this.selectedIngredients = exists
        ? this.selectedIngredients.filter(i => i.id !== ingredient.id)
        : [...this.selectedIngredients, ingredient];
    },

    isSelected(ingredient) {
      return this.selectedIngredients.some(i => i.id === ingredient.id);
    },

    clearSelection() {
      this.selectedIngredients = [];
      this.generatedRecipe = "";
    },

    async generateRecipe() {
        this.generating = true;
        this.error = '';
        this.generatedRecipe = null;

        try {
            const names = this.selectedIngredients.map(i => i.nosaukums);
            const response = await axios.post('/api/generate-recipe', { 
                ingredients: names.join(', ')
            }, {
                timeout: 60000, // Increase timeout to 60 seconds
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            // Check for successful response
            if (response.status >= 200 && response.status < 300) {
                this.generatedRecipe = response.data.recipe;
                this.parseRecipe();
            } else {
                throw new Error(`Server responded with ${response.status}: ${response.statusText}`);
            }
            
        } catch (e) {
            console.error('Full error:', e);
            
            // Enhanced error handling
            if (e.response) {
                // The request was made and the server responded with a status code
                console.error('Response data:', e.response.data);
                console.error('Response status:', e.response.status);
                console.error('Response headers:', e.response.headers);
                
                this.error = `Server error (${e.response.status}): ${
                    e.response.data?.error || 
                    e.response.data?.message || 
                    e.response.statusText
                }`;
            } else if (e.request) {
                // The request was made but no response was received
                console.error('Request:', e.request);
                this.error = 'No response from server. Check network connection.';
            } else {
                // Something happened in setting up the request
                console.error('Error message:', e.message);
                this.error = `Request setup error: ${e.message}`;
            }
        } finally {
            this.generating = false;
        }
    },

    parseRecipe() {
        // Normalize line breaks and trim whitespace
        const normalizedRecipe = this.generatedRecipe
            .replace(/\r\n/g, '\n')
            .replace(/\n+/g, '\n')
            .trim();
        
        const lines = normalizedRecipe.split('\n').filter(l => l.trim());
        
        // If no lines, reset everything
        if (lines.length === 0) {
            this.recipeTitle = "Recepte netika pareizi formatēta";
            this.recipeIngredients = [];
            this.recipeInstructions = [];
            return;
        }

        // Extract title - handle various formats
        const titleLine = lines[0];
        this.recipeTitle = titleLine
            .replace(/^[#\d\s*:.-]+/i, '')  // Remove markdown prefixes
            .replace(/[*_]+/g, '')           // Remove bold/italic markers
            .trim();

        // Find section indices
        const ingIdx = lines.findIndex(l => /sastāvdaļas|ingredients|lietot|lietotājs/i.test(l));
        const insIdx = lines.findIndex(l => /pagatavošana|instrukcijas|gatavošana|gatavošanas/i.test(l));
        
        // Handle case where sections might be merged
        if (ingIdx === insIdx && ingIdx > -1) {
            this.parseCombinedSections(lines, ingIdx);
            return;
        }

        // Extract ingredients
        this.recipeIngredients = this.extractSection(
            lines, 
            ingIdx, 
            insIdx > ingIdx ? insIdx : lines.length,
            /^[-*•]|^\d+\.?\)?/ // Match bullet points or numbered lists
        );

        // Extract instructions
        this.recipeInstructions = this.extractSection(
            lines, 
            insIdx > -1 ? insIdx : (ingIdx > -1 ? ingIdx + 1 : 0),
            lines.length,
            /^\d+[.)]|^[-*•]/   // Match numbered steps or bullet points
        );
        
        // Fallback if sections weren't found
        if (this.recipeIngredients.length === 0 || this.recipeInstructions.length === 0) {
            this.parseFallbackFormat(lines);
        }
    },

    // Helper function to extract sections
    extractSection(lines, startIdx, endIdx, pattern) {
        if (startIdx < 0 || startIdx >= lines.length) return [];
        
        const sectionLines = lines.slice(startIdx + 1, endIdx);
        return sectionLines
            .map(line => {
                // Remove section headers if they appear in the content
                const cleaned = line.replace(/^(sastāvdaļas|ingredients|pagatavošana|gatavošanas|instrukcijas)[:.]?\s*/i, '');
                // Remove list markers
                return cleaned.replace(pattern, '').trim();
            })
            .filter(line => line.length > 0);
    },

    // Handle combined sections (like "Sastāvdaļas un pagatavošana:")
    parseCombinedSections(lines, sectionIdx) {
        const sectionLines = lines.slice(sectionIdx + 1);
        const separatorIndex = sectionLines.findIndex(line => /^[=-]{5,}|^\s*$/.test(line));
        
        if (separatorIndex > -1) {
            this.recipeIngredients = this.extractSection(
                sectionLines, 
                -1, 
                separatorIndex,
                /^[-*•]|^\d+\.?\)?/
            );
            
            this.recipeInstructions = this.extractSection(
                sectionLines, 
                separatorIndex,
                sectionLines.length,
                /^\d+[.)]|^[-*•]/
            );
        } else {
            // Try to split by list type change
            const firstNumberedIndex = sectionLines.findIndex(line => /^\d+[.)]/.test(line));
            
            if (firstNumberedIndex > 0) {
                this.recipeIngredients = this.extractSection(
                    sectionLines, 
                    -1, 
                    firstNumberedIndex,
                    /^[-*•]/
                );
                
                this.recipeInstructions = this.extractSection(
                    sectionLines, 
                    firstNumberedIndex,
                    sectionLines.length,
                    /^\d+[.)]/
                );
            } else {
                // Fallback: treat everything as ingredients
                this.recipeIngredients = this.extractSection(
                    sectionLines, 
                    -1, 
                    sectionLines.length,
                    /^[-*•]|^\d+\.?\)?/
                );
                this.recipeInstructions = ["Skatīt sastāvdaļu sadaļā"];
            }
        }
    },

    // Fallback parser for unexpected formats
    parseFallbackFormat(lines) {
        // Try to detect list patterns
        const isNumberedList = lines.some(line => /^\d+[.)]/.test(line));
        const isBulletList = lines.some(line => /^[-*•]/.test(line));
        
        if (isNumberedList) {
            this.recipeInstructions = lines
                .filter(line => /^\d+[.)]/.test(line))
                .map(line => line.replace(/^\d+[.)]\s*/, '').trim());
            
            this.recipeIngredients = lines
                .filter(line => !/^\d+[.)]/.test(line) && line.trim().length > 0)
                .slice(1) // Skip title
                .map(line => line.replace(/^[-*•]\s*/, '').trim());
        } 
        else if (isBulletList) {
            this.recipeIngredients = lines
                .slice(1) // Skip title
                .map(line => line.replace(/^[-*•]\s*/, '').trim());
            
            this.recipeInstructions = ["Lūdzu skatīt sastāvdaļu sadaļā"];
        } 
        else {
            // Last resort: split by empty lines
            const sections = normalizedRecipe.split(/\n\s*\n/);
            if (sections.length > 1) {
                this.recipeIngredients = sections[1].split('\n').map(l => l.trim());
                this.recipeInstructions = sections.length > 2 
                    ? sections[2].split('\n').map(l => l.trim())
                    : ["Instrukcijas nav atrastas"];
            } else {
                // Show everything as instructions
                this.recipeInstructions = lines.slice(1);
            }
        }
    }
  }
};
</script>

<style scoped>
.ai-recipe-page {
  max-width: 1400px;
  margin: 0 auto;
  padding: 2rem;
}

h1 {
  text-align: center;
  font-size: 2.5rem;
  margin-bottom: 2rem;
  color: #4a3f35;
  font-family: monospace;
}

.selection-container {
  display: flex;
  gap: 2rem;
  margin-bottom: 2rem;
}

.ingredient-window {
  flex: 1;
  background: linear-gradient(135deg, #FFF5E1, #FFE4B5);
  border-radius: 10px;
  padding: 1.5rem;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  max-height: 70vh;
  overflow-y: auto;
}

.action-section {
  width: 200px;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  justify-content: center;
}

.generate-btn {
  background: linear-gradient(135deg, #4CAF50, #2E7D32);
  color: white;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 1.1rem;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  font-weight: bold;
}

.generate-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.generate-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.clear-btn {
  background: linear-gradient(135deg, #f44336, #d32f2f);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s;
}

.clear-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.clear-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.ingredient-categories {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.category-card h3 {
  font-size: 1.4rem;
  margin-bottom: 1rem;
  color: #5d4037;
  border-bottom: 2px solid #d7ccc8;
  padding-bottom: 0.5rem;
}

.ingredient-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.8rem;
}

.ingredient-btn {
  background-color: #f5f5f5;
  border: 2px solid #ccc;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s;
  font-size: 1.1rem;
}

.ingredient-btn.selected {
  background-color: #4caf50;
  color: white;
  border-color: #388e3c;
}

.recipe-results {
  background: linear-gradient(135deg, #FFF5E1, #FFE4B5);
  border-radius: 10px;
  padding: 2rem;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  min-height: 400px;
}

.recipe-output {
  animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.recipe-title {
  text-align: center;
  font-size: 2rem;
  margin-bottom: 1.5rem;
  color: #4a3f35;
  padding-bottom: 1rem;
  border-bottom: 2px solid #d7ccc8;
}

.recipe-section {
  margin-bottom: 2rem;
}

.recipe-section h3 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
  color: #5d4037;
}

.ingredients-list, .instructions-list {
  text-align: left;
  padding-left: 2rem;
}

.ingredients-list li {
  margin-bottom: 0.5rem;
  font-size: 1.1rem;
}

.instructions-list li {
  margin-bottom: 1rem;
  font-size: 1.1rem;
  line-height: 1.5;
}

.placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 300px;
  color: #7a6a5d;
}

.placeholder-icon {
  width: 80px;
  height: 80px;
  margin-bottom: 1rem;
}

.ai-icon {
  fill: #7a6a5d;
  opacity: 0.3;
}

.error-message {
  color: #d32f2f;
  padding: 1rem;
  background: #ffebee;
  border-radius: 8px;
  text-align: center;
  font-weight: bold;
}

.loading {
  text-align: center;
  padding: 2rem;
  font-style: italic;
  color: #666;
}

.ai-warning {
  margin-top: 2rem;
  padding: 1rem;
  background-color: #fff8e1;
  border-radius: 8px;
  border-left: 4px solid #ffc107;
  display: flex;
  align-items: flex-start;
  gap: 12px;
  animation: fadeIn 0.8s ease;
}

.ai-warning p {
  margin: 0;
  font-size: 0.95rem;
  line-height: 1.5;
  color: #5d4037;
}

.warning-icon {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
  fill: #ff9800;
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

@media (max-width: 1024px) {
  .selection-container {
    flex-direction: column;
  }
  
  .action-section {
    width: 100%;
    flex-direction: row;
    justify-content: center;
  }
}

@media (max-width: 768px) {
  .ai-recipe-page {
    padding: 1rem;
  }
  
  h1 {
    font-size: 2rem;
  }
  
  .ingredient-list {
    gap: 0.5rem;
  }
  
  .ingredient-btn {
    padding: 0.4rem 0.8rem;
    font-size: 1rem;
  }
}
</style>