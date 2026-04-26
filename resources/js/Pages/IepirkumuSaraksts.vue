<template>
  <MainLayout>
  <div class="page-wrapper">
    <div class="page-header">
      <h1 class="gradient-text">Iepirkumu saraksts</h1>
      <p class="page-subtitle">Izvēlies receptes un porciju skaitu, lai ģenerētu apkopotu sastāvdaļu sarakstu.</p>
    </div>

    <div class="main-grid">

      <!-- LEFT: Recipe picker -->
      <div class="glass-card picker-card">
        <h2 class="section-title">Receptes</h2>

        <div class="search-box">
          <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            class="glass-input search-input"
            placeholder="Meklēt recepti..."
          />
        </div>

        <div v-if="loadingRecipes" class="empty-state">
          <div class="spinner"></div>
          <span>Ielādē receptes...</span>
        </div>

        <div v-else-if="filteredRecipes.length === 0" class="empty-state">
          <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
          </svg>
          <span>Nav atrastas receptes</span>
        </div>

        <div v-else class="recipe-list">
          <div
            v-for="recipe in filteredRecipes"
            :key="recipe.id"
            class="recipe-row"
            :class="{ 'recipe-row--added': isSelected(recipe.id) }"
            @click="toggleRecipe(recipe)"
          >
            <div class="recipe-thumb">
              <img v-if="recipe.image" :src="recipe.image" :alt="recipe.title" />
              <div v-else class="recipe-thumb-placeholder">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M3 2l1.5 18L12 22l7.5-2L21 2z"/><path d="M9 2v3M15 2v3"/>
                </svg>
              </div>
            </div>
            <span class="recipe-row-name">{{ recipe.title }}</span>
            <div class="recipe-row-action">
              <svg v-if="!isSelected(recipe.id)" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                   viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                   stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/>
                <line x1="8" y1="12" x2="16" y2="12"/>
              </svg>
              <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                   viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                   stroke-linecap="round" stroke-linejoin="round" class="added-icon">
                <circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- RIGHT: Selected recipes + generate -->
      <div class="right-col">

        <div class="glass-card selected-card">
          <h2 class="section-title">Izvēlētās receptes</h2>

          <div v-if="selectedRecipes.length === 0" class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
              <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
              <path d="M16 10a4 4 0 0 1-8 0"/>
            </svg>
            <span>Nav izvēlētu recepšu</span>
            <span class="empty-hint">Nospied recepti kreisajā pusē</span>
          </div>

          <div v-else class="selected-list">
            <div v-for="item in selectedRecipes" :key="item.id" class="selected-row">
              <span class="selected-name">{{ item.title }}</span>
              <div class="portions-control">
                <button class="portions-btn" @click="decrement(item)" :disabled="item.portions <= 1">−</button>
                <span class="portions-value">{{ item.portions }}x</span>
                <button class="portions-btn" @click="increment(item)" :disabled="item.portions >= 20">+</button>
              </div>
              <button class="remove-btn" @click="removeRecipe(item.id)" title="Noņemt">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
              </button>
            </div>
          </div>

          <button
            class="generate-btn"
            :disabled="selectedRecipes.length === 0 || generating"
            @click="generateList"
          >
            <span v-if="generating">
              <span class="btn-spinner"></span> Ģenerē...
            </span>
            <span v-else>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                   fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                   style="vertical-align:-2px; margin-right:6px">
                <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 0 1-8 0"/>
              </svg>
              Ģenerēt sarakstu
            </span>
          </button>
        </div>

      </div>
    </div>

    <!-- Shopping list results -->
    <div v-if="shoppingList.length > 0" class="glass-card results-card" ref="resultsCard">
      <div class="results-header">
        <h2 class="section-title" style="margin-bottom:0">Iepirkumu saraksts</h2>
        <button class="pdf-btn" @click="downloadPdf" :disabled="downloadingPdf">
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
               fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
               style="vertical-align:-2px; margin-right:5px">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
          </svg>
          {{ downloadingPdf ? 'Lejupielādē...' : 'Lejupielādēt PDF' }}
        </button>
      </div>

      <div class="results-grid">
        <div v-for="group in shoppingList" :key="group.category" class="category-group">
          <div class="category-title">{{ group.category }}</div>
          <div v-for="ing in group.ingredients" :key="ing.name" class="ingredient-row">
            <label class="ingredient-check-label">
              <input type="checkbox" class="ingredient-checkbox" />
              <span class="ingredient-qty">{{ ing.quantity }} {{ ing.unit }}</span>
              <span class="ingredient-name">{{ ing.name }}</span>
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- Error message -->
    <div v-if="errorMsg" class="error-banner">{{ errorMsg }}</div>
  </div>
  </MainLayout>
</template>

