<template>
  <MainLayout>
    <div class="ai-recipe-page">
      <div class="hero-section">
        <h1 class="gradient-text">AI Receptes Ģenerators</h1>
        <p class="hero-subtitle">Izveido unikālas receptes ar Tavām sastāvdaļām</p>
      </div>
      
      <div class="selection-container">
        <div class="ingredient-window glass-card">
          <div v-if="loading" class="loading">
            <div class="spinner"></div>
            <p>Ielādējam sastāvdaļas...</p>
          </div>

          <div v-else class="ingredient-categories">
            <div
              v-for="(ingredients, category) in ingredientCategories"
              :key="category"
              class="category-card"
            >
              <button class="category-header" @click="toggleCategory(category)" type="button">
                <span class="category-title">{{ category }}</span>
                <div class="category-header-right">
                  <span v-if="selectedCountInCategory(category) > 0" class="category-badge">
                    {{ selectedCountInCategory(category) }}
                  </span>
                  <svg
                    class="chevron"
                    :class="{ rotated: !collapsedCategories[category] }"
                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round"
                  >
                    <polyline points="6 9 12 15 18 9"/>
                  </svg>
                </div>
              </button>
              <div v-show="!collapsedCategories[category]" class="ingredient-list">
                <button
                  v-for="ingredient in ingredients"
                  :key="ingredient.id"
                  @click="toggleIngredient(ingredient)"
                  :class="{
                    selected: isSelected(ingredient),
                    'ingredient-btn': true
                  }"
                >
                  {{ ingredient.name }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="action-section glass-card">
          <button
            @click="generateRecipe"
            :disabled="selectedIngredients.length < 3 || generating"
            class="glass-btn generate-btn"
          >
            <span v-if="generating">Ģenerējam...</span>
            <span v-else>Ģenerēt Recepti</span>
          </button>
          <button
            @click="clearSelection"
            :disabled="selectedIngredients.length === 0"
            class="glass-btn-secondary clear-btn"
          >
            Notīrīt izvēli
          </button>
          <p v-if="selectedIngredients.length < 3" class="min-ingredient-warning">
            Izvēlieties vismaz 3 sastāvdaļas
          </p>

          <div v-if="selectedIngredients.length > 0" class="selected-chips">
            <span
              v-for="ingredient in selectedIngredients"
              :key="ingredient.id"
              class="chip"
              @click="toggleIngredient(ingredient)"
              title="Noņemt"
            >
              {{ ingredient.name }}
              <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </span>
          </div>

          <div
            v-if="isLoggedIn && hasPreferences"
            class="preferences-toggle-wrap"
          >
            <button
              @click="usePreferences = !usePreferences"
              :class="['preferences-toggle', { active: usePreferences }]"
              type="button"
              aria-label="Diētas un alerģiju filtrs"
            >
              <!-- shield icon -->
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <path v-if="usePreferences" d="M9 12l2 2 4-4"/>
                <g v-else>
                  <line x1="9" y1="9" x2="15" y2="15"/>
                  <line x1="15" y1="9" x2="9" y2="15"/>
                </g>
              </svg>
            </button>
            <div class="pref-tooltip">
              <strong>{{ usePreferences ? 'Preferences: ieslēgtas' : 'Preferences: izslēgtas' }}</strong>
              <span>{{ usePreferences ? 'AI ņems vērā Tavas diētas un alerģijas.' : 'AI ignorēs Tavas diētas un alerģijas.' }}</span>
            </div>
          </div>
        </div>
      </div>

      <div v-if="generatedRecipe || generating || error" ref="recipeResults" class="recipe-results glass-card">
        <div v-if="generatedRecipe" class="recipe-output">
          <h2 class="recipe-title gradient-text">{{ recipeTitle }}</h2>
          
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

          <div class="ai-warning glass-card">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="warning-icon">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
            </svg>
            <p>Šī recepte ir ģenerēta, izmantojot mākslīgo intelektu. Lūdzu, pārbaudiet recepti pirms tās pagatavošanas.</p>
          </div>
        </div>

        <div v-else-if="error" class="error-message glass-card">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="error-icon">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
          </svg>
          <p>{{ error }}</p>
        </div>

        <div v-else-if="generating" class="generating-state">
          <div class="spinner"></div>
          <p class="generating-text">Ģenerējam Tavu recepti...</p>
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
  computed: {
    hasPreferences() {
      return this.$page.props.auth?.has_preferences === true;
    },
    isLoggedIn() {
      return this.$page.props.auth?.user !== null;
    },
  },
  data() {
    return {
      ingredientCategories: {},
      collapsedCategories: {},
      selectedIngredients: [],
      generatedRecipe: "",
      loading: true,
      generating: false,
      error: "",
      recipeTitle: "",
      recipeIngredients: [],
      recipeInstructions: [],
      usePreferences: true,
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
        // Start all categories collapsed
        const collapsed = {};
        Object.keys(response.data).forEach(cat => { collapsed[cat] = true; });
        this.collapsedCategories = collapsed;
      } catch (error) {
        console.error("Error fetching ingredients:", error);
        this.error = "Neizdevās ielādēt sastāvdaļas";
      } finally {
        this.loading = false;
      }
    },

    toggleCategory(category) {
      this.collapsedCategories = {
        ...this.collapsedCategories,
        [category]: !this.collapsedCategories[category],
      };
    },

    selectedCountInCategory(category) {
      const ids = new Set((this.ingredientCategories[category] || []).map(i => i.id));
      return this.selectedIngredients.filter(i => ids.has(i.id)).length;
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
        this.$nextTick(() => {
          this.$refs.recipeResults?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        try {
            const names = this.selectedIngredients.map(i => i.name);
            const response = await axios.post('/api/generate-recipe', {
                ingredients: names.join(', '),
                use_preferences: this.usePreferences,
            }, {
                timeout: 60000,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            

            if (response.status >= 200 && response.status < 300) {
                this.generatedRecipe = response.data.recipe;
                this.parseRecipe();
            } else {
                throw new Error(`Server responded with ${response.status}: ${response.statusText}`);
            }
            
        } catch (e) {
            console.error('Full error:', e);
            

            if (e.response) {

                console.error('Response data:', e.response.data);
                console.error('Response status:', e.response.status);
                console.error('Response headers:', e.response.headers);
                
                this.error = `Server error (${e.response.status}): ${
                    e.response.data?.error || 
                    e.response.data?.message || 
                    e.response.statusText
                }`;
            } else if (e.request) {

                console.error('Request:', e.request);
                this.error = 'No response from server. Check network connection.';
            } else {

                console.error('Error message:', e.message);
                this.error = `Request setup error: ${e.message}`;
            }
        } finally {
            this.generating = false;
        }
    },

    parseRecipe() {

        const normalizedRecipe = this.generatedRecipe
            .replace(/\r\n/g, '\n')
            .replace(/\n+/g, '\n')
            .trim();

        const lines = normalizedRecipe.split('\n').filter(l => l.trim());

        if (lines.length === 0) {
            throw new Error("Recepte netika pareizi formatēta");
        }

        const titleLabelIdx = lines.findIndex(l => /^nosaukums:/i.test(l.trim()));
        if (titleLabelIdx !== -1 && titleLabelIdx + 1 < lines.length) {
            this.recipeTitle = lines[titleLabelIdx + 1].trim();
        } else {
            const titleLine = lines[0];
            this.recipeTitle = titleLine
                .replace(/^[#\d\s*:.-]+/i, '')
                .replace(/[*_]+/g, '')
                .trim();
        }

        const ingIdx = lines.findIndex(l => /sastāvdaļas|ingredients|lietot|lietotājs/i.test(l));
        const insIdx = lines.findIndex(l => /pagatavošana|instrukcijas|gatavošana|gatavošanas/i.test(l));
        

        if (ingIdx === insIdx && ingIdx > -1) {
            this.parseCombinedSections(lines, ingIdx);
            return;
        }

        this.recipeIngredients = this.extractSection(
            lines, 
            ingIdx, 
            insIdx > ingIdx ? insIdx : lines.length,
            /^[-*•]|^\d+\.?\)?/
        );

        this.recipeInstructions = this.extractSection(
            lines, 
            insIdx > -1 ? insIdx : (ingIdx > -1 ? ingIdx + 1 : 0),
            lines.length,
            /^\d+[.)]|^[-*•]/
        );
        

        if (this.recipeIngredients.length === 0 || this.recipeInstructions.length === 0) {
            this.parseFallbackFormat(lines);
        }
    },

    extractSection(lines, startIdx, endIdx, pattern) {
        if (startIdx < 0 || startIdx >= lines.length) return [];
        
        const sectionLines = lines.slice(startIdx + 1, endIdx);
        return sectionLines
            .map(line => {

                const cleaned = line.replace(/^(sastāvdaļas|ingredients|pagatavošana|gatavošanas|instrukcijas)[:.]?\s*/i, '');

                return cleaned.replace(pattern, '').trim();
            })
            .filter(line => line.length > 0);
    },

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

    parseFallbackFormat(lines) {

        const isNumberedList = lines.some(line => /^\d+[.)]/.test(line));
        const isBulletList = lines.some(line => /^[-*•]/.test(line));
        
        if (isNumberedList) {
            this.recipeInstructions = lines
                .filter(line => /^\d+[.)]/.test(line))
                .map(line => line.replace(/^\d+[.)]\s*/, '').trim());
            
            this.recipeIngredients = lines
                .filter(line => !/^\d+[.)]/.test(line) && line.trim().length > 0)
                .slice(1)
                .map(line => line.replace(/^[-*•]\s*/, '').trim());
        } 
        else if (isBulletList) {
            this.recipeIngredients = lines
                .slice(1)
                .map(line => line.replace(/^[-*•]\s*/, '').trim());
            
            this.recipeInstructions = ["Lūdzu skatīt sastāvdaļu sadaļā"];
        } 
        else {

            const sections = normalizedRecipe.split(/\n\s*\n/);
            if (sections.length > 1) {
                this.recipeIngredients = sections[1].split('\n').map(l => l.trim());
                this.recipeInstructions = sections.length > 2 
                    ? sections[2].split('\n').map(l => l.trim())
                    : ["Instrukcijas nav atrastas"];
            } else {

                this.recipeInstructions = lines.slice(1);
            }
        }
    }
  }
};
</script>

