<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
  email: '',     // stays as email
  password: '',  // stays as password
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
    <div class="login-container">
      <!-- Logo -->
      <div class="logo-container">
        <img src="/foodyML_logo.png" alt="FoodyML Logo" />
        <h1>FOODYML</h1>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="submit" class="login-form">
        <!-- Combined error message -->
        <div v-if="form.errors.email" class="input-error" style="margin-bottom: 15px; text-align: center;">
          {{ form.errors.email }}
        </div>

        <div class="input-group">
          <label for="email">E-pasts</label>
          <input
            type="email"
            id="email"
            v-model="form.email"
            placeholder="Ievadiet e-pastu"
            required
            :class="{ 'error-border': form.errors.email }"
          />
        </div>

        <div class="input-group">
          <label for="password">Parole</label>
          <input
            type="password"
            id="password"
            v-model="form.password"
            placeholder="Ievadiet paroli"
            required
          />
        </div>

        <div class="form-actions">
          <button
            type="submit"
            class="login-btn"
            :class="{ 'opacity-50': form.processing }"
            :disabled="form.processing"
          >
            Ienākt
          </button>
        </div>
      </form>

      <p class="signup-link">
        Nav konta? <a :href="route('register')">Reģistrējies</a>
      </p>
    </div>
  </div>
</template>

<style scoped>
* {
    font-family: monospace;
}

.login-page {
    display: flex;
    justify-content: center;
    align-items: center;
    background-image: url('/background.png'); 
    background-size: cover;
    background-position: center;
    height: 100vh;
}

.login-container {
    background: linear-gradient(135deg, #FFF5E1, #FFE4B5);
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    text-align: center;
}

.logo-container img {
    height: 50px;
    width: auto;
    margin-bottom: 10px;
}

.logo-container h1 {
    font-size: 2rem;
    background: linear-gradient(to right, #b927fc 0%, #2c64fc 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.input-group {
    margin-bottom: 15px;
}

.input-group label {
    display: block;
    text-align: left;
    font-size: 1rem;
    margin-bottom: 5px;
}

.input-group input {
    width: 100%;
    padding: 10px;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.input-error {
    color: red;
    font-size: 0.9rem;
    margin-top: 5px;
}

.form-actions {
    margin-top: 15px;
}

.remember-me {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    margin-bottom: 10px;
}

.remember-me input {
    margin-right: 5px;
}

.login-btn {
    width: 100%;
    padding: 12px;
    background-color: #2c64fc;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1.2rem;
    cursor: pointer;
}

.login-btn:hover {
    background-color: #1f4bb7;
}

.signup-link {
    margin-top: 20px;
    font-size: 1rem;
}

.signup-link a {
    color: #2c64fc;
    text-decoration: none;
}

.signup-link a:hover {
    text-decoration: underline;
}

.error-border {
  border: 2px solid red !important;
}

.input-error {
  color: red;
  font-size: 1rem;
  font-weight: bold;
  padding: 8px;
  background-color: #ffeeee;
  border-radius: 4px;
}
</style>