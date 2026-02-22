<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <div class="verify-page">
        <Head title="E-pasta verifikācija" />
        <div class="container">
            <div class="row justify-content-center align-items-center min-vh-100">
                <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4">
                    <div class="verify-card glass-card">
                        <div class="logo-container text-center mb-4">
                            <img src="/foodyML_logo.png" alt="FoodyML Logo" class="logo-img" />
                            <h1 class="gradient-text mb-0">FOODYML</h1>
                        </div>

                        <div class="verify-icon">✉️</div>

                        <h2 class="verify-title">Apstipriniet e-pastu</h2>

                        <p class="verify-text">
                            Paldies par reģistrēšanos! Lai turpinātu, lūdzu apstipriniet savu
                            e-pasta adresi, noklikšķinot uz saites, ko nosūtījām jums.
                            Ja nesaņēmāt e-pastu, mēs ar prieku nosūtīsim jaunu.
                        </p>

                        <div v-if="verificationLinkSent" class="success-msg">
                            Jauna verifikācijas saite ir nosūtīta uz jūsu e-pasta adresi.
                        </div>

                        <form @submit.prevent="submit" class="verify-form">
                            <button
                                type="submit"
                                class="btn glass-btn w-100 mb-3"
                                :disabled="form.processing"
                            >
                                <span v-if="form.processing">Sūta...</span>
                                <span v-else>Atkārtoti nosūtīt verifikācijas e-pastu</span>
                            </button>

                            <div class="text-center">
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="logout-link"
                                >
                                    Iziet
                                </Link>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.verify-page {
    min-height: 100vh;
    background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
    background-attachment: fixed;
}

.verify-card {
    padding: 2.5rem 2rem;
    animation: fadeIn 0.6s ease-out;
    text-align: center;
}

.logo-img {
    height: 60px;
    width: auto;
    margin-bottom: 1rem;
    filter: drop-shadow(0 4px 8px rgba(255, 107, 53, 0.3));
}

.logo-container h1 {
    font-size: 2rem;
    font-weight: 800;
}

.verify-icon {
    font-size: 3rem;
    margin: 1rem 0 0.5rem;
}

.verify-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--warm-dark);
    margin-bottom: 1rem;
}

.verify-text {
    color: var(--warm-dark);
    opacity: 0.8;
    font-size: 0.95rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.success-msg {
    background: rgba(34, 197, 94, 0.12);
    border: 1px solid rgba(34, 197, 94, 0.35);
    color: #16a34a;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 1.25rem;
}

.verify-form {
    text-align: left;
}

.logout-link {
    background: none;
    border: none;
    color: var(--warm-dark);
    opacity: 0.6;
    font-size: 0.9rem;
    cursor: pointer;
    text-decoration: underline;
    font-family: inherit;
    transition: opacity 0.2s;
}

.logout-link:hover {
    opacity: 1;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 576px) {
    .verify-card {
        padding: 2rem 1.5rem;
    }

    .logo-img {
        height: 50px;
    }

    .logo-container h1 {
        font-size: 1.75rem;
    }
}
</style>
