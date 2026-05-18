<script setup>
import { showToast } from '@/Composables/useToast.js';
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    recipes: Object,
    difficultyLevels: Array,
    mealTimes: Array,
    nutritionTypes: Array,
    dietTypes: Array,
    proteinSources: Array,
    filters: Object,
});

const formIngredients = ref([]);
const formUnits = ref([]);
let formDataLoaded = false;

async function loadFormData() {
    if (formDataLoaded) return;
    const [ingRes, unitsRes] = await Promise.all([
        axios.get('/api/admin/ingredients'),
        axios.get('/api/units'),
    ]);
    formIngredients.value = ingRes.data;
    formUnits.value = unitsRes.data;
    formDataLoaded = true;
}

// Filter state (initialised from current URL filters)
const search = ref(props.filters?.search ?? '');
const selectedMealTime = ref(props.filters?.meal_time_id ?? '');
const selectedNutritionType = ref(props.filters?.nutrition_type_id ?? '');
const selectedProteinSource = ref(props.filters?.protein_source_id ?? '');
const sortBy = ref(props.filters?.sort_by ?? 'name');
const sortDirection = ref(props.filters?.sort_direction ?? 'asc');

const currentPage = computed(() => props.recipes.current_page ?? 1);
const lastPage = computed(() => props.recipes.last_page ?? 1);
const total = computed(() => props.recipes.total ?? 0);

const paginationPages = computed(() => {
    const pages = [];
    const maxVisible = 7;
    const current = currentPage.value;
    const last = lastPage.value;

    if (last <= maxVisible) {
        for (let i = 1; i <= last; i++) pages.push(i);
    } else {
        pages.push(1);
        if (current <= 3) {
            for (let i = 2; i <= Math.min(5, last - 1); i++) pages.push(i);
            if (last > 5) pages.push('...');
        } else if (current >= last - 2) {
            pages.push('...');
            for (let i = Math.max(2, last - 4); i < last; i++) pages.push(i);
        } else {
            pages.push('...');
            for (let i = current - 1; i <= current + 1; i++) pages.push(i);
            pages.push('...');
        }
        pages.push(last);
    }
    return pages;
});

function applyFilters(page = 1) {
    const params = {};
    if (search.value) params.search = search.value;
    if (selectedMealTime.value) params.meal_time_id = selectedMealTime.value;
    if (selectedNutritionType.value) params.nutrition_type_id = selectedNutritionType.value;
    if (selectedProteinSource.value) params.protein_source_id = selectedProteinSource.value;
    if (sortBy.value && sortBy.value !== 'name') params.sort_by = sortBy.value;
    if (sortDirection.value && sortDirection.value !== 'asc') params.sort_direction = sortDirection.value;
    if (page > 1) params.page = page;

    router.get(route('admin.recipes.index'), params, { preserveState: true, replace: true });
}

function goToPage(page) {
    if (page >= 1 && page <= lastPage.value) {
        applyFilters(page);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function clearFilters() {
    search.value = '';
    selectedMealTime.value = '';
    selectedNutritionType.value = '';
    selectedProteinSource.value = '';
    sortBy.value = 'name';
    sortDirection.value = 'asc';
    router.get(route('admin.recipes.index'), {}, { preserveState: true, replace: true });
}

// Debounce for search
let searchTimeout = null;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => applyFilters(1), 400);
});

watch([selectedMealTime, selectedNutritionType, selectedProteinSource, sortBy, sortDirection], () => {
    applyFilters(1);
});

const drawerOpen = ref(false);
const editingRecipe = ref(null);
const confirmDeleteId = ref(null);
const saving = ref(false);

const existingImages = ref([]);
const deletedImageIds = ref([]);
const newImageFiles = ref([]);
const newImagePreviews = ref([]);

const emptyForm = () => ({
    name: '',
    description: '',
    cooking_time: '',
    difficulty_level_id: '',
    meal_time_id: '',
    nutrition_type_id: '',
    diet_type_id: '',
    protein_source_id: null,
    is_public: true,
    ingredients: [],
    instructions: [],
});

const form = ref(emptyForm());
const errors = ref({});

