<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2>
                Atjaunināt Paroli
            </h2>

            <p class="description">
                Pārliecinieties, ka jūsu konts izmanto garu, nejaušu paroli, lai nodrošinātu drošību.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="profile-form">
            <div class="form-group">
                <InputLabel for="current_password" value="Pašreizējā parole" />

                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="form-input"
                    autocomplete="current-password"
                />

                <InputError
                    :message="form.errors.current_password"
                    class="error-message"
                />
            </div>

            <div class="form-group">
                <InputLabel for="password" value="Jauna parole" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="form-input"
                    autocomplete="new-password"
                />

                <InputError :message="form.errors.password" class="error-message" />
            </div>

            <div class="form-group">
                <InputLabel
                    for="password_confirmation"
                    value="Apstipriniet paroli"
                />

                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="form-input"
                    autocomplete="new-password"
                />

                <InputError
                    :message="form.errors.password_confirmation"
                    class="error-message"
                />
            </div>

            <div class="button-group">
                <button
                    type="submit"
                    class="save-btn"
                    :disabled="form.processing"
                >
                    Saglabāt paroli
                </button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="success-message"
                    >
                        Saglabāts.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

<style scoped>
section {
    margin-top: 2rem;
}

h2 {
    font-size: 1.5rem;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.description {
    color: #666;
    margin-bottom: 1.5rem;
    font-size: 0.95rem;
}

.profile-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-input {
    width: 100%;
    padding: 0.8rem;
    border: 2px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
    margin-top: 0.5rem;
    font-family: monospace;
}

.error-message {
    color: #e53935;
    margin-top: 0.5rem;
    font-size: 0.9rem;
}

.button-group {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.save-btn {
    background-color: #000000;
    color: #ffffff;
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    font-family: monospace;
    transition: all 0.2s ease;
}

.save-btn:hover {
    background-color: #333333;
    transform: translateY(-1px);
}

.save-btn:disabled {
    background-color: #666666;
    cursor: not-allowed;
    opacity: 0.9;
    transform: none;
}

.success-message {
    color: #4CAF50;
    font-size: 0.9rem;
    margin: 0;
}
</style>