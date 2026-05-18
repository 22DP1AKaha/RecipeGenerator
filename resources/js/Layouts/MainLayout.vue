<template>
  <div class="layout-wrapper">
    <Navbar />
    <slot />
    <Footer />
    <WelcomeModal v-if="$page.props.auth.user" :user-id="$page.props.auth.user.id" />
  </div>
</template>

<script>
import Footer from "@/Components/Footer.vue";
import Navbar from "@/Components/NavBar.vue";
import WelcomeModal from "@/Components/WelcomeModal.vue";

export default {
  components: {
    Navbar,
    Footer,
    WelcomeModal,
  },
};
</script>

<style>
.layout-wrapper {
  overflow-x: clip;
}
</style>
