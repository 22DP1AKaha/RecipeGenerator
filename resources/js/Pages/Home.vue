<template>
    <MainLayout>
      <div class="home-container">
        <div class="hero-section">
          <h1 class="hero-title">
            <span class="gradient-text">Atklāj garšas pasauli</span>
          </h1>
          <p class="hero-subtitle">Tavs personalizētais recepšu palīgs</p>
        </div>

        <div class="container">
          <div class="row g-4 justify-content-center">
            <div class="col-12 col-md-6 col-lg-5">
              <Card
                :link="route('receptes')"
                image="/Burgers.jpg"
                title="Recepšu meklēšana"
                description="Meklē sev vēlamo recepti."
              />
            </div>
            <div class="col-12 col-md-6 col-lg-5">
              <Card
                :link="route('aireceptes')"
                image="/Pelmeni.jpg"
                title="Izmēģini mūsu AI"
                description="Ģenerē receptes ar sev pieejamajiem produktiem."
              />
            </div>
          </div>
        </div>
      </div>
    </MainLayout>
</template>


<script>
import Card from "@/Components/Card.vue";
import MainLayout from "@/Layouts/MainLayout.vue";

export default {
    components: {
        MainLayout,
        Card,
    }
};
</script>

<style scoped>
.home-container {
    min-height: calc(100vh - 200px);
    padding: 2rem 1rem;
}

.hero-section {
    text-align: center;
    margin-bottom: 3rem;
    animation: fadeInDown 0.8s ease-out;
}

.hero-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 1rem;
    color: var(--warm-dark);
}

.hero-subtitle {
    font-size: 1.25rem;
    color: var(--warm-dark);
    opacity: 0.8;
    font-weight: 400;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .home-container {
        padding: 1.5rem 0.75rem;
    }

    .hero-section {
        margin-bottom: 2rem;
    }

    .hero-title {
        font-size: 2.5rem;
    }

    .hero-subtitle {
        font-size: 1.1rem;
    }
}

@media (max-width: 480px) {
    .hero-title {
        font-size: 2rem;
    }

    .hero-subtitle {
        font-size: 1rem;
    }
}
</style>
