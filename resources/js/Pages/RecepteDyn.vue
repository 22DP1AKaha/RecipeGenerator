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

            <div v-if="isUserLoggedIn" class="comment-area">
              <textarea
                v-model="comment"
                class="comment-input glass-input"
                placeholder="Pievieno komentāru (neobligāti)..."
                rows="2"
                maxlength="500"
              ></textarea>
              <div class="comment-hint">Izvēlies zvaigznes, lai nosūtītu vērtējumu</div>
            </div>

            <button
              v-if="isUserLoggedIn && recipe.user_rating > 0"
              @click="deleteRating"
              class="delete-rating-btn"
            >Noņemt vērtējumu</button>
          </div>

          <div class="recipe-gallery" v-if="galleryImages.length > 0">
            <div class="gallery-main" @click="openLightbox(currentImageIndex)">
              <img :src="galleryImages[currentImageIndex]" :alt="recipe.title" class="gallery-image" />
              <button
                v-if="galleryImages.length > 1 && currentImageIndex > 0"
                class="gallery-arrow gallery-arrow--prev"
                @click.stop="prevImage"
              >&#8249;</button>
              <button
                v-if="galleryImages.length > 1 && currentImageIndex < galleryImages.length - 1"
                class="gallery-arrow gallery-arrow--next"
                @click.stop="nextImage"
              >&#8250;</button>
              <div class="gallery-zoom-hint">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                  <line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/>
                </svg>
              </div>
            </div>

            <div v-if="galleryImages.length > 1" class="gallery-dots">
              <button
                v-for="(img, i) in galleryImages"
                :key="i"
                class="gallery-dot"
                :class="{ active: i === currentImageIndex }"
                @click="currentImageIndex = i"
              ></button>
            </div>

            <div v-if="galleryImages.length > 1" class="gallery-thumbs">
              <img
                v-for="(img, i) in galleryImages"
                :key="i"
                :src="img"
                :alt="`${recipe.title} ${i + 1}`"
                class="gallery-thumb"
                :class="{ active: i === currentImageIndex }"
                @click="currentImageIndex = i"
              />
            </div>
          </div>

          <Teleport to="body">
            <div v-if="lightboxOpen" class="lightbox-overlay" @click.self="closeLightbox">
              <div class="lightbox-content">
                <button class="lightbox-close" @click="closeLightbox">✕</button>
                <img :src="galleryImages[lightboxIndex]" :alt="recipe.title" class="lightbox-image" />
                <button
                  v-if="galleryImages.length > 1 && lightboxIndex > 0"
                  class="lightbox-arrow lightbox-arrow--prev"
                  @click="lightboxIndex--"
                >&#8249;</button>
                <button
                  v-if="galleryImages.length > 1 && lightboxIndex < galleryImages.length - 1"
                  class="lightbox-arrow lightbox-arrow--next"
                  @click="lightboxIndex++"
                >&#8250;</button>
                <div class="lightbox-counter">{{ lightboxIndex + 1 }} / {{ galleryImages.length }}</div>
              </div>
            </div>
          </Teleport>

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

          <RecipeReviews :reviews="recipe.reviews ?? []" />

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
            <button
              @click="downloadPdf"
              class="glass-btn pdf-button"
              :disabled="downloadingPdf"
            >
              {{ downloadingPdf ? 'Lejupielādē...' : '📄 Lejupielādēt PDF' }}
            </button>
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
import RecipeReviews from "@/Components/RecipeReviews.vue";
import MainLayout from "@/Layouts/MainLayout.vue";
import axios from 'axios';
import { showToast } from '@/Composables/useToast';

