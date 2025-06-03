<template>
    <MainLayout>
      <div class="card-flexbox">
        <div class="card-container">
          <Card
            :link="route('receptes')"
            image="/Burgers.jpg"
            title="Recepšu meklēšana"
            description="Meklē sev vēlamo recepti."
          />
          <Card
            :link="route('aireceptes')"
            image="/Pelmeni.jpg"
            title="Izmēģini mūsu AI"
            description="Ģenerē receptes ar sev pieejamajiem produktiem."
          />
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
.card-flexbox { 
    margin-top: 4.375rem;
}

.card-container {
    display: flex;
    justify-content: space-around;
    flex-direction: row;
    align-items: flex-start;
    text-align: center;
    gap: 1rem;
    padding: 0.5rem;
    padding-bottom: 1rem;
}

/* Mobile optimization */
@media (max-width: 768px) {
    .card-flexbox {
        margin-top: 2rem; /* Reduced top margin */
    }
    
    .card-container {
        flex-direction: column; /* Stack cards vertically */
        align-items: center; /* Center cards horizontally */
        gap: 1.5rem; /* Increase vertical spacing */
        padding: 0 1rem 1rem; /* Add horizontal padding */
    }
    
    /* Ensure cards don't get too wide on mobile */
    .card-container > * {
        width: 100%;
        max-width: 320px;
    }
}
</style>