function openCreate() {
    editingRecipe.value = null;
    form.value = emptyForm();
    errors.value = {};
    existingImages.value = [];
    deletedImageIds.value = [];
    newImageFiles.value = [];
    newImagePreviews.value = [];
    loadFormData();
    drawerOpen.value = true;
}

function openEdit(recipe) {
    editingRecipe.value = recipe;
    form.value = {
        name: recipe.name,
        description: recipe.description,
        cooking_time: recipe.cooking_time,
        difficulty_level_id: recipe.difficulty_level_id,
        meal_time_id: recipe.meal_time_id,
        nutrition_type_id: recipe.nutrition_type_id,
        diet_type_id: recipe.diet_type_id,
        protein_source_id: recipe.protein_source_id ?? null,
        is_public: recipe.is_public,
        ingredients: recipe.ingredients.map(ing => ({
            ingredient_id: ing.id,
            quantity: ing.pivot?.quantity ?? '',
            unit_id: ing.pivot?.unit_id ?? '',
        })),
        instructions: recipe.instructions.map(s => ({ description: s.description })),
    };
    existingImages.value = (recipe.images ?? []).map(img => ({ id: img.id, url: img.data_url ?? img.url }));
    deletedImageIds.value = [];
    newImageFiles.value = [];
    newImagePreviews.value = [];
    errors.value = {};
    loadFormData();
    drawerOpen.value = true;
}

function closeDrawer() {
    drawerOpen.value = false;
    editingRecipe.value = null;
    errors.value = {};
    newImagePreviews.value = [];
    newImageFiles.value = [];
}

function addIngredient() {
    form.value.ingredients.push({ ingredient_id: '', quantity: '', unit_id: '' });
}

function removeIngredient(i) {
    form.value.ingredients.splice(i, 1);
}

function addStep() {
    form.value.instructions.push({ description: '' });
}

function removeStep(i) {
    form.value.instructions.splice(i, 1);
}

function onImageFilesSelected(e) {
    const files = Array.from(e.target.files);
    files.forEach(file => {
        newImageFiles.value.push(file);
        const reader = new FileReader();
        reader.onload = ev => newImagePreviews.value.push(ev.target.result);
        reader.readAsDataURL(file);
    });
    e.target.value = '';
}

function removeNewImage(i) {
    newImageFiles.value.splice(i, 1);
    newImagePreviews.value.splice(i, 1);
}

function markImageForDeletion(imgId) {
    deletedImageIds.value.push(imgId);
    existingImages.value = existingImages.value.filter(img => img.id !== imgId);
}

function buildFormData() {
    const fd = new FormData();
    const f = form.value;

    fd.append('name', f.name);
    fd.append('description', f.description);
    fd.append('cooking_time', f.cooking_time);
    fd.append('difficulty_level_id', f.difficulty_level_id);
    fd.append('meal_time_id', f.meal_time_id);
    fd.append('nutrition_type_id', f.nutrition_type_id);
    fd.append('diet_type_id', f.diet_type_id);
    if (f.protein_source_id) fd.append('protein_source_id', f.protein_source_id);
    fd.append('is_public', f.is_public ? '1' : '0');

    f.ingredients.forEach((ing, i) => {
        fd.append(`ingredients[${i}][ingredient_id]`, ing.ingredient_id);
        fd.append(`ingredients[${i}][quantity]`, ing.quantity);
        fd.append(`ingredients[${i}][unit_id]`, ing.unit_id);
    });

    f.instructions.forEach((step, i) => {
        fd.append(`instructions[${i}][description]`, step.description);
    });

    newImageFiles.value.forEach(file => fd.append('images[]', file));

    deletedImageIds.value.forEach(id => fd.append('deleted_image_ids[]', id));

    return fd;
}

async function save() {
    saving.value = true;
    errors.value = {};
    try {
        const fd = buildFormData();
        if (editingRecipe.value) {
            fd.append('_method', 'PUT');
            await axios.post(route('admin.recipes.update', editingRecipe.value.id), fd, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            showToast('Recepte atjaunināta!', 'success');
        } else {
            await axios.post(route('admin.recipes.store'), fd, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });
            showToast('Recepte pievienota!', 'success');
        }
        closeDrawer();
        router.reload({ only: ['recipes'] });
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors ?? {};
            showToast('Lūdzu, pārbaudiet aizpildītos laukus.', 'error');
        } else {
            showToast('Kļūda saglabājot recepti.', 'error');
        }
    } finally {
        saving.value = false;
    }
}

