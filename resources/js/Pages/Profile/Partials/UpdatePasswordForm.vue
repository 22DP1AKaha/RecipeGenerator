<script setup>
import InputError from '@/Components/InputError.vue';
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
    <h2 class="section-title">Atjaunināt Paroli</h2>
    <p class="section-hint">Izmantojiet garu, nejaušu paroli, lai nodrošinātu konta drošību.</p>

    <form @submit.prevent="updatePassword" class="password-form">
        <div class="form-group">
            <label class="form-label" for="current_password">Pašreizējā parole</label>
            <input
                id="current_password"
                ref="currentPasswordInput"
                v-model="form.current_password"
                type="password"
                class="form-control glass-input"
                :class="{ 'is-invalid': form.errors.current_password }"
                autocomplete="current-password"
                placeholder="••••••••"
            />
            <InputError :message="form.errors.current_password" class="invalid-feedback d-block" />
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Jauna parole</label>
            <input
                id="password"
                ref="passwordInput"
                v-model="form.password"
                type="password"
                class="form-control glass-input"
                :class="{ 'is-invalid': form.errors.password }"
                autocomplete="new-password"
                placeholder="••••••••"
            />
            <InputError :message="form.errors.password" class="invalid-feedback d-block" />
        </div>

        <div class="form-group">
            <label class="form-label" for="password_confirmation">Apstipriniet paroli</label>
            <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                class="form-control glass-input"
                :class="{ 'is-invalid': form.errors.password_confirmation }"
                autocomplete="new-password"
                placeholder="••••••••"
            />
            <InputError :message="form.errors.password_confirmation" class="invalid-feedback d-block" />
        </div>

        <div class="button-row">
            <button
                type="submit"
                class="btn glass-btn"
                :disabled="form.processing"
            >
                <span v-if="form.processing">Saglabā...</span>
                <span v-else>Saglabāt paroli</span>
            </button>

            <Transition name="fade">
                <p v-if="form.recentlySuccessful" class="success-msg">
                    ✓ Parole saglabāta!
                </p>
            </Transition>
        </div>
    </form>
</template>

<style scoped>
.section-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--warm-dark);
    margin: 0 0 0.4rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid rgba(255, 107, 53, 0.15);
}

.section-hint {
    color: var(--warm-dark);
    opacity: 0.6;
    font-size: 0.875rem;
    margin: 0 0 1.5rem;
}

.password-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 600;
    color: var(--warm-dark);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
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

.button-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 0.25rem;
}

.success-msg {
    color: #4caf50;
    font-size: 0.9rem;
    font-weight: 600;
    margin: 0;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
