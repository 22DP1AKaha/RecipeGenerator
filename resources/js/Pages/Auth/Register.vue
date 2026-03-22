<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
  vards: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('register.post'), {
    onSuccess: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <div class="register-page">
    <Head title="Register" />
    <div class="container">
      <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
          <div class="register-card glass-card">
            <div class="logo-container text-center mb-4">
              <img src="/foodyML_logo.png" alt="FoodyML Logo" class="logo-img" />
              <h1 class="gradient-text mb-0">FOODYML</h1>
              <p class="welcome-text mt-2">Izveido savu kontu</p>
            </div>

            <div v-if="form.errors.error" class="alert alert-danger glass-card p-3 mb-3 text-center">
              {{ form.errors.error }}
            </div>

            <form @submit.prevent="submit" class="register-form">
              <div class="mb-3">
                <label for="vards" class="form-label">Vārds</label>
                <input
                  type="text"
                  id="vards"
                  v-model="form.vards"
                  placeholder="Tavs vārds"
                  class="form-control glass-input"
                  :class="{ 'is-invalid': form.errors.vards }"
                  required
                />
                <div v-if="form.errors.vards" class="invalid-feedback d-block">
                  {{ form.errors.vards }}
                </div>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">E-pasts</label>
                <input
                  type="email"
                  id="email"
                  v-model="form.email"
                  placeholder="tavs@epasts.lv"
                  class="form-control glass-input"
                  :class="{ 'is-invalid': form.errors.email }"
                  required
                />
                <div v-if="form.errors.email" class="invalid-feedback d-block">
                  {{ form.errors.email }}
                </div>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Parole</label>
                <input
                  type="password"
                  id="password"
                  v-model="form.password"
                  placeholder="••••••••"
                  class="form-control glass-input"
                  :class="{ 'is-invalid': form.errors.password }"
                  required
                />
                <div v-if="form.errors.password" class="invalid-feedback d-block">
                  {{ Array.isArray(form.errors.password) ? form.errors.password[0] : form.errors.password }}
                </div>
              </div>

              <div class="mb-4">
                <label for="password_confirmation" class="form-label">Apstipriniet paroli</label>
                <input
                  type="password"
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  placeholder="••••••••"
                  class="form-control glass-input"
                  :class="{ 'is-invalid': form.errors.password_confirmation }"
                  required
                />
                <div v-if="form.errors.password_confirmation" class="invalid-feedback d-block">
                  {{ form.errors.password_confirmation }}
                </div>
              </div>

              <button
                type="submit"
                class="btn glass-btn w-100 mb-3"
                :disabled="form.processing"
              >
                <span v-if="form.processing">Reģistrē...</span>
                <span v-else>Reģistrēties</span>
              </button>

              <div class="divider"><span>vai</span></div>

              <a :href="route('google.redirect')" class="google-btn w-100 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="google-icon">
                  <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                  <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                  <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                  <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                Reģistrēties ar Google
              </a>

              <p class="login-link text-center mb-0">
                Jau ir konts? <Link :href="route('login')" class="link-warm">Ienākt</Link>
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.register-page {
  min-height: 100vh;
  background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
  background-attachment: fixed;
}

.register-card {
  padding: 2.5rem 2rem;
  animation: fadeIn 0.6s ease-out;
}

.logo-img {
  height: 60px;
  width: auto;
  margin-bottom: 1rem;
  filter: drop-shadow(0 4px 8px rgba(255, 107, 53, 0.3));
}

.logo-container h1 {
  font-size: 2rem;
  font-weight: 800;
}

.welcome-text {
  color: var(--warm-dark);
  opacity: 0.8;
  font-size: 1.1rem;
  font-weight: 500;
}

.form-label {
  font-weight: 600;
  color: var(--warm-dark);
  margin-bottom: 0.5rem;
}

.login-link {
  color: var(--warm-dark);
  font-size: 0.95rem;
}

.link-warm {
  color: var(--primary-color);
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
}

.link-warm:hover {
  color: var(--secondary-color);
  text-decoration: underline;
}

.is-invalid {
  border-color: var(--accent-color) !important;
}

.invalid-feedback {
  color: var(--accent-color);
  font-size: 0.875rem;
  margin-top: 0.25rem;
  font-weight: 600;
}

.alert-danger {
  background: rgba(230, 57, 70, 0.15);
  border: 1px solid rgba(230, 57, 70, 0.3);
  color: var(--accent-color);
  font-weight: 600;
}

.divider {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin: 0.5rem 0 1rem;
  color: var(--warm-dark);
  opacity: 0.5;
  font-size: 0.9rem;
}

.divider::before,
.divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: currentColor;
}

.google-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  padding: 0.65rem 1rem;
  background: white;
  border: 1px solid #dadce0;
  border-radius: 8px;
  color: #3c4043;
  font-size: 0.95rem;
  font-weight: 500;
  text-decoration: none;
  transition: box-shadow 0.2s ease, background 0.2s ease;
  cursor: pointer;
}

.google-btn:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  background: #f8f9fa;
  color: #3c4043;
}

.google-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 576px) {
  .register-card {
    padding: 2rem 1.5rem;
  }

  .logo-img {
    height: 50px;
  }

  .logo-container h1 {
    font-size: 1.75rem;
  }

  .welcome-text {
    font-size: 1rem;
  }
}
</style>