async function deleteRecipe(id) {
    try {
        const fd = new FormData();
        fd.append('_method', 'DELETE');
        await axios.post(route('admin.recipes.destroy', id), fd);
        showToast('Recepte dzēsta.', 'success');
        confirmDeleteId.value = null;
        router.reload({ only: ['recipes'] });
    } catch {
        showToast('Kļūda dzēšot recepti.', 'error');
    }
}

function labelFor(list, id, field = 'name') {
    return list?.find(i => i.id === id)?.[field] ?? '—';
}
</script>

<template>
    <MainLayout>
        <Head title="Recepšu pārvaldība" />

        <div class="admin-page">
            <div class="admin-header">
                <h2 class="admin-title gradient-text">Recepšu pārvaldība</h2>
                <button class="btn glass-btn" @click="openCreate">+ Pievienot recepti</button>
            </div>

            <div class="admin-filters">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Meklēt pēc nosaukuma..."
                    class="filter-input"
                />

                <select v-model="selectedMealTime" class="filter-select">
                    <option value="">Visas ēdienreizes</option>
                    <option v-for="m in mealTimes" :key="m.id" :value="m.id">{{ m.name }}</option>
                </select>

                <select v-model="selectedNutritionType" class="filter-select">
                    <option value="">Visi uzturvielu tipi</option>
                    <option v-for="n in nutritionTypes" :key="n.id" :value="n.id">{{ n.name }}</option>
                </select>

                <select v-model="selectedProteinSource" class="filter-select">
                    <option value="">Visi olbaltumvielu avoti</option>
                    <option v-for="p in proteinSources" :key="p.id" :value="p.id">{{ p.name }}</option>
                </select>

                <select v-model="sortBy" class="filter-select">
                    <option value="name">Kārtot: nosaukums</option>
                    <option value="cooking_time">Kārtot: laiks</option>
                    <option value="created_at">Kārtot: pievienošanas datums</option>
                </select>

                <select v-model="sortDirection" class="filter-select filter-select--narrow">
                    <option value="asc">Augoši</option>
                    <option value="desc">Dilstoši</option>
                </select>

                <button class="btn-clear" @click="clearFilters">Notīrīt</button>
            </div>

            <div class="recipe-grid">
                <div v-for="recipe in recipes.data" :key="recipe.id" class="recipe-card glass-card">
                    <div class="recipe-thumb" v-if="recipe.images?.length">
                        <img :src="recipe.images[0].data_url ?? recipe.images[0].url" :alt="recipe.name" loading="lazy" />
                        <span v-if="recipe.images.length > 1" class="img-count">+{{ recipe.images.length - 1 }}</span>
                    </div>
                    <div class="recipe-card-body">
                        <h4 class="recipe-name">{{ recipe.name }}</h4>
                        <div class="recipe-meta">
                            <span class="meta-badge">{{ labelFor(mealTimes, recipe.meal_time_id) }}</span>
                            <span class="meta-badge">{{ labelFor(difficultyLevels, recipe.difficulty_level_id) }}</span>
                            <span class="meta-badge">{{ recipe.cooking_time }} min</span>
                        </div>
                    </div>
                    <div class="recipe-card-actions">
                        <button class="action-btn edit-btn" @click="openEdit(recipe)" title="Rediģēt">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <template v-if="confirmDeleteId === recipe.id">
                            <button class="action-btn confirm-btn" @click="deleteRecipe(recipe.id)">Dzēst!</button>
                            <button class="action-btn cancel-btn" @click="confirmDeleteId = null">Atcelt</button>
                        </template>
                        <button v-else class="action-btn delete-btn" @click="confirmDeleteId = recipe.id" title="Dzēst">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                <path d="M10 11v6M14 11v6"/>
                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <p v-if="!recipes.data?.length" class="empty-msg">Nav nevienas receptes.</p>
            </div>

            <div v-if="lastPage > 1" class="pagination-container">
                <nav aria-label="Recipe pagination">
                    <ul class="pagination glass-pagination">
                        <li class="page-item" :class="{ disabled: currentPage === 1 }">
                            <button
                                class="page-link glass-page-link"
                                @click="goToPage(currentPage - 1)"
                                :disabled="currentPage === 1"
                            >‹ Iepriekšējā</button>
                        </li>

                        <li
                            v-for="(page, index) in paginationPages"
                            :key="index"
                            class="page-item"
                            :class="{ active: page === currentPage, disabled: page === '...' }"
                        >
                            <button
                                v-if="page !== '...'"
                                class="page-link glass-page-link"
                                @click="goToPage(page)"
                                :aria-current="page === currentPage ? 'page' : undefined"
                            >{{ page }}</button>
                            <span v-else class="page-link glass-page-link disabled-ellipsis">...</span>
                        </li>

                        <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                            <button
                                class="page-link glass-page-link"
                                @click="goToPage(currentPage + 1)"
                                :disabled="currentPage === lastPage"
                            >Nākamā ›</button>
                        </li>
                    </ul>

                    <div class="pagination-info">
                        Lapa {{ currentPage }} no {{ lastPage }} (Kopā: {{ total }} receptes)
                    </div>
                </nav>
            </div>
        </div>

        <Teleport to="body">
            <div v-if="drawerOpen" class="drawer-overlay" @click.self="closeDrawer">
                <div class="drawer glass-card">
                    <div class="drawer-header">
                        <h3>{{ editingRecipe ? 'Rediģēt recepti' : 'Pievienot recepti' }}</h3>
                        <button class="drawer-close" @click="closeDrawer">✕</button>
                    </div>

                    <div class="drawer-body">
                        <div class="field-group">
                            <label>Nosaukums</label>
                            <input v-model="form.name" type="text" class="form-control glass-input" :class="{ 'is-invalid': errors.name }" />
                            <span v-if="errors.name" class="err">{{ errors.name[0] }}</span>
                        </div>

                        <div class="field-group">
                            <label>Apraksts</label>
                            <textarea v-model="form.description" rows="3" class="form-control glass-input" :class="{ 'is-invalid': errors.description }"></textarea>
                            <span v-if="errors.description" class="err">{{ errors.description[0] }}</span>
                        </div>

                        <div class="field-row">
                            <div class="field-group">
                                <label>Laiks (min)</label>
                                <input v-model.number="form.cooking_time" type="number" min="1" class="form-control glass-input" :class="{ 'is-invalid': errors.cooking_time }" />
                                <span v-if="errors.cooking_time" class="err">{{ errors.cooking_time[0] }}</span>
                            </div>
                            <div class="field-group">
                                <label>Grūtības pak.</label>
                                <select v-model="form.difficulty_level_id" class="form-control glass-input" :class="{ 'is-invalid': errors.difficulty_level_id }">
                                    <option value="">Izvēlēties</option>
                                    <option v-for="d in difficultyLevels" :key="d.id" :value="d.id">{{ d.name }}</option>
                                </select>
                                <span v-if="errors.difficulty_level_id" class="err">{{ errors.difficulty_level_id[0] }}</span>
                            </div>
                        </div>

                        <div class="field-row">
                            <div class="field-group">
                                <label>Ēdienreize</label>
                                <select v-model="form.meal_time_id" class="form-control glass-input" :class="{ 'is-invalid': errors.meal_time_id }">
                                    <option value="">Izvēlēties</option>
                                    <option v-for="m in mealTimes" :key="m.id" :value="m.id">{{ m.name }}</option>
                                </select>
                                <span v-if="errors.meal_time_id" class="err">{{ errors.meal_time_id[0] }}</span>
                            </div>
                            <div class="field-group">
                                <label>Uzturvielu tips</label>
                                <select v-model="form.nutrition_type_id" class="form-control glass-input" :class="{ 'is-invalid': errors.nutrition_type_id }">
                                    <option value="">Izvēlēties</option>
                                    <option v-for="n in nutritionTypes" :key="n.id" :value="n.id">{{ n.name }}</option>
                                </select>
                                <span v-if="errors.nutrition_type_id" class="err">{{ errors.nutrition_type_id[0] }}</span>
                            </div>
                        </div>

                        <div class="field-row">
                            <div class="field-group">
                                <label>Diētas tips</label>
                                <select v-model="form.diet_type_id" class="form-control glass-input" :class="{ 'is-invalid': errors.diet_type_id }">
                                    <option value="">Izvēlēties</option>
                                    <option v-for="d in dietTypes" :key="d.id" :value="d.id">{{ d.name }}</option>
                                </select>
                                <span v-if="errors.diet_type_id" class="err">{{ errors.diet_type_id[0] }}</span>
                            </div>
                            <div class="field-group">
                                <label>Olbaltumvielu avots</label>
                                <select v-model="form.protein_source_id" class="form-control glass-input">
                                    <option :value="null">Nav</option>
                                    <option v-for="p in proteinSources" :key="p.id" :value="p.id">{{ p.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="field-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" v-model="form.is_public" />
                                Publiska recepte
                            </label>
                        </div>

                        <div class="section-divider"><span>Attēli</span></div>

                        <div v-if="existingImages.length" class="image-preview-grid">
                            <div v-for="img in existingImages" :key="img.id" class="preview-item">
                                <img :src="img.url" :alt="'Attēls ' + img.id" />
                                <button class="preview-remove" @click="markImageForDeletion(img.id)" title="Noņemt">✕</button>
                            </div>
                        </div>

                        <div v-if="newImagePreviews.length" class="image-preview-grid">
                            <div v-for="(src, i) in newImagePreviews" :key="'new-' + i" class="preview-item preview-item--new">
                                <img :src="src" alt="Jauns attēls" />
                                <button class="preview-remove" @click="removeNewImage(i)" title="Noņemt">✕</button>
                                <span class="preview-new-badge">Jauns</span>
                            </div>
                        </div>

                        <label class="upload-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="16 16 12 12 8 16"/>
                                <line x1="12" y1="12" x2="12" y2="21"/>
                                <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                            </svg>
                            Pievienot attēlus
                            <input type="file" accept="image/*" multiple @change="onImageFilesSelected" class="hidden-input" />
                        </label>
                        <p class="upload-hint">Maks. 5 attēli, katrs līdz 5 MB (JPG, PNG, WebP)</p>

                        <div class="section-divider"><span>Sastāvdaļas</span></div>

                        <div v-for="(ing, i) in form.ingredients" :key="i" class="ingredient-row">
                            <select v-model="ing.ingredient_id" class="form-control glass-input ing-select">
                                <option value="">Sastāvdaļa</option>
                                <option v-for="item in formIngredients" :key="item.id" :value="item.id">{{ item.name }}</option>
                            </select>
                            <input v-model.number="ing.quantity" type="number" min="0" step="0.01" placeholder="Daudzums" class="form-control glass-input ing-qty" />
                            <select v-model="ing.unit_id" class="form-control glass-input ing-unit">
                                <option value="">Vienība</option>
                                <option v-for="u in formUnits" :key="u.id" :value="u.id">{{ u.name }}</option>
                            </select>
                            <button class="remove-btn" @click="removeIngredient(i)">✕</button>
                        </div>
                        <button class="add-row-btn" @click="addIngredient">+ Pievienot sastāvdaļu</button>

                        <div class="section-divider"><span>Soļi</span></div>

                        <div v-for="(step, i) in form.instructions" :key="i" class="step-row">
                            <span class="step-badge">{{ i + 1 }}</span>
                            <textarea v-model="step.description" rows="2" placeholder="Aprakstiet soli..." class="form-control glass-input step-textarea"></textarea>
                            <button class="remove-btn" @click="removeStep(i)">✕</button>
                        </div>
                        <button class="add-row-btn" @click="addStep">+ Pievienot soli</button>
                    </div>

                    <div class="drawer-footer">
                        <button class="btn btn-secondary" @click="closeDrawer">Atcelt</button>
                        <button class="btn glass-btn" @click="save" :disabled="saving">
                            {{ saving ? 'Saglabā...' : 'Saglabāt' }}
                        </button>
                    </div>
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

.recipe-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.25rem;
}

.recipe-card {
    padding: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.recipe-thumb {
    position: relative;
    height: 140px;
    overflow: hidden;
}

.recipe-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.img-count {
    position: absolute;
    bottom: 8px;
    right: 8px;
    background: rgba(0,0,0,0.55);
    color: #fff;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 0.15rem 0.5rem;
    border-radius: 20px;
}

.recipe-card-body {
    padding: 1rem 1.25rem 0.5rem;
    flex: 1;
}

.recipe-name {
    font-size: 1rem;
    font-weight: 700;
    color: var(--warm-dark);
    margin: 0 0 0.5rem;
}

.recipe-meta {
    display: flex;
    gap: 0.4rem;
    flex-wrap: wrap;
}

.meta-badge {
    font-size: 0.75rem;
    padding: 0.2rem 0.6rem;
    background: rgba(255, 107, 53, 0.1);
    color: var(--primary-color);
    border-radius: 20px;
    font-weight: 600;
}

.recipe-card-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
    padding: 0.75rem 1.25rem 1rem;
}

.action-btn {
    padding: 0.4rem 0.75rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.edit-btn { background: rgba(255, 184, 77, 0.15); color: #b07800; }
.edit-btn:hover { background: rgba(255, 184, 77, 0.3); }
.delete-btn { background: rgba(230, 57, 70, 0.12); color: #c0392b; }
.delete-btn:hover { background: rgba(230, 57, 70, 0.25); }
.confirm-btn { background: rgba(230, 57, 70, 0.8); color: white; }
.confirm-btn:hover { background: rgba(230, 57, 70, 1); }
.cancel-btn { background: rgba(150, 150, 150, 0.15); color: var(--warm-dark); }

.empty-msg {
    color: var(--warm-dark);
    opacity: 0.6;
    grid-column: 1/-1;
    text-align: center;
    padding: 3rem 0;
}

.drawer-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.35);
    z-index: 2000;
    display: flex;
    justify-content: flex-end;
}

.drawer {
    width: 480px;
    max-width: 100vw;
    height: 100vh;
    display: flex;
    flex-direction: column;
    border-radius: 0;
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}

.drawer-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.15);
    flex-shrink: 0;
}

