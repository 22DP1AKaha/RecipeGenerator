<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
    form.clearErrors();
};
</script>

<template>
    <section class="delete-section">
        <header>
            <h2 class="section-title">
                Dzēst Kontu
            </h2>

            <p class="warning-text">
                Pēc konta dzēšanas visi dati tiks neatgriezeniski izdzēsti.
                Lūdzu, lejupielādējiet visus saglabājamos datus pirms turpināšanas.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion">
            Dzēst Kontu
        </DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="modal-content">
                <h2 class="modal-title">
                    Vai tiešām vēlaties dzēst savu kontu?
                </h2>

                <p class="modal-warning">
                    Lūdzu, ievadiet savu paroli, lai apstiprinātu konta dzēšanu.
                </p>

                <div class="password-input">
                    <InputLabel
                        for="password"
                        value="Parole"
                        class="sr-only"
                    />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="password-field"
                        placeholder="Parole"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="error-message" />
                </div>

                <div class="button-container">
                    <SecondaryButton @click="closeModal">
                        Atcelt
                    </SecondaryButton>

                    <DangerButton
                        class="confirm-button"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Dzēst Kontu
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>

<style scoped>
.delete-section {
    margin-top: 3rem;
    padding: 2rem;
    background: linear-gradient(135deg, #FFF5E1, #FFE4B5);
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.section-title {
    font-size: 1.5rem;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.warning-text {
    color: #7f8c8d;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
}

.modal-content {
    padding: 2rem;
    background: white;
    border-radius: 8px;
}

.modal-title {
    font-size: 1.25rem;
    color: #e74c3c;
    margin-bottom: 1rem;
}

.modal-warning {
    color: #7f8c8d;
    margin-bottom: 1.5rem;
}

.password-input {
    margin: 1.5rem 0;
}

.password-field {
    width: 100%;
    padding: 0.8rem;
    border: 2px solid #e74c3c;
    border-radius: 6px;
    font-size: 1rem;
}

.button-container {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
}

.confirm-button {
    background: #e74c3c;
    padding: 0.8rem 1.5rem;
    transition: all 0.3s ease;
}

.confirm-button:hover {
    background: #c0392b;
    transform: translateY(-1px);
}

.error-message {
    color: #e74c3c;
    margin-top: 0.5rem;
    font-size: 0.9rem;
}
</style>