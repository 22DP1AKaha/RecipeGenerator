<template>
  <MainLayout>
    <div class="profile-page">
      <div class="profile-container">

        <div class="profile-header glass-card">
          <div class="avatar">
            {{ avatarLetter }}
          </div>
          <div class="user-info">
            <h1 class="gradient-text">{{ props.user.vards }}</h1>
            <p class="user-email">{{ props.user.email }}</p>
          </div>
        </div>

        <form @submit.prevent="submit">
          <div class="section glass-card">
            <h2 class="section-title">Konta Informācija</h2>

            <div class="form-group">
              <label class="form-label">Vārds</label>
              <input
                v-model="form.vards"
                type="text"
                class="form-input glass-input"
                :class="{ 'is-invalid': form.errors.vards }"
                required
              />
              <div v-if="form.errors.vards" class="invalid-feedback">{{ form.errors.vards }}</div>
            </div>

            <div class="form-group">
              <label class="form-label">E-pasts</label>
              <input
                v-model="form.email"
                type="email"
                class="form-input glass-input disabled-input"
                disabled
              />
              <p class="field-hint">E-pasta adrese nav maināma.</p>
            </div>
          </div>

          <div class="section glass-card">
            <UpdatePasswordForm />
          </div>

          <div class="section glass-card">
            <h2 class="section-title">Diētas Ierobežojumi</h2>
            <div class="option-grid">
              <label
                v-for="opt in dietas"
                :key="opt.id"
                class="option-card"
                :class="{ 'option-card--selected': form.dietas_ierobezojumi.includes(opt.id) }"
              >
                <input
                  type="checkbox"
                  :value="opt.id"
                  v-model="form.dietas_ierobezojumi"
                />
                <span class="checkmark"></span>
                {{ opt.name }}
              </label>
            </div>
          </div>

          <div class="section glass-card">
            <h2 class="section-title">Alerģijas</h2>
            <div class="option-grid">
              <label
                v-for="opt in alergijas"
                :key="opt.id"
                class="option-card"
                :class="{ 'option-card--selected': form.alergijas.includes(opt.id) }"
              >
                <input
                  type="checkbox"
                  :value="opt.id"
                  v-model="form.alergijas"
                />
                <span class="checkmark"></span>
                {{ opt.name }}
              </label>
            </div>
          </div>

          <div class="actions-row">
            <button
              type="submit"
              class="btn glass-btn save-btn"
              :disabled="form.processing"
            >
              <span v-if="form.processing">Saglabā...</span>
              <span v-else>Saglabāt Izmaiņas</span>
            </button>

            <DeleteUserForm />
          </div>
        </form>

      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import { showToast } from '@/Composables/useToast';

const props = defineProps({
  user: Object,
  dietas: Array,
  alergijas: Array,
});

const avatarLetter = computed(() => props.user.vards?.charAt(0).toUpperCase() || '?');

const form = useForm({
  vards: props.user.vards,
  email: props.user.email,
  dietas_ierobezojumi: props.user.dietas_ierobezojumi,
  alergijas: props.user.alergijas,
});

const page = usePage();

watch(
  () => page.props.flash?.status,
  (newStatus) => {
    if (newStatus === 'profils-atjauninats') {
      showToast('Profils veiksmīgi atjaunināts!', 'success');
    }
  }
);

function submit() {
  form.patch(route('profile.update'), {
    onError: () => {
      showToast('Kļūda saglabājot profilu. Lūdzu mēģiniet vēlreiz.', 'error');
    },
  });
}
</script>

<style scoped>
.profile-page {
  min-height: 100vh;
  padding: 2rem 1rem 4rem;
  background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
  background-attachment: fixed;
}

.profile-container {
  max-width: 760px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.profile-container form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* Header */
.profile-header {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  padding: 2rem;
  animation: fadeIn 0.5s ease-out;
}

.avatar {
  width: 72px;
  height: 72px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: 700;
  color: white;
  flex-shrink: 0;
  box-shadow: 0 4px 16px rgba(255, 107, 53, 0.35);
}

.user-info h1 {
  font-size: 1.75rem;
  font-weight: 800;
  margin: 0 0 0.25rem;
}

.user-email {
  color: var(--warm-dark);
  opacity: 0.65;
  font-size: 0.95rem;
  margin: 0;
}

/* Sections */
.section {
  padding: 2rem;
  animation: fadeIn 0.5s ease-out;
}

.section-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: var(--warm-dark);
  margin: 0 0 1.5rem;
  padding-bottom: 0.75rem;
  border-bottom: 2px solid rgba(255, 107, 53, 0.15);
}

/* Form */
.form-group {
  margin-bottom: 1.25rem;
}

.form-label {
  display: block;
  font-weight: 600;
  color: var(--warm-dark);
  margin-bottom: 0.5rem;
  font-size: 0.95rem;
}

.form-input {
  width: 100%;
  box-sizing: border-box;
}

.disabled-input {
  opacity: 0.6;
  cursor: not-allowed;
}

.field-hint {
  color: var(--warm-dark);
  opacity: 0.5;
  font-size: 0.8rem;
  margin: 0.4rem 0 0;
}

.is-invalid {
  border-color: var(--accent-color) !important;
}

.invalid-feedback {
  color: var(--accent-color);
  font-size: 0.85rem;
  margin-top: 0.3rem;
  font-weight: 600;
}

/* Option cards */
.option-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 0.75rem;
}

.option-card {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.75rem 1rem;
  border-radius: 10px;
  cursor: pointer;
  border: 2px solid rgba(255, 107, 53, 0.15);
  background: rgba(255, 255, 255, 0.4);
  transition: all 0.2s ease;
  font-size: 0.9rem;
  font-weight: 500;
  color: var(--warm-dark);
  position: relative;
}

.option-card:hover {
  border-color: rgba(255, 107, 53, 0.4);
  background: rgba(255, 107, 53, 0.07);
  transform: translateY(-1px);
}

.option-card--selected {
  border-color: var(--primary-color);
  background: rgba(255, 107, 53, 0.1);
}

.option-card input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  width: 0;
  height: 0;
}

.checkmark {
  width: 18px;
  height: 18px;
  border-radius: 4px;
  border: 2px solid rgba(255, 107, 53, 0.4);
  background: white;
  flex-shrink: 0;
  transition: all 0.2s ease;
  position: relative;
}

.option-card input:checked ~ .checkmark {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  border-color: var(--primary-color);
}

.option-card input:checked ~ .checkmark::after {
  content: '';
  position: absolute;
  left: 4px;
  top: 1px;
  width: 5px;
  height: 9px;
  border: solid white;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
}

/* Actions */
.actions-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
}

.save-btn {
  padding: 0.75rem 2rem;
  font-size: 1rem;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}

@media (max-width: 576px) {
  .profile-header {
    flex-direction: column;
    text-align: center;
    padding: 1.5rem;
  }

  .section {
    padding: 1.5rem;
  }

  .actions-row {
    flex-direction: column;
    align-items: stretch;
  }

  .save-btn {
    width: 100%;
  }
}
</style>