export default {
  components: {
      MainLayout,
      RecDynIngredients,
      RecDynInstructions,
      RecipeReviews,
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
          comment: '',
          currentImageIndex: 0,
          lightboxOpen: false,
          lightboxIndex: 0,
          downloadingPdf: false,
      };
  },
  computed: {
      isUserLoggedIn() {
          return this.$page.props.auth.user !== null;
      },
      galleryImages() {
          if (this.recipe?.images?.length > 0) {
              return this.recipe.images.map(img => img.url);
          }
          if (this.recipe?.image) {
              return [this.recipe.image];
          }
          return [];
      },
  },
  methods: {
      prevImage() {
          if (this.currentImageIndex > 0) this.currentImageIndex--;
      },
      nextImage() {
          if (this.currentImageIndex < this.galleryImages.length - 1) this.currentImageIndex++;
      },
      openLightbox(index) {
          this.lightboxIndex = index;
          this.lightboxOpen = true;
      },
      closeLightbox() {
          this.lightboxOpen = false;
      },
      onKeydown(e) {
          if (!this.lightboxOpen) return;
          if (e.key === 'Escape') this.closeLightbox();
          if (e.key === 'ArrowLeft' && this.lightboxIndex > 0) this.lightboxIndex--;
          if (e.key === 'ArrowRight' && this.lightboxIndex < this.galleryImages.length - 1) this.lightboxIndex++;
      },
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
      async deleteRating() {
          try {
              const response = await axios.delete(`/ratings/${this.recipe.id}`, {
                  withCredentials: true
              });
              this.recipe.user_rating = 0;
              this.recipe.average_rating = response.data.average;
              this.comment = '';
              if (this.recipe.reviews) {
                  this.recipe.reviews = this.recipe.reviews.filter(r => !r.is_own);
              }
              showToast('Vērtējums noņemts!', 'success');
          } catch (error) {
              if (error.response && error.response.status === 401) {
                  window.location.href = '/ienakt';
              } else {
                  showToast('Neizdevās noņemt vērtējumu. Lūdzu mēģiniet vēlreiz.', 'error');
              }
          }
      },
      async rateRecipe(star) {
          try {
              const response = await axios.post('/ratings', {
                  recipe_id: this.recipe.id,
                  rating: star,
                  comment: this.comment.trim() || null,
              }, {
                  withCredentials: true
              });

              this.recipe.user_rating = star;
              this.recipe.average_rating = response.data.average;

              if (this.comment.trim()) {
                  const userName = this.$page.props.auth.user?.vards ?? 'Tu';
                  const today = new Date().toLocaleDateString('lv-LV', { day: '2-digit', month: '2-digit', year: 'numeric' });
                  const existing = this.recipe.reviews?.findIndex(r => r.is_own) ?? -1;
                  const reviewEntry = { user: userName, rating: star, comment: this.comment.trim(), created_at: today, is_own: true };
                  if (!this.recipe.reviews) this.recipe.reviews = [];
                  if (existing > -1) {
                      this.recipe.reviews.splice(existing, 1, reviewEntry);
                  } else {
                      this.recipe.reviews.unshift(reviewEntry);
                  }
              }

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
      async downloadPdf() {
          this.downloadingPdf = true;
          try {
              const { data } = await axios.get(`/api/recipes/${this.id}/pdf`);
              const link = document.createElement('a');
              link.href = `/api/pdf/serve/${data.token}`;
              link.setAttribute('download', `recepte-${this.id}.pdf`);
              document.body.appendChild(link);
              link.click();
              link.remove();
          } catch {
              showToast('Kļūda lejupielādējot PDF. Lūdzu mēģiniet vēlreiz.', 'error');
          } finally {
              this.downloadingPdf = false;
          }
      },
  },
  mounted() {
      window.addEventListener('keydown', this.onKeydown);
  },
  beforeUnmount() {
      window.removeEventListener('keydown', this.onKeydown);
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

.recipe-gallery {
  margin: 1.5rem 0;
}

.gallery-main {
  position: relative;
  display: flex;
  justify-content: center;
  cursor: zoom-in;
  border-radius: var(--radius-lg);
  overflow: hidden;
}

.gallery-image {
  width: 100%;
  max-width: 600px;
  max-height: 420px;
  object-fit: cover;
  border-radius: var(--radius-lg);
  box-shadow: 0 8px 24px rgba(255, 107, 53, 0.2);
  transition: transform 0.3s ease;
  display: block;
}

.gallery-main:hover .gallery-image {
  transform: scale(1.015);
}

.gallery-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0, 0, 0, 0.45);
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 42px;
  height: 42px;
  font-size: 1.6rem;
  line-height: 1;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s ease;
  z-index: 2;
  backdrop-filter: blur(4px);
}

.gallery-arrow:hover {
  background: rgba(255, 107, 53, 0.75);
}

.gallery-arrow--prev { left: 10px; }
.gallery-arrow--next { right: 10px; }

.gallery-zoom-hint {
  position: absolute;
  bottom: 10px;
  right: 14px;
  background: rgba(0, 0, 0, 0.4);
  color: #fff;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.2s ease;
  backdrop-filter: blur(4px);
}

.gallery-main:hover .gallery-zoom-hint {
  opacity: 1;
}

.gallery-dots {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  margin-top: 0.75rem;
}

.gallery-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  border: none;
  background: rgba(255, 107, 53, 0.25);
  cursor: pointer;
  padding: 0;
  transition: background 0.2s ease, transform 0.2s ease;
}

