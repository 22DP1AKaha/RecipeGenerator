<template>
    <MainLayout>
      <div class="profile-page">
        <div class="profile-card">
          <h1>Profila Rediģēšana</h1>
  
          <form @submit.prevent="submit" class="profile-form">
            <!-- Name Field -->
            <div class="form-group">
              <label>Vārds</label>
              <input
                v-model="form.vards"
                type="text"
                class="form-input"
                required
              />
            </div>
  
            <!-- Disabled Email Field -->
            <div class="form-group">
              <label>E-pasts</label>
              <input
                v-model="form.email"
                type="email"
                class="form-input"
                disabled
              />
            </div>

            <div class="password-update-section">
              <UpdatePasswordForm />
            </div>
  
            <!-- Dietary Restrictions -->
            <div class="form-group">
              <h2>Diētas Ierobežojumi</h2>
              <div class="option-grid">
                <label
                  v-for="opt in dietas"
                  :key="opt.id"
                  class="option-card"
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
  
            <!-- Allergies -->
            <div class="form-group">
              <h2>Alerģijas</h2>
              <div class="option-grid">
                <label
                  v-for="opt in alergijas"
                  :key="opt.id"
                  class="option-card"
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
  
            <div class="button-group">
              <CustomButton 
                type="submit"
                text="Saglabāt Izmaiņas"
                class="save-btn"
              />
              <DeleteUserForm />
            </div>
          </form>
        </div>
      </div>
    </MainLayout>
  </template>
  
  <script setup>
  import CustomButton from '@/Components/CustomButton.vue';
import MainLayout from '@/Layouts/MainLayout.vue';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import { useForm } from '@inertiajs/vue3';
  
  const props = defineProps({
    user: Object,
    dietas: Array,
    alergijas: Array,
  });
  
  const form = useForm({
    vards: props.user.vards,
    email: props.user.email,
    dietas_ierobezojumi: props.user.dietas_ierobezojumi,
    alergijas: props.user.alergijas,
  });
  
  function submit() {
    form.patch(route('profile.update'));
  }
  </script>
  
  <style scoped>
  .profile-page {
    font-family: monospace;
    padding: 2rem;
    min-height: 100vh;
  }
  
  .profile-card {
    background: linear-gradient(135deg, #FFF5E1, #FFE4B5);
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
  }
  
  h1 {
    font-size: 2rem;
    text-align: center;
    margin-bottom: 2rem;
  }
  
  .form-group {
    margin-bottom: 2rem;
  }
  
  .form-group h2 {
    font-size: 1.25rem;
    margin-bottom: 1rem;
    color: #2c3e50;
  }
  
  .form-input {
    width: 100%;
    padding: 0.8rem;
    border: 2px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    margin-top: 0.5rem;
  }
  
  .form-input:disabled {
    background-color: #e9ecef;
    cursor: not-allowed;
  }
  
  .option-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
  }
  
  .option-card {
    display: block;
    position: relative;
    background: wheat;
    padding: 1rem;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
  }
  
  .option-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
  }
  
  .option-card input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
  }
  
  .checkmark {
    position: absolute;
    top: 1rem;
    right: 1rem;
    height: 20px;
    width: 20px;
    background-color: #eee;
    border-radius: 4px;
    transition: all 0.3s ease;
  }
  
  .option-card input:checked ~ .checkmark {
    background-color: #4CAF50;
  }
  
  .checkmark:after {
    content: "";
    position: absolute;
    display: none;
    left: 7px;
    top: 3px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
  }
  
  .option-card input:checked ~ .checkmark:after {
    display: block;
  }
  
  .button-group {
    margin-top: 2rem;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    align-items: center;
  }
  
  /* Removed custom save-btn styles */
  @media (max-width: 768px) {
    .profile-card {
      margin: 1rem;
      padding: 1.5rem;
    }
    
    .option-grid {
      grid-template-columns: 1fr;
    }
  }
  </style>