<script setup>
import { ref } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { showToast } from '@/Composables/useToast.js';
import axios from 'axios';

const props = defineProps({
    users: Array,
    roles: Array,
});

const page = usePage();
const modalOpen = ref(false);
const confirmDeleteId = ref(null);

const localRoles = ref(
    Object.fromEntries(props.users.map(u => [u.id, u.role_id]))
);

function getRoleClass(userId) {
    const roleId = localRoles.value[userId];
    const role = props.roles.find(r => r.id == roleId);
    return role?.name === 'Administrators' ? 'role-admin' : 'role-user';
}

const form = useForm({
    vards: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '',
});

function openModal() {
    form.reset();
    form.clearErrors();
    modalOpen.value = true;
}

function closeModal() {
    modalOpen.value = false;
    form.reset();
    form.clearErrors();
}

function submit() {
    form.post(route('admin.users.store'), {
        onSuccess: () => {
            closeModal();
            showToast('Lietotājs pievienots!', 'success');
        },
        onError: () => {
            showToast('Lūdzu, pārbaudiet aizpildītos laukus.', 'error');
        },
    });
}

async function updateUserRole(userId, roleId) {
    try {
        await axios.patch(route('admin.users.updateRole', userId), { role_id: roleId });
        localRoles.value[userId] = Number(roleId);
        showToast('Loma atjaunināta.', 'success');
    } catch (e) {
        showToast(e.response?.data?.message ?? 'Kļūda atjauninot lomu.', 'error');
    }
}

async function deleteUser(id) {
    try {
        const fd = new FormData();
        fd.append('_method', 'DELETE');
        await axios.post(route('admin.users.destroy', id), fd);
        showToast('Lietotājs dzēsts.', 'success');
        confirmDeleteId.value = null;
        window.location.reload();
    } catch (e) {
        showToast(e.response?.data?.message ?? 'Kļūda dzēšot lietotāju.', 'error');
    }
}

function isSelf(userId) {
    return userId === page.props.auth.user?.id;
}

function formatDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleDateString('lv-LV');
}
</script>

