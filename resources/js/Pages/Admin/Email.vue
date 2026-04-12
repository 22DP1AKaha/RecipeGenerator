<script setup>
import { ref, computed, watch } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { showToast } from '@/Composables/useToast.js';

const props = defineProps({
    recipients: Array,
});

const page = usePage();

const recipientSearch = ref('');

const filteredRecipients = computed(() => {
    const q = recipientSearch.value.trim().toLowerCase();
    if (!q) return props.recipients;
    return props.recipients.filter(
        u => u.vards.toLowerCase().includes(q) || u.email.toLowerCase().includes(q)
    );
});

const form = useForm({
    subject:       '',
    body:          '',
    recipient_ids: props.recipients.map(u => u.id),
});

const allSelected = computed(
    () => form.recipient_ids.length === props.recipients.length
);

const selectedCount = computed(() => form.recipient_ids.length);

function toggleUser(id) {
    const idx = form.recipient_ids.indexOf(id);
    if (idx === -1) form.recipient_ids.push(id);
    else form.recipient_ids.splice(idx, 1);
}

function selectAll() {
    form.recipient_ids = props.recipients.map(u => u.id);
}

function selectNone() {
    form.recipient_ids = [];
}

watch(
    () => page.props.flash?.status,
    (status) => {
        if (status === 'email-sent') {
            showToast(`E-pasts nosūtīts ${page.props.flash?.sent_count ?? ''} saņēmējiem!`, 'success');
            form.reset();
            form.recipient_ids = props.recipients.map(u => u.id);
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

                <!-- Recipients panel -->
                <div class="recipients-card glass-card">
                    <div class="recipients-header">
                        <div class="recipients-title-row">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span class="recipients-title">Saņēmēji</span>
                            <span class="selected-badge">{{ selectedCount }} / {{ recipients.length }}</span>
                        </div>
                        <div class="recipients-actions">
                            <button type="button" class="action-link" @click="selectAll" :disabled="allSelected">Visi</button>
                            <span class="sep">·</span>
                            <button type="button" class="action-link" @click="selectNone" :disabled="selectedCount === 0">Neviens</button>
                        </div>
                    </div>

                    <div class="search-box">
                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input
                            v-model="recipientSearch"
                            type="text"
                            class="glass-input search-input"
                            placeholder="Meklēt pēc vārda vai e-pasta..."
                        />
                    </div>

                    <div class="user-list">
                        <label
                            v-for="user in filteredRecipients"
                            :key="user.id"
                            class="user-row"
                            :class="{ 'user-row--checked': form.recipient_ids.includes(user.id) }"
                        >
                            <input
                                type="checkbox"
                                class="user-checkbox"
                                :checked="form.recipient_ids.includes(user.id)"
                                @change="toggleUser(user.id)"
                            />
                            <div class="user-avatar">{{ user.vards.charAt(0).toUpperCase() }}</div>
                            <div class="user-info">
                                <span class="user-name">{{ user.vards }}</span>
                                <span class="user-email">{{ user.email }}</span>
                            </div>
                        </label>

                        <div v-if="filteredRecipients.length === 0" class="no-results">
                            Nav atrasts neviens lietotājs
                        </div>
                    </div>

                    <span v-if="form.errors.recipient_ids" class="err" style="margin-top:0.5rem;display:block">
                        {{ form.errors.recipient_ids }}
                    </span>
                </div>

                <!-- Compose form -->
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                Tiks nosūtīts <strong>{{ selectedCount }}</strong> saņēmējiem.
                            </div>
                            <button
                                type="submit"
                                class="btn glass-btn send-btn"
                                :disabled="form.processing || !form.subject.trim() || !form.body.trim() || selectedCount === 0"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13"/>
                                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                                </svg>
                                {{ form.processing ? 'Sūta...' : 'Nosūtīt' }}
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

/* Recipients card */
.recipients-card {
    padding: 1.25rem 1.5rem;
}

.recipients-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.recipients-title-row {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    color: var(--warm-dark);
}

.recipients-title {
    font-weight: 700;
    font-size: 0.95rem;
}

.selected-badge {
    font-size: 0.78rem;
    font-weight: 700;
    background: rgba(255, 107, 53, 0.12);
    color: var(--primary-color);
    padding: 0.15rem 0.55rem;
    border-radius: 20px;
    border: 1px solid rgba(255, 107, 53, 0.2);
}

.recipients-actions {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.action-link {
    background: none;
    border: none;
    color: var(--primary-color);
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    padding: 0.2rem 0.4rem;
    border-radius: 6px;
    transition: background 0.2s;
}

.action-link:hover:not(:disabled) {
    background: rgba(255, 107, 53, 0.1);
}

.action-link:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.sep {
    color: #ccc;
    font-size: 0.85rem;
}

/* Search */
.search-box {
    position: relative;
    margin-bottom: 0.75rem;
}

.search-icon {
    position: absolute;
    left: 11px;
    top: 50%;
    transform: translateY(-50%);
    color: #aaa;
    pointer-events: none;
}

.search-input {
    padding-left: 32px !important;
    font-size: 0.88rem;
}

/* User list */
.user-list {
    max-height: 260px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding-right: 2px;
}

.user-list::-webkit-scrollbar { width: 4px; }
.user-list::-webkit-scrollbar-thumb { background: rgba(255,107,53,0.25); border-radius: 2px; }

.user-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0.6rem;
    border-radius: 10px;
    cursor: pointer;
    transition: background 0.15s;
    border: 1.5px solid transparent;
}

.user-row:hover {
    background: rgba(255, 107, 53, 0.05);
}

.user-row--checked {
    background: rgba(255, 107, 53, 0.06);
    border-color: rgba(255, 107, 53, 0.15);
}

.user-checkbox {
    width: 16px;
    height: 16px;
    accent-color: var(--primary-color);
    flex-shrink: 0;
    cursor: pointer;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    font-size: 0.85rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.user-info {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.user-name {
    font-size: 0.88rem;
    font-weight: 600;
    color: var(--warm-dark);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-email {
    font-size: 0.78rem;
    color: var(--warm-dark);
    opacity: 0.55;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.no-results {
    text-align: center;
    padding: 1.5rem;
    color: #aaa;
    font-size: 0.88rem;
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