.gallery-dot.active {
  background: var(--primary-color);
  transform: scale(1.3);
}

.gallery-thumbs {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  margin-top: 0.75rem;
  flex-wrap: wrap;
}

.gallery-thumb {
  width: 64px;
  height: 48px;
  object-fit: cover;
  border-radius: 8px;
  cursor: pointer;
  opacity: 0.55;
  border: 2px solid transparent;
  transition: opacity 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
}

.gallery-thumb:hover {
  opacity: 0.85;
  transform: scale(1.05);
}

.gallery-thumb.active {
  opacity: 1;
  border-color: var(--primary-color);
}

.lightbox-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.88);
  z-index: 9000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  animation: fadeIn 0.2s ease;
}

.lightbox-content {
  position: relative;
  max-width: 90vw;
  max-height: 90vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.lightbox-image {
  max-width: 90vw;
  max-height: 85vh;
  object-fit: contain;
  border-radius: var(--radius-lg);
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
}

.lightbox-close {
  position: fixed;
  top: 1.25rem;
  right: 1.25rem;
  background: rgba(255, 255, 255, 0.12);
  border: none;
  color: #fff;
  font-size: 1.3rem;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s ease;
  backdrop-filter: blur(6px);
}

.lightbox-close:hover {
  background: rgba(255, 255, 255, 0.25);
}

.lightbox-arrow {
  position: fixed;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(255, 255, 255, 0.12);
  color: #fff;
  border: none;
  border-radius: 50%;
  width: 52px;
  height: 52px;
  font-size: 2rem;
  line-height: 1;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s ease;
  backdrop-filter: blur(6px);
}

.lightbox-arrow:hover {
  background: rgba(255, 107, 53, 0.6);
}

.lightbox-arrow--prev { left: 1rem; }
.lightbox-arrow--next { right: 1rem; }

.lightbox-counter {
  position: fixed;
  bottom: 1.5rem;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(255, 255, 255, 0.12);
  color: #fff;
  font-size: 0.9rem;
  font-weight: 600;
  padding: 0.35rem 0.9rem;
  border-radius: 20px;
  backdrop-filter: blur(6px);
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

.recipe-ingredients :deep(ul),
.recipe-instructions :deep(ul) {
  padding-left: 0;
  margin-top: 1rem;
}

.recipe-ingredients :deep(li),
.recipe-instructions :deep(li) {
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

.comment-area {
  width: 100%;
  max-width: 480px;
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.comment-input {
  width: 100%;
  resize: vertical;
  min-height: 60px;
  font-size: 0.92rem;
  box-sizing: border-box;
}

.comment-hint {
  font-size: 0.75rem;
  color: var(--warm-dark);
  opacity: 0.45;
  text-align: center;
}

.delete-rating-btn {
  background: none;
  border: none;
  color: #c0392b;
  font-size: 0.85rem;
  cursor: pointer;
  text-decoration: underline;
  padding: 0;
  opacity: 0.75;
  transition: opacity 0.2s ease;
}

.delete-rating-btn:hover {
  opacity: 1;
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

.pdf-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(255, 107, 53, 0.4);
}

.pdf-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
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

  .gallery-image {
    max-height: 260px;
  }

  .gallery-arrow {
    width: 34px;
    height: 34px;
    font-size: 1.3rem;
  }

  .gallery-thumb {
    width: 52px;
    height: 40px;
  }

  .lightbox-arrow {
    width: 40px;
    height: 40px;
    font-size: 1.6rem;
  }
}
</style>
