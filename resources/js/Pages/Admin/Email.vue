<script setup>
import { ref, watch } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { showToast } from '@/Composables/useToast.js';

const props = defineProps({
    recipientCount: Number,
});

const page = usePage();

const form = useForm({
    subject: '',
    body: '',
});

watch(
    () => page.props.flash?.status,
    (status) => {
        if (status === 'email-sent') {
            showToast(`E-pasts nosūtīts ${page.props.flash?.sent_count ?? ''} saņēmējiem!`, 'success');
            form.reset();
        }
    }
);

function submit() {
    form.post(route('admin.email.send'), {
        onError: () => showToast('Lūdzu, pārbaudiet aizpildītos laukus.', 'error'),
    });
}
</script>

<template>
    <MainLayout>
        <Head title="E-pasta sūtīšana" />

        <div class="admin-page">
            <div class="admin-header">
                <h2 class="admin-title gradient-text">E-pasta sūtīšana</h2>
            </div>

            <div class="mail-container">
                <div class="recipient-info glass-card">
                    <div class="recipient-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div>
                        <div class="recipient-count">{{ recipientCount }}</div>
                        <div class="recipient-label">verificēti saņēmēji</div>
                    </div>
                </div>

                <div class="mail-form glass-card">
                    <h3 class="form-title">Jauns ziņojums</h3>

                    <form @submit.prevent="submit">
                        <div class="field-group">
                            <label class="field-label">Temats</label>
                            <input
                                v-model="form.subject"
                                type="text"
                                class="form-control glass-input"
                                :class="{ 'is-invalid': form.errors.subject }"
                                placeholder="E-pasta temats..."
                            />
                            <span v-if="form.errors.subject" class="err">{{ form.errors.subject }}</span>
                        </div>

                        <div class="field-group">
                            <label class="field-label">Ziņojums</label>
                            <textarea
                                v-model="form.body"
                                class="form-control glass-input body-input"
                                :class="{ 'is-invalid': form.errors.body }"
                                placeholder="Raksti ziņojumu šeit..."
                                rows="10"
                            ></textarea>
                            <div class="char-count" :class="{ 'char-count--warn': form.body.length > 4500 }">
                                {{ form.body.length }} / 5000
                            </div>
                            <span v-if="form.errors.body" class="err">{{ form.errors.body }}</span>
                        </div>

                        <div class="form-footer">
                            <div class="send-note">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                E-pasts tiks nosūtīts <strong>{{ recipientCount }}</strong> lietotājiem ar verificētu e-pastu.
                            </div>
                            <button
                                type="submit"
                                class="btn glass-btn send-btn"
                                :disabled="form.processing || !form.subject.trim() || !form.body.trim()"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                                {{ form.processing ? 'Sūta...' : 'Nosūtīt visiem' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
.admin-page {
    min-height: 100vh;
    background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
    padding: 2rem;
    padding-top: 6rem;
}

.admin-header {
    max-width: 720px;
    margin: 0 auto 2rem;
}

.admin-title {
    font-size: 1.8rem;
    font-weight: 800;
    margin: 0;
}

.mail-container {
    max-width: 720px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Recipient info card */
.recipient-info {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.25rem 1.75rem;
}

.recipient-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: rgba(255, 107, 53, 0.12);
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.recipient-count {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--primary-color);
    line-height: 1;
}

.recipient-label {
    font-size: 0.85rem;
    color: var(--warm-dark);
    opacity: 0.6;
    margin-top: 0.2rem;
}

/* Form card */
.mail-form {
    padding: 2rem;
}

.form-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--warm-dark);
    margin: 0 0 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid rgba(255, 107, 53, 0.15);
}

.field-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
    margin-bottom: 1.25rem;
}

.field-label {
    font-weight: 600;
    font-size: 0.88rem;
    color: var(--warm-dark);
}

.body-input {
    resize: vertical;
    min-height: 200px;
    font-family: inherit;
}

.char-count {
    font-size: 0.75rem;
    color: var(--warm-dark);
    opacity: 0.45;
    text-align: right;
}

.char-count--warn {
    color: var(--accent-color);
    opacity: 1;
    font-weight: 600;
}

.is-invalid {
    border-color: var(--accent-color) !important;
}

.err {
    font-size: 0.8rem;
    color: var(--accent-color);
    font-weight: 600;
}

.form-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
    padding-top: 0.5rem;
}

.send-note {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.82rem;
    color: var(--warm-dark);
    opacity: 0.65;
}

.send-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.7rem 1.75rem;
    font-size: 0.95rem;
}

.send-btn:disabled {
    opacity: 0.45;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

@media (max-width: 576px) {
    .admin-page { padding: 1rem; padding-top: 5rem; }
    .form-footer { flex-direction: column; align-items: stretch; }
    .send-btn { justify-content: center; }
}
</style>
