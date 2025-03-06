<template>
    <MainLayout>
        <div>
            <div class="card-flexbox">
                <div class="card-container">
                    <Card
                        image="/Burgers.jpg"
                        title="Recepšu meklēšana"
                        description="Meklē sev vēlamo recepti."
                        link="/receptes"
                    />
                    <Card
                        image="/Pelmeni.jpg"
                        title="Izmēģini mūsu AI"
                        description="Ģenerē receptes ar sev pieejamajiem produktiem."
                        link="/aireceptes"
                    />
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
</style>