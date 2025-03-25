<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

// Create the form using useForm from Inertia
const form = useForm({
    email: '',
    password: '',
    password_confirmation: '',
});

// Function to handle form submission
const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
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
            <form @submit.prevent="submit" class="register-form">
                <div class="input-group">
                    <label for="email">E-pasts</label>
                    <input 
                        type="email" 
                        id="email" 
                        v-model="form.email" 
                        placeholder="Ievadiet e-pastu" 
                        required 
                    />
                    <!-- Display error message if it exists -->
                    <span v-if="form.errors.email" class="error-message">{{ form.errors.email[0] }}</span>
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
                    <!-- Display error message if it exists -->
                    <span v-if="form.errors.password" class="error-message">{{ form.errors.password[0] }}</span>
                </div>

                <div class="input-group">
                    <label for="password_confirmation">Apstipriniet paroli</label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        v-model="form.password_confirmation" 
                        placeholder="Apstipriniet paroli" 
                        required 
                    />
                    <!-- Display error message if it exists -->
                    <span v-if="form.errors.password_confirmation" class="error-message">{{ form.errors.password_confirmation[0] }}</span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="register-btn">Reģistrēties</button>
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

/* Error Message Styling */
.error-message {
    color: red;
    font-size: 0.9rem;
    margin-top: 5px;
}
</style>