<script>
import axios from 'axios';
import MainLayout from '@/Layouts/MainLayout.vue';
import { showToast } from '@/Composables/useToast';

export default {
  name: 'IepirkumuSaraksts',
  components: { MainLayout },

  data() {
    return {
      allRecipes: [],
      searchQuery: '',
      selectedRecipes: [],
      shoppingList: [],
      loadingRecipes: true,
      generating: false,
      downloadingPdf: false,
      errorMsg: '',
    };
  },

  computed: {
    filteredRecipes() {
      const q = this.searchQuery.trim().toLowerCase();
      if (!q) return this.allRecipes;
      return this.allRecipes.filter(r => r.title.toLowerCase().includes(q));
    },
  },

  mounted() {
    this.loadRecipes();
  },

  methods: {
    async loadRecipes() {
      try {
        const { data } = await axios.get('/api/recipes', { params: { per_page: 200 } });
        this.allRecipes = data.data ?? [];
      } catch {
        this.errorMsg = 'Neizdevās ielādēt receptes.';
      } finally {
        this.loadingRecipes = false;
      }
    },

    isSelected(id) {
      return this.selectedRecipes.some(r => r.id === id);
    },

    toggleRecipe(recipe) {
      if (this.isSelected(recipe.id)) {
        this.removeRecipe(recipe.id);
      } else {
        this.selectedRecipes.push({ id: recipe.id, title: recipe.title, portions: 1 });
      }
    },

    removeRecipe(id) {
      this.selectedRecipes = this.selectedRecipes.filter(r => r.id !== id);
    },

    increment(item) {
      if (item.portions < 20) item.portions++;
    },

    decrement(item) {
      if (item.portions > 1) item.portions--;
    },

    async generateList() {
      this.generating = true;
      this.errorMsg = '';
      this.shoppingList = [];

      try {
        const { data } = await axios.post('/api/shopping-list', {
          recipes: this.selectedRecipes.map(r => ({ recipe_id: r.id, portions: r.portions })),
        });
        this.shoppingList = data.list;
        this.$nextTick(() => {
          this.$refs.resultsCard?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
      } catch (e) {
        this.errorMsg = e.response?.data?.message ?? 'Kļūda ģenerējot sarakstu.';
      } finally {
        this.generating = false;
      }
    },

    async downloadPdf() {
      this.downloadingPdf = true;
      try {
        const response = await axios.post(
          '/api/shopping-list/pdf',
          { recipes: this.selectedRecipes.map(r => ({ recipe_id: r.id, portions: r.portions })) },
          { responseType: 'blob' }
        );
        const url  = window.URL.createObjectURL(response.data);
        const link = document.createElement('a');
        link.href     = url;
        link.download = 'iepirkumu-saraksts.pdf';
        document.body.appendChild(link);
        link.click();
        link.remove();
        setTimeout(() => window.URL.revokeObjectURL(url), 100);
      } catch {
        showToast('Kļūda lejupielādējot PDF. Lūdzu mēģiniet vēlreiz.', 'error');
      } finally {
        this.downloadingPdf = false;
      }
    },
  },
};
</script>

<style scoped>
.page-wrapper {
  max-width: 1100px;
  margin: 0 auto;
  padding: 2rem 1rem 4rem;
}

.page-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.page-header h1 {
  font-size: 2.2rem;
  font-weight: 800;
  margin-bottom: 0.5rem;
}

.page-subtitle {
  color: #666;
  font-size: 1rem;
}

.main-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
  .main-grid {
    grid-template-columns: 1fr;
  }
}

.picker-card,
.selected-card,
.results-card {
  padding: 1.5rem;
}

.right-col {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.section-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--warm-dark);
  margin-bottom: 1rem;
}

/* Search */
.search-box {
  position: relative;
  margin-bottom: 1rem;
}

.search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #999;
  pointer-events: none;
}

.search-input {
  width: 100%;
  padding-left: 36px !important;
}

/* Recipe list */
.recipe-list {
  max-height: 380px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  padding-right: 4px;
}

.recipe-list::-webkit-scrollbar { width: 4px; }
.recipe-list::-webkit-scrollbar-track { background: transparent; }
.recipe-list::-webkit-scrollbar-thumb { background: rgba(255,107,53,0.3); border-radius: 2px; }

.recipe-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.6rem 0.75rem;
  border-radius: 10px;
  cursor: pointer;
  transition: background 0.2s ease;
  border: 1.5px solid transparent;
}

.recipe-row:hover {
  background: rgba(255, 107, 53, 0.07);
}

.recipe-row--added {
  background: rgba(255, 107, 53, 0.08);
  border-color: rgba(255, 107, 53, 0.25);
}

.recipe-thumb {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
  background: rgba(255,107,53,0.08);
  display: flex;
  align-items: center;
  justify-content: center;
}