<template>
    <MainLayout>
        <Head title="Lietotāju pārvaldība" />

        <div class="admin-page">
            <div class="admin-header">
                <h2 class="admin-title gradient-text">Lietotāju pārvaldība</h2>
                <button class="btn glass-btn" @click="openModal">+ Pievienot lietotāju</button>
            </div>

            <div class="users-table glass-card">
                <table>
                    <thead>
                        <tr>
                            <th>Vārds</th>
                            <th>E-pasts</th>
                            <th>Loma</th>
                            <th>Reģistrācija</th>
                            <th>Darbības</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id" :class="{ 'self-row': isSelf(user.id) }">
                            <td>
                                {{ user.vards }}
                                <span v-if="isSelf(user.id)" class="self-badge">Tu</span>
                            </td>
                            <td>{{ user.email }}</td>
                            <td>
                                <select
                                    v-if="!isSelf(user.id)"
                                    :value="localRoles[user.id]"
                                    class="role-select"
                                    :class="getRoleClass(user.id)"
                                    @change="updateUserRole(user.id, $event.target.value)"
                                >
                                    <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                                </select>
                                <span v-else class="role-badge" :class="getRoleClass(user.id)">
                                    {{ user.role?.name ?? '—' }}
                                </span>
                            </td>
                            <td>{{ formatDate(user.registracijas_datums) }}</td>
                            <td>
                                <div class="action-cell">
                                    <template v-if="!isSelf(user.id)">
                                        <template v-if="confirmDeleteId === user.id">
                                            <button class="action-btn confirm-btn" @click="deleteUser(user.id)">Dzēst!</button>
                                            <button class="action-btn cancel-btn" @click="confirmDeleteId = null">Atcelt</button>
                                        </template>
                                        <button v-else class="action-btn delete-btn" @click="confirmDeleteId = user.id">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                <path d="M10 11v6M14 11v6"/>
                                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                            </svg>
                                        </button>
                                    </template>
                                    <span v-else class="self-note">—</span>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!users.length">
                            <td colspan="5" class="empty-msg">Nav neviena lietotāja.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Teleport to="body">
            <div v-if="modalOpen" class="modal-overlay" @click.self="closeModal">
                <div class="modal-box glass-card">
                    <div class="modal-header">
                        <h3>Pievienot lietotāju</h3>
                        <button class="modal-close" @click="closeModal">✕</button>
                    </div>

                    <form @submit.prevent="submit" class="modal-body">
                        <div class="field-group">
                            <label>Vārds</label>
                            <input v-model="form.vards" type="text" class="form-control glass-input" :class="{ 'is-invalid': form.errors.vards }" />
                            <span v-if="form.errors.vards" class="err">{{ form.errors.vards }}</span>
                        </div>

                        <div class="field-group">
                            <label>E-pasts</label>
                            <input v-model="form.email" type="email" class="form-control glass-input" :class="{ 'is-invalid': form.errors.email }" />
                            <span v-if="form.errors.email" class="err">{{ form.errors.email }}</span>
                        </div>

                        <div class="field-group">
                            <label>Parole</label>
                            <input v-model="form.password" type="password" autocomplete="new-password" class="form-control glass-input" :class="{ 'is-invalid': form.errors.password }" />
                            <span v-if="form.errors.password" class="err">{{ form.errors.password }}</span>
                        </div>

                        <div class="field-group">
                            <label>Paroles apstiprinājums</label>
                            <input v-model="form.password_confirmation" type="password" autocomplete="new-password" class="form-control glass-input" />
                        </div>

                        <div class="field-group">
                            <label>Loma</label>
                            <select v-model="form.role_id" class="form-control glass-input" :class="{ 'is-invalid': form.errors.role_id }">
                                <option value="">Izvēlēties lomu</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                            </select>
                            <span v-if="form.errors.role_id" class="err">{{ form.errors.role_id }}</span>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="closeModal">Atcelt</button>
                            <button type="submit" class="btn glass-btn" :disabled="form.processing">
                                {{ form.processing ? 'Saglabā...' : 'Saglabāt' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>
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
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.admin-title {
    font-size: 1.8rem;
    font-weight: 800;
    margin: 0;
}

.users-table {
    padding: 0;
    overflow: hidden;
    overflow-x: auto;
    background: rgba(255, 240, 220, 0.45);
}

.users-table:hover {
    background: rgba(255, 240, 220, 0.45);
    transform: none;
    box-shadow: 0 8px 32px 0 var(--glass-shadow);
}

table {
    width: 100%;
    border-collapse: collapse;
}

thead tr {
    background: rgba(255, 107, 53, 0.08);
}

th {
    padding: 1rem 1.25rem;
    text-align: left;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--primary-color);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
}

td {
    padding: 0.9rem 1.25rem;
    font-size: 0.9rem;
    color: var(--warm-dark);
    border-top: 1px solid rgba(255,255,255,0.1);
    vertical-align: middle;
}

.self-row td { background: rgba(255, 107, 53, 0.04); }

.self-badge {
    font-size: 0.7rem;
    background: rgba(255, 107, 53, 0.15);
    color: var(--primary-color);
    border-radius: 20px;
    padding: 0.1rem 0.5rem;
    font-weight: 700;
    margin-left: 0.4rem;
}

.role-badge {
    font-size: 0.75rem;
    padding: 0.2rem 0.65rem;
    border-radius: 20px;
    font-weight: 600;
}

.role-select {
    font-size: 0.75rem;
    padding: 0.25rem 1.75rem 0.25rem 0.75rem;
    border-radius: 20px;
    font-weight: 600;
    border: 2px solid transparent;
    cursor: pointer;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath fill='none' stroke='currentColor' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round' d='M1 1l4 4 4-4'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.55rem center;
    transition: all 0.2s ease;
}

.role-select:hover {
    border-color: currentColor;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.role-select:focus {
    border-color: currentColor;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.06);
}

.role-admin {
    background: rgba(255, 107, 53, 0.15);
    color: var(--primary-color);
}

.role-user {
    background: rgba(100, 100, 200, 0.12);
    color: #5555aa;
}

.action-cell {
    display: flex;
    gap: 0.4rem;
    align-items: center;
}

.action-btn {
    padding: 0.35rem 0.7rem;
    border: none;
    border-radius: 7px;
    cursor: pointer;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.delete-btn {
    background: rgba(230, 57, 70, 0.12);
    color: #c0392b;
}

.delete-btn:hover { background: rgba(230, 57, 70, 0.25); }

.confirm-btn {
    background: rgba(230, 57, 70, 0.8);
    color: white;
}

.confirm-btn:hover { background: rgba(230, 57, 70, 1); }

.cancel-btn {
    background: rgba(150, 150, 150, 0.15);
    color: var(--warm-dark);
}

.self-note { opacity: 0.4; font-size: 0.85rem; }

.empty-msg {
    text-align: center;
    opacity: 0.5;
    padding: 2rem;
}

.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    z-index: 2000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal-box {
    width: 100%;
    max-width: 440px;
    animation: scaleIn 0.25s ease;
    background: rgba(255, 240, 220, 0.45);
}

.modal-box:hover {
    background: rgba(255, 240, 220, 0.45);
    transform: none;
    box-shadow: 0 8px 32px 0 var(--glass-shadow);
}

@keyframes scaleIn {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.15);
}

.modal-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--warm-dark);
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: var(--warm-dark);
    opacity: 0.6;
    transition: opacity 0.2s;
}

.modal-close:hover { opacity: 1; }

.modal-body {
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.modal-footer {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    padding-top: 0.5rem;
}

.field-group {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.field-group label {
    font-weight: 600;
    font-size: 0.85rem;
    color: var(--warm-dark);
}

.err {
    font-size: 0.8rem;
    color: #e63946;
}

.btn-secondary {
    background: rgba(150,150,150,0.15);
    border: 1px solid rgba(150,150,150,0.3);
    color: var(--warm-dark);
    border-radius: 12px;
    padding: 0.5rem 1.25rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-secondary:hover { background: rgba(150,150,150,0.25); }

@media (max-width: 576px) {
    .admin-page { padding: 1rem; padding-top: 5rem; }
    th, td { padding: 0.7rem 0.75rem; font-size: 0.82rem; }
}
</style>