.drawer-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--warm-dark);
}

.drawer-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    cursor: pointer;
    color: var(--warm-dark);
    opacity: 0.6;
    transition: opacity 0.2s;
}

.drawer-close:hover { opacity: 1; }

.drawer-body {
    flex: 1;
    overflow-y: auto;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.drawer-footer {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    padding: 1rem 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.15);
    flex-shrink: 0;
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

.field-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.err { font-size: 0.8rem; color: #e63946; }

.checkbox-group { flex-direction: row; align-items: center; }

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
}

.section-divider {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 0.25rem 0;
}

.section-divider::before,
.section-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(255,107,53,0.25);
}

.section-divider span {
    font-weight: 700;
    font-size: 0.85rem;
    color: var(--primary-color);
    white-space: nowrap;
}

.image-preview-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
}

.preview-item {
    position: relative;
    width: 80px;
    height: 80px;
    border-radius: 10px;
    overflow: hidden;
    border: 2px solid rgba(255,255,255,0.2);
}

.preview-item--new {
    border-color: rgba(255,107,53,0.4);
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-remove {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: rgba(0,0,0,0.6);
    color: white;
    border: none;
    cursor: pointer;
    font-size: 0.65rem;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    transition: background 0.2s;
}

.preview-remove:hover { background: rgba(230,57,70,0.85); }

.preview-new-badge {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(255,107,53,0.75);
    color: white;
    font-size: 0.6rem;
    font-weight: 700;
    text-align: center;
    padding: 1px 0;
}

.upload-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.55rem 1rem;
    border: 1px dashed rgba(255, 107, 53, 0.5);
    border-radius: 10px;
    color: var(--primary-color);
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    width: fit-content;
}

