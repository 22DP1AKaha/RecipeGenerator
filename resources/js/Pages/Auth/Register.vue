<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
  vards: '',
  email: '',                // ← was epasts
  password: '',             // ← was parole
  password_confirmation: '',// ← was parole_confirmation
});

const submit = () => {
  form.post(route('register.post'), {
    onSuccess: () => form.reset('password', 'password_confirmation'),
  });
};
</script>

<template>
  <div class="register-page">
    <div class="register-container">
      <!-- Logo -->
      <div class="logo-container">
        <img src="/foodyML_logo.png" alt="FoodyML Logo" />
        <h1>FOODYML</h1>
      </div>

      <Head title="Register" />

      <!-- General error -->
      <div v-if="form.errors.error" class="error-message general-error">
        {{ form.errors.error }}
      </div>

      <form @submit.prevent="submit" class="register-form">
        <!-- Vārds -->
        <div class="input-group">
          <label for="vards">Vārds</label>
          <input
            type="text"
            id="vards"
            v-model="form.vards"
            placeholder="Ievadiet savu vārdu"
            required
          />
          <span v-if="form.errors.vards" class="error-message">
            {{ form.errors.vards }}
          </span>
        </div>

        <!-- E-pasts -->
        <div class="input-group">
          <label for="email">E-pasts</label>
          <input
            type="email"
            id="email"
            v-model="form.email"
            placeholder="Ievadiet e-pastu"
            required
          />
          <span v-if="form.errors.email" class="error-message">
            {{ form.errors.email }}
          </span>
        </div>

        <!-- Parole - Fixed display -->
        <div class="input-group">
          <label for="password">Parole</label>
          <input
            type="password"
            id="password"
            v-model="form.password"
            placeholder="Ievadiet paroli"
            required
          />
          <!-- Changed to simple span -->
          <span v-if="form.errors.password" class="error-message">
            {{ Array.isArray(form.errors.password) ? form.errors.password[0] : form.errors.password }}
          </span>
        </div>

        <!-- Apstipriniet paroli -->
        <div class="input-group">
          <label for="password_confirmation">Apstipriniet paroli</label>
          <input
            type="password"
            id="password_confirmation"
            v-model="form.password_confirmation"
            placeholder="Apstipriniet paroli"
            required
          />
          <span v-if="form.errors.password_confirmation" class="error-message">
            {{ form.errors.password_confirmation }}
          </span>
        </div>

        <div class="form-actions">
          <button 
            type="submit" 
            class="register-btn"
            :disabled="form.processing"
          >
            <span v-if="!form.processing">Reģistrēties</span>
            <span v-else>Reģistrē...</span>
          </button>
        </div>
      </form>

      <p class="login-link">
        Jau ir konts? <Link :href="route('login')">Ienākt</Link>
      </p>
    </div>
  </div>
</template>

<style scoped>
* {
    font-family: monospace;
}

.register-page {
    display: flex;
    justify-content: center;
    align-items: center;
    background-image: url('/background.png');
    background-size: cover;
    background-position: center;
    height: 100vh;
}

.register-container {
    background: linear-gradient(135deg, #FFF5E1, #FFE4B5);
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    text-align: center;
}

.logo-container {
    margin-bottom: 20px;
}

.logo-container img {
    height: 50px;
    width: auto;
    margin-bottom: 10px;
}

h1 {
    font-size: 2rem;
    background: linear-gradient(to right, #b927fc 0%, #2c64fc 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.register-form .input-group {
    margin-bottom: 15px;
    text-align: left;
}

.register-form label {
    display: block;
    font-size: 1rem;
    margin-bottom: 5px;
}

.register-form input {
    width: 100%;
    padding: 10px;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.form-actions {
    margin-top: 15px;
}

.register-btn {
    width: 100%;
    padding: 12px;
    background-color: #2c64fc;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1.2rem;
    cursor: pointer;
}

.register-btn:hover {
    background-color: #1f4bb7;
}

.login-link {
    margin-top: 20px;
    font-size: 1rem;
}

.login-link a {
    color: #2c64fc;
    text-decoration: none;
}

.login-link a:hover {
    text-decoration: underline;
}

.general-error {
  background-color: #ffebee;
  border: 1px solid #ffcdd2;
  border-radius: 4px;
  padding: 10px;
  margin-bottom: 15px;
  text-align: center;
  font-weight: bold;
}

.register-btn:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

/* Ensure error messages don't show bullets */
.error-message {
  color: red;
  display: block;
  list-style: none;
  padding: 0;
  margin: 0;
}

.error-message li {
  display: block;
}
</style>
