<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login.post'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <div class="login-page">
    <Head title="Log in" />
    <div class="container">
      <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
          <div class="login-card glass-card">
            <div class="logo-container text-center mb-4">
              <img src="/foodyML_logo.png" alt="FoodyML Logo" class="logo-img" />
              <h1 class="gradient-text mb-0">FOODYML</h1>
              <p class="welcome-text mt-2">Laipni lūgti atpakaļ!</p>
            </div>

            <form @submit.prevent="submit" class="login-form">
              <div v-if="form.errors.email" class="alert alert-danger glass-card p-3 mb-3 text-center">
                {{ form.errors.email }}
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
              </div>

              <div class="mb-4">
                <label for="password" class="form-label">Parole</label>
                <input
                  type="password"
                  id="password"
                  v-model="form.password"
                  placeholder="••••••••"
                  class="form-control glass-input"
                  required
                />
              </div>

              <button
                type="submit"
                class="btn glass-btn w-100 mb-3"
                :disabled="form.processing"
              >
                <span v-if="form.processing">Ienāk...</span>
                <span v-else>Ienākt</span>
              </button>

              <p class="signup-link text-center mb-0">
                Nav konta? <a :href="route('register')" class="link-warm">Reģistrējies</a>
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  min-height: 100vh;
  background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
  background-attachment: fixed;
}

.login-card {
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

.signup-link {
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

.alert-danger {
  background: rgba(230, 57, 70, 0.15);
  border: 1px solid rgba(230, 57, 70, 0.3);
  color: var(--accent-color);
  font-weight: 600;
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
  .login-card {
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