.upload-btn:hover {
    background: rgba(255, 107, 53, 0.08);
    border-color: var(--primary-color);
}

.hidden-input {
    display: none;
}

.upload-hint {
    font-size: 0.75rem;
    color: var(--warm-dark);
    opacity: 0.5;
    margin: -0.5rem 0 0;
}

.ingredient-row {
    display: grid;
    grid-template-columns: 1fr 80px 90px 30px;
    gap: 0.4rem;
    align-items: center;
}

.step-row {
    display: grid;
    grid-template-columns: 28px 1fr 30px;
    gap: 0.5rem;
    align-items: flex-start;
}

.step-badge {
    width: 28px;
    height: 28px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    flex-shrink: 0;
    margin-top: 6px;
}

.step-textarea { resize: vertical; }

.remove-btn {
    background: none;
    border: none;
    color: #e63946;
    cursor: pointer;
    font-size: 0.9rem;
    padding: 0.25rem;
    border-radius: 4px;
    line-height: 1;
    transition: background 0.2s;
}

.remove-btn:hover { background: rgba(230,57,70,0.1); }

.add-row-btn {
    background: none;
    border: 1px dashed rgba(255, 107, 53, 0.4);
    color: var(--primary-color);
    border-radius: 8px;
    padding: 0.5rem;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 600;
    width: 100%;
    transition: all 0.2s;
}