.recipe-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.recipe-thumb-placeholder {
  color: rgba(255,107,53,0.4);
}

.recipe-row-name {
  flex: 1;
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--warm-dark);
  line-height: 1.3;
}

.recipe-row-action {
  color: #ccc;
  flex-shrink: 0;
  display: flex;
  align-items: center;
}

.added-icon {
  color: #2ecc71;
}

/* Selected list */
.selected-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-bottom: 1.25rem;
  max-height: 240px;
  overflow-y: auto;
  padding-right: 4px;
}

.selected-list::-webkit-scrollbar { width: 4px; }
.selected-list::-webkit-scrollbar-thumb { background: rgba(255,107,53,0.3); border-radius: 2px; }

.selected-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem 0.75rem;
  background: rgba(255, 107, 53, 0.06);
  border-radius: 10px;
  border: 1px solid rgba(255, 107, 53, 0.15);
}

.selected-name {
  flex: 1;
  font-size: 0.88rem;
  font-weight: 500;
  color: var(--warm-dark);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.portions-control {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  flex-shrink: 0;
}

.portions-btn {
  width: 26px;
  height: 26px;
  border-radius: 50%;
  border: 1.5px solid rgba(255,107,53,0.4);
  background: transparent;
  color: var(--primary-color);
  font-size: 1rem;
  line-height: 1;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  padding: 0;
}

.portions-btn:hover:not(:disabled) {
  background: rgba(255,107,53,0.15);
  border-color: var(--primary-color);
}

.portions-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.portions-value {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--warm-dark);
  min-width: 28px;
  text-align: center;
}

.remove-btn {
  width: 24px;
  height: 24px;
  border-radius: 6px;
  border: none;
  background: transparent;
  color: #aaa;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  flex-shrink: 0;
  transition: all 0.2s;
}

.remove-btn:hover {
  background: rgba(231,76,60,0.1);
  color: #e74c3c;
}

/* Generate button */
.generate-btn {
  width: 100%;
  padding: 0.85rem;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  border: none;
  border-radius: 14px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 14px rgba(255,107,53,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  margin-top: auto;
}

.generate-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 18px rgba(255,107,53,0.4);
}

.generate-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

/* Results */
.results-card {
  margin-top: 0;
}

.results-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  gap: 0.75rem;
}

.pdf-btn {
  padding: 0.55rem 1.25rem;
  background: transparent;
  border: 1.5px solid rgba(255,107,53,0.5);
  border-radius: 10px;
  color: var(--primary-color);
  font-size: 0.88rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
}

.pdf-btn:hover:not(:disabled) {
  background: rgba(255,107,53,0.1);
  border-color: var(--primary-color);
}

.pdf-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.results-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1.25rem;
}

.category-group {
  background: rgba(255, 107, 53, 0.04);
  border: 1px solid rgba(255, 107, 53, 0.12);
  border-radius: 12px;
  padding: 1rem;
}

.category-title {
  font-size: 0.8rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--primary-color);
  margin-bottom: 0.75rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid rgba(255,107,53,0.15);
}

.ingredient-row {
  padding: 0.3rem 0;
}

.ingredient-check-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-size: 0.88rem;
}

.ingredient-checkbox {
  width: 15px;
  height: 15px;
  accent-color: var(--primary-color);
  flex-shrink: 0;
  cursor: pointer;
}

.ingredient-check-label:has(.ingredient-checkbox:checked) .ingredient-name,
.ingredient-check-label:has(.ingredient-checkbox:checked) .ingredient-qty {
  opacity: 0.4;
  text-decoration: line-through;
}

.ingredient-qty {
  font-weight: 600;
  color: var(--warm-dark);
  min-width: 60px;
  font-size: 0.85rem;
  white-space: nowrap;
}

.ingredient-name {
  color: var(--warm-dark);
}

/* Empty state */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  padding: 2rem 1rem;
  color: #aaa;
  text-align: center;
  font-size: 0.9rem;
}

.empty-hint {
  font-size: 0.8rem;
  color: #ccc;
}

/* Error */
.error-banner {
  margin-top: 1rem;
  padding: 0.75rem 1rem;
  background: rgba(231,76,60,0.1);
  border: 1px solid rgba(231,76,60,0.3);
  border-radius: 10px;
  color: #e74c3c;
  font-size: 0.9rem;
  text-align: center;
}

/* Spinners */
.spinner {
  width: 24px;
  height: 24px;
  border: 3px solid rgba(255,107,53,0.2);
  border-top-color: var(--primary-color);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

.btn-spinner {
  display: inline-block;
  width: 14px;
  height: 14px;
  border: 2px solid rgba(255,255,255,0.4);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
  vertical-align: -2px;
  margin-right: 6px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