<style scoped>
.ai-recipe-page {
  max-width: 100%;
  margin: 0 auto;
  padding: 2rem 1rem;
  min-height: calc(100vh - 200px);
}

.hero-section {
  text-align: center;
  margin-bottom: 2.5rem;
  animation: fadeInDown 0.8s ease-out;
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

@keyframes fadeInDown {
  from {
    opacity: 0;
    transform: translateY(-30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.selection-container {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 2rem;
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

.ingredient-window {
  flex: 1;
  padding: 1.5rem;
  max-height: 60vh;
  overflow-y: auto;
  animation: fadeIn 0.6s ease-out;
}

.ingredient-window::-webkit-scrollbar {
  width: 8px;
}

.ingredient-window::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
}

.ingredient-window::-webkit-scrollbar-thumb {
  background: var(--primary-color);
  border-radius: 10px;
}

.action-section {
  width: 200px;
  display: flex;
  flex-direction: column;
  gap: 1rem;
  justify-content: flex-start;
  padding: 1.5rem;
  animation: fadeIn 0.8s ease-out;
}

.generate-btn {
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
  font-weight: 700;
}

.generate-btn:disabled {
  background: rgba(200, 200, 200, 0.5);
  cursor: not-allowed;
  transform: none;
}

.glass-btn-secondary {
  background: linear-gradient(135deg, var(--accent-color), #c0392b);
  backdrop-filter: blur(var(--glass-blur));
  border-radius: var(--radius-md);
  border: 1px solid rgba(230, 57, 70, 0.3);
  color: white;
  font-weight: 600;
  padding: 0.65rem 1.2rem;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 16px rgba(230, 57, 70, 0.3);
}

.glass-btn-secondary:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: 0 6px 20px rgba(230, 57, 70, 0.4);
  background: linear-gradient(135deg, #e74c3c, #a93226);
}

.glass-btn-secondary:disabled {
  background: rgba(200, 200, 200, 0.5);
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.ingredient-categories {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.category-card {
  animation: fadeIn 0.4s ease-out;
}

.category-header {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: none;
  border: none;
  border-bottom: 2px solid rgba(255, 107, 53, 0.3);
  padding: 0 0 0.5rem 0;
  margin-bottom: 1rem;
  cursor: pointer;
  gap: 0.5rem;
}

.category-header:hover .category-title {
  color: var(--primary-color);
}

.category-header-right {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  flex-shrink: 0;
}

.category-badge {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  font-size: 0.7rem;
  font-weight: 700;
  border-radius: 999px;
  padding: 0.1rem 0.45rem;
  min-width: 1.2rem;
  text-align: center;
  line-height: 1.5;
}

.chevron {
  color: var(--warm-dark);
  opacity: 0.5;
  transition: transform 0.25s ease;
  transform: rotate(-90deg);
}

.chevron.rotated {
  transform: rotate(0deg);
}

.category-title {
  font-size: 1.3rem;
  color: var(--warm-dark);
  font-weight: 700;
  transition: color 0.2s ease;
  text-align: left;
}

.selected-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
  max-height: 120px;
  overflow-y: auto;
  padding: 0.25rem 0;
}

.selected-chips::-webkit-scrollbar {
  width: 4px;
}

.selected-chips::-webkit-scrollbar-thumb {
  background: var(--primary-color);
  border-radius: 4px;
}

.chip {
  display: inline-flex;
  align-items: center;
  gap: 0.3rem;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  font-size: 0.78rem;
  font-weight: 600;
  padding: 0.25rem 0.55rem;
  border-radius: 999px;
  cursor: pointer;
  transition: opacity 0.2s ease, transform 0.15s ease;
  user-select: none;
}

.chip:hover {
  opacity: 0.8;
  transform: scale(0.96);
}

.chip svg {
  flex-shrink: 0;
  opacity: 0.85;
}

.ingredient-list {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
}

.ingredient-btn {
  background: rgba(255, 255, 255, 0.4);
  backdrop-filter: blur(10px);
  border: 2px solid rgba(255, 107, 53, 0.2);
  padding: 0.5rem 1rem;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  font-size: 0.95rem;
  font-weight: 500;
  color: var(--warm-dark);
}

.ingredient-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 107, 53, 0.2);
  border-color: var(--primary-color);
}

.ingredient-btn.selected {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  border-color: var(--primary-color);
  box-shadow: 0 4px 16px rgba(255, 107, 53, 0.4);
  transform: translateY(-2px);
}

.min-ingredient-warning {
  color: var(--accent-color);
  font-size: 0.9rem;
  font-weight: 600;
  text-align: center;
  margin: 0;
  padding: 0.5rem;
  background: rgba(230, 57, 70, 0.1);
  border-radius: var(--radius-md);
}

.preferences-toggle-wrap {
  position: relative;
  display: flex;
  justify-content: center;
}

.preferences-toggle {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 2px solid rgba(150, 150, 150, 0.35);
  background: rgba(150, 150, 150, 0.08);
  color: var(--warm-dark);
  cursor: pointer;
  transition: all 0.2s ease;
  opacity: 0.6;
  flex-shrink: 0;
}

.preferences-toggle:hover {
  opacity: 1;
  border-color: rgba(150, 150, 150, 0.6);
  transform: scale(1.08);
}

.preferences-toggle.active {
  border-color: rgba(76, 175, 80, 0.6);
  background: rgba(76, 175, 80, 0.12);
  color: #3d8b40;
  opacity: 1;
}

.pref-tooltip {
  position: absolute;
  bottom: calc(100% + 10px);
  left: 50%;
  transform: translateX(-50%) scale(0.92);
  background: rgba(30, 30, 30, 0.92);
  color: #fff;
  border-radius: 10px;
  padding: 0.6rem 0.85rem;
  font-size: 0.78rem;
  white-space: nowrap;
  pointer-events: none;
  opacity: 0;
  transition: opacity 0.18s ease, transform 0.18s ease;
  display: flex;
  flex-direction: column;
  gap: 0.2rem;
  z-index: 100;
  box-shadow: 0 4px 16px rgba(0,0,0,0.25);
}

.pref-tooltip::after {
  content: '';
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  border: 5px solid transparent;
  border-top-color: rgba(30, 30, 30, 0.92);
}

.pref-tooltip strong {
  font-size: 0.82rem;
}

.pref-tooltip span {
  opacity: 0.75;
}

.preferences-toggle-wrap:hover .pref-tooltip {
  opacity: 1;
  transform: translateX(-50%) scale(1);
}

.recipe-results {
  padding: 2rem;
  min-height: 300px;
  animation: fadeIn 0.6s ease-out;
}

.recipe-output {
  animation: fadeIn 0.6s ease-out;
}

.recipe-title {
  text-align: center;
  font-size: 2rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid rgba(255, 107, 53, 0.3);
  font-weight: 800;
}

.recipe-section {
  margin-bottom: 2rem;
}

.recipe-section h3 {
  font-size: 1.4rem;
  margin-bottom: 1rem;
  color: var(--warm-dark);
  font-weight: 700;
}

.ingredients-list, .instructions-list {
  text-align: left;
  padding-left: 2rem;
}

.ingredients-list li {
  margin-bottom: 0.5rem;
  font-size: 1rem;
  color: var(--warm-dark);
}

.instructions-list li {
  margin-bottom: 1rem;
  font-size: 1rem;
  line-height: 1.6;
  color: var(--warm-dark);
}

.generating-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
}

.generating-text {
  margin-top: 1rem;
  font-size: 1.1rem;
  color: var(--warm-dark);
  font-weight: 600;
}

.error-message {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.5rem;
  border: 2px solid var(--accent-color);
  border-radius: var(--radius-lg);
  animation: fadeIn 0.4s ease;
}

.error-message p {
  margin: 0;
  color: var(--accent-color);
  font-weight: 600;
  font-size: 1rem;
}

.error-icon {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
  fill: var(--accent-color);
}

.loading {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
  padding: 2rem;
  text-align: center;
}

.loading p {
  color: var(--warm-dark);
  font-size: 1rem;
  font-weight: 500;
  margin: 0;
}

.ai-warning {
  margin-top: 2rem;
  padding: 1rem 1.5rem;
  border: 2px solid var(--secondary-color);
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  gap: 1rem;
  animation: fadeIn 0.6s ease;
}

.ai-warning p {
  margin: 0;
  font-size: 0.95rem;
  line-height: 1.5;
  color: var(--warm-dark);
  font-weight: 500;
}

.warning-icon {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
  fill: var(--secondary-color);
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

@media (max-width: 1024px) {
  .selection-container {
    flex-direction: column;
  }

  .ingredient-window {
    max-height: 50vh;
  }

  .action-section {
    width: 100%;
    flex-direction: row;
    justify-content: center;
    padding: 1.5rem;
  }
}

@media (max-width: 768px) {
  .hero-section .gradient-text {
    font-size: 2rem;
  }

  .hero-subtitle {
    font-size: 1rem;
  }

  .ai-recipe-page {
    padding: 1.5rem 0.75rem;
  }

  .ingredient-window {
    padding: 1rem;
  }

  .category-title {
    font-size: 1.1rem;
  }

  .ingredient-btn {
    padding: 0.4rem 0.8rem;
    font-size: 0.9rem;
  }

  .action-section {
    gap: 0.8rem;
    padding: 1rem;
  }

  .generate-btn {
    padding: 0.65rem 1.2rem;
    font-size: 0.95rem;
  }

  .glass-btn-secondary {
    padding: 0.55rem 1rem;
    font-size: 0.9rem;
  }

  .recipe-results {
    padding: 1.5rem;
  }

  .recipe-title {
    font-size: 1.6rem;
  }

  .recipe-section h3 {
    font-size: 1.2rem;
  }
}

@media (max-width: 480px) {
  .hero-section .gradient-text {
    font-size: 1.75rem;
  }

  .hero-subtitle {
    font-size: 0.95rem;
  }

  .ai-recipe-page {
    padding: 1rem 0.5rem;
  }

  .selection-container {
    flex-direction: column;
    gap: 1rem;
  }

  .ingredient-window {
    padding: 1rem;
    max-height: 45vh;
  }

  .category-title {
    font-size: 1rem;
  }

  .ingredient-list {
    gap: 0.5rem;
  }

  .ingredient-btn {
    padding: 0.4rem 0.7rem;
    font-size: 0.85rem;
  }

  .action-section {
    flex-direction: column;
    width: 100%;
    gap: 0.8rem;
  }

  .generate-btn,
  .glass-btn-secondary {
    width: 100%;
    text-align: center;
  }

  .recipe-results {
    padding: 1rem;
  }

  .recipe-title {
    font-size: 1.5rem;
    padding-bottom: 0.8rem;
  }

  .recipe-section h3 {
    font-size: 1.1rem;
  }

  .ingredients-list,
  .instructions-list {
    padding-left: 1.5rem;
  }

  .ingredients-list li,
  .instructions-list li {
    font-size: 0.9rem;
  }

  .ai-warning {
    padding: 0.8rem 1rem;
  }

  .ai-warning p {
    font-size: 0.85rem;
  }

  .min-ingredient-warning {
    font-size: 0.85rem;
  }
}
</style>