.add-row-btn:hover {
    background: rgba(255, 107, 53, 0.08);
    border-color: var(--primary-color);
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
    .drawer { width: 100vw; }
    .field-row { grid-template-columns: 1fr; }
    .ingredient-row { grid-template-columns: 1fr 60px 70px 28px; }
}

/* ── Filters ── */
.admin-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 0.6rem;
    margin-bottom: 1.5rem;
    align-items: center;
}

.filter-input {
    padding: 0.5rem 0.8rem;
    font-size: 0.9rem;
    border: 1px solid rgba(255, 107, 53, 0.25);
    border-radius: 10px;
    background: var(--glass-bg);
    color: var(--warm-dark);
    min-width: 200px;
    flex: 1;
    outline: none;
    transition: border-color 0.2s;
}

.filter-input:focus {
    border-color: var(--primary-color);
}

.filter-select {
    padding: 0.5rem 0.7rem;
    font-size: 0.85rem;
    border: 1px solid rgba(255, 107, 53, 0.25);
    border-radius: 10px;
    background: var(--glass-bg);
    color: var(--warm-dark);
    cursor: pointer;
    transition: border-color 0.2s;
}

.filter-select--narrow {
    min-width: unset;
}

.filter-select:focus {
    border-color: var(--primary-color);
    outline: none;
}

