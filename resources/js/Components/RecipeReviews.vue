<template>
  <div class="reviews-section glass-card">
    <h3 class="reviews-title">
      Atsauksmes
      <span class="reviews-count">{{ reviews.length }}</span>
    </h3>

    <div v-if="reviews.length === 0" class="no-reviews">
      <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="no-reviews-icon">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
      </svg>
      <p>Vēl nav nevienas atsauksmes. Esi pirmais!</p>
    </div>

    <div v-else class="reviews-list">
      <div
        v-for="(review, index) in reviews"
        :key="index"
        class="review-card"
        :class="{ 'review-card--own': review.is_own }"
      >
        <div class="review-header">
          <div class="reviewer-info">
            <div class="reviewer-avatar">{{ review.user.charAt(0).toUpperCase() }}</div>
            <div>
              <span class="reviewer-name">
                {{ review.user }}
                <span v-if="review.is_own" class="own-badge">Tu</span>
              </span>
              <span class="review-date">{{ review.created_at }}</span>
            </div>
          </div>
          <div class="review-stars">
            <span
              v-for="star in 5"
              :key="star"
              class="review-star"
              :class="{ filled: star <= review.rating }"
            >★</span>
          </div>
        </div>
        <p class="review-comment">{{ review.comment }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  reviews: {
    type: Array,
    default: () => [],
  },
});
</script>

<style scoped>
.reviews-section {
  padding: 1.75rem;
  margin-top: 1.5rem;
}

.reviews-title {
  font-size: 1.4rem;
  font-weight: 700;
  color: var(--warm-dark);
  margin: 0 0 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.6rem;
}

.reviews-count {
  background: rgba(255, 107, 53, 0.12);
  color: var(--primary-color);
  font-size: 0.85rem;
  font-weight: 700;
  padding: 0.15rem 0.55rem;
  border-radius: 20px;
}

.no-reviews {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  padding: 2rem 1rem;
  color: var(--warm-dark);
  opacity: 0.45;
  text-align: center;
}

.no-reviews-icon {
  opacity: 0.6;
}

.no-reviews p {
  margin: 0;
  font-size: 0.95rem;
}

.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.review-card {
  padding: 1.25rem;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.45);
  border: 1px solid rgba(255, 255, 255, 0.35);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.review-card:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(255, 107, 53, 0.1);
}

.review-card--own {
  border-color: rgba(255, 107, 53, 0.3);
  background: rgba(255, 107, 53, 0.05);
}

.review-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 0.75rem;
}

.reviewer-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.reviewer-avatar {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  font-weight: 700;
  font-size: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.reviewer-name {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  font-weight: 700;
  color: var(--warm-dark);
  font-size: 0.95rem;
}

.own-badge {
  font-size: 0.68rem;
  background: rgba(255, 107, 53, 0.15);
  color: var(--primary-color);
  border-radius: 20px;
  padding: 0.1rem 0.45rem;
  font-weight: 700;
}

.review-date {
  display: block;
  font-size: 0.78rem;
  color: var(--warm-dark);
  opacity: 0.5;
  margin-top: 0.1rem;
}

.review-stars {
  display: flex;
  gap: 2px;
  flex-shrink: 0;
}

.review-star {
  font-size: 1rem;
  color: rgba(255, 107, 53, 0.2);
}

.review-star.filled {
  color: var(--secondary-color);
}

.review-comment {
  margin: 0;
  font-size: 0.95rem;
  line-height: 1.6;
  color: var(--warm-dark);
  opacity: 0.85;
}

@media (max-width: 480px) {
  .review-header {
    flex-direction: column;
    gap: 0.5rem;
  }

  .review-stars {
    margin-left: calc(38px + 0.75rem);
  }
}
</style>
