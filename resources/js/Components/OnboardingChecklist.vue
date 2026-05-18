<template>
  <div v-if="visible" class="checklist-card glass-card">
    <div class="checklist-header">
      <div>
        <h3 class="checklist-title">Iesācēja ceļvedis</h3>
        <p class="checklist-sub">{{ completedCount }} / {{ steps.length }} izpildīts</p>
      </div>
      <button class="checklist-close" @click="dismiss" title="Aizvērt">✕</button>
    </div>

    <div class="progress-bar">
      <div class="progress-fill" :style="{ width: (completedCount / steps.length * 100) + '%' }" />
    </div>

    <ul class="checklist-steps">
      <li v-for="s in steps" :key="s.key" class="step-item" :class="{ done: s.done }">
        <span class="step-check">
          <svg v-if="s.done" xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </span>
        <span class="step-label">{{ s.label }}</span>
        <a v-if="!s.done && s.link" :href="s.link" class="step-link">Doties →</a>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'OnboardingChecklist',
  props: {
    userId:         { type: [Number, String], required: true },
    hasPreferences: { type: Boolean, default: false },
    hasFavorites:   { type: Boolean, default: false },
  },
  data() {
    return { dismissed: false };
  },
  computed: {
    steps() {
      const uid = this.userId;
      return [
        {
          key:   'registered',
          label: 'Izveidot kontu',
          done:  true,
          link:  null,
        },
        {
          key:   'preferences',
          label: 'Pievienot uztura prasības vai alerģijas',
          done:  this.hasPreferences,
          link:  '/profils',
        },
        {
          key:   'favorites',
          label: 'Saglabāt recepti favorītos',
          done:  this.hasFavorites,
          link:  '/receptes',
        },
        {
          key:   'shopping',
          label: 'Ģenerēt iepirkumu sarakstu',
          done:  !!localStorage.getItem(`foodyml_list_${uid}`),
          link:  '/iepirkumi',
        },
        {
          key:   'ai',
          label: 'Izmēģināt AI recepti',
          done:  !!localStorage.getItem(`foodyml_ai_${uid}`),
          link:  '/aireceptes',
        },
      ];
    },
    completedCount() {
      return this.steps.filter(s => s.done).length;
    },
    allDone() {
      return this.completedCount === this.steps.length;
    },
    visible() {
      if (this.dismissed) return false;
      if (localStorage.getItem(`foodyml_checklist_done_${this.userId}`)) return false;
      return !this.allDone;
    },
  },
  watch: {
    allDone(val) {
      if (val) localStorage.setItem(`foodyml_checklist_done_${this.userId}`, '1');
    },
  },
  methods: {
    dismiss() {
      localStorage.setItem(`foodyml_checklist_done_${this.userId}`, '1');
      this.dismissed = true;
    },
  },
};
</script>

<style scoped>
.checklist-card {
  max-width: 420px;
  margin: 0 auto 2.5rem;
  padding: 1.25rem 1.5rem;
  animation: fadeIn 0.4s ease;
}

.checklist-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.75rem;
}

.checklist-title {
  font-size: 1rem;
  font-weight: 700;
  color: var(--warm-dark);
  margin: 0 0 0.15rem;
}

.checklist-sub {
  font-size: 0.8rem;
  color: var(--primary-color);
  font-weight: 600;
  margin: 0;
}

.checklist-close {
  background: none;
  border: none;
  font-size: 0.85rem;
  color: var(--warm-dark);
  opacity: 0.4;
  cursor: pointer;
  transition: opacity 0.2s;
  line-height: 1;
  padding: 0;
}
.checklist-close:hover { opacity: 0.8; }

.progress-bar {
  height: 4px;
  background: rgba(255, 107, 53, 0.12);
  border-radius: 2px;
  margin-bottom: 1rem;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
  border-radius: 2px;
  transition: width 0.4s ease;
}

.checklist-steps {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.step-item {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  font-size: 0.88rem;
  color: var(--warm-dark);
}

.step-check {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 1.5px solid rgba(255, 107, 53, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.2s;
}

.step-item.done .step-check {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  border-color: transparent;
  color: white;
}

.step-item.done .step-label {
  text-decoration: line-through;
  opacity: 0.45;
}

.step-label {
  flex: 1;
}

.step-link {
  font-size: 0.78rem;
  font-weight: 600;
  color: var(--primary-color);
  text-decoration: none;
  white-space: nowrap;
  opacity: 0.8;
  transition: opacity 0.2s;
}
.step-link:hover { opacity: 1; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>