.btn-clear {
    padding: 0.5rem 1rem;
    background: rgba(230, 57, 70, 0.12);
    color: #c0392b;
    border: 1px solid rgba(230, 57, 70, 0.25);
    border-radius: 10px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 600;
    transition: background 0.2s;
    white-space: nowrap;
}

.btn-clear:hover {
    background: rgba(230, 57, 70, 0.25);
}

@media (max-width: 768px) {
    .admin-filters {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-input,
    .filter-select {
        width: 100%;
    }
}

/* ── Pagination ── */
.pagination-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    margin-top: 2.5rem;
    padding-bottom: 2rem;
}

.glass-pagination {
    display: flex;
    gap: 0.4rem;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
    justify-content: center;
}

.page-item { display: inline-block; }

.glass-page-link {
    background: var(--glass-bg);
    backdrop-filter: blur(var(--glass-blur));
    -webkit-backdrop-filter: blur(var(--glass-blur));
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-md);
    padding: 0.5rem 0.9rem;
    min-width: 40px;
    color: var(--warm-dark);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 2px 8px rgba(255, 107, 53, 0.08);
    font-size: 0.9rem;
    text-align: center;
}

.glass-page-link:hover:not(:disabled):not(.disabled-ellipsis) {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(255, 107, 53, 0.3);
}

.page-item.active .glass-page-link {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    box-shadow: 0 4px 14px rgba(255, 107, 53, 0.35);
    transform: scale(1.05);
}

.page-item.disabled .glass-page-link,
.glass-page-link:disabled {
    opacity: 0.4;
    cursor: not-allowed;
    background: rgba(200, 200, 200, 0.2);
}

.disabled-ellipsis {
    cursor: default;
    background: transparent;
    box-shadow: none;
    border: none;
}

.pagination-info {
    color: var(--warm-dark);
    font-size: 0.9rem;
    font-weight: 500;
    opacity: 0.75;
    text-align: center;
}
</style>
