<script setup>
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import { nextTick, ref, computed } from 'vue';

const isGoogleUser = computed(() => usePage().props.user?.is_google_user ?? false);

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    if (!isGoogleUser.value) {
        nextTick(() => passwordInput.value?.focus());
    }
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
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
    <button type="button" class="delete-btn" @click="confirmUserDeletion">
        Dzēst Kontu
    </button>

    <Modal :show="confirmingUserDeletion" @close="closeModal">
        <div class="modal-body">
            <div class="modal-icon">!</div>
            <h2 class="modal-title">Dzēst kontu?</h2>
            <p class="modal-text">
                Šī darbība ir neatgriezeniska. Visi jūsu dati tiks neatgriezeniski izdzēsti.
                Ievadiet savu paroli, lai apstiprinātu.
            </p>

            <div v-if="!isGoogleUser" class="form-group">
                <input
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="form-control glass-input"
                    :class="{ 'is-invalid': form.errors.password }"
                    placeholder="Jūsu parole"
                    @keyup.enter="deleteUser"
                />
                <InputError :message="form.errors.password" class="invalid-feedback d-block" />
            </div>
            <div v-else class="google-notice">
                Jūs esat pieteicies ar Google kontu. Parole nav nepieciešama.
            </div>

            <div class="modal-actions">
                <button type="button" class="btn cancel-btn" @click="closeModal">
                    Atcelt
                </button>
                <button
                    type="button"
                    class="btn confirm-delete-btn"
                    :disabled="form.processing"
                    @click="deleteUser"
                >
                    <span v-if="form.processing">Dzēš...</span>
                    <span v-else>Dzēst Kontu</span>
                </button>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
.delete-btn {
    padding: 0.75rem 1.75rem;
    border-radius: 10px;
    border: 2px solid rgba(230, 57, 70, 0.4);
    background: rgba(230, 57, 70, 0.08);
    color: var(--accent-color);
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.delete-btn:hover {
    background: rgba(230, 57, 70, 0.15);
    border-color: var(--accent-color);
    transform: translateY(-1px);
}

/* Modal */
.modal-body {
    padding: 2.5rem 2rem;
    text-align: center;
}

.modal-icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: rgba(230, 57, 70, 0.12);
    border: 2px solid rgba(230, 57, 70, 0.3);
    color: var(--accent-color);
    font-size: 1.75rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
}

.modal-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--accent-color);
    margin: 0 0 0.75rem;
}

.modal-text {
    color: var(--warm-dark);
    opacity: 0.7;
    font-size: 0.9rem;
    margin: 0 0 1.5rem;
    line-height: 1.5;
}

.form-group {
    margin-bottom: 1.5rem;
    text-align: left;
}

.google-notice {
    margin-bottom: 1.5rem;
    padding: 0.75rem 1rem;
    background: rgba(255, 107, 53, 0.08);
    border: 1px solid rgba(255, 107, 53, 0.2);
    border-radius: 10px;
    font-size: 0.9rem;
    color: var(--warm-dark);
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

.modal-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
}

.cancel-btn {
    padding: 0.7rem 1.5rem;
    border-radius: 10px;
    border: 2px solid rgba(0,0,0,0.1);
    background: rgba(0,0,0,0.04);
    color: var(--warm-dark);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.cancel-btn:hover {
    background: rgba(0,0,0,0.08);
}

.confirm-delete-btn {
    padding: 0.7rem 1.5rem;
    border-radius: 10px;
    border: none;
    background: linear-gradient(135deg, #e63946, #c1121f);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px rgba(230, 57, 70, 0.3);
}

.confirm-delete-btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(230, 57, 70, 0.4);
}

.confirm-delete-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
