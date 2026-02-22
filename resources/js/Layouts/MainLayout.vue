<template>
    <div class="layout-wrapper">
      <Navbar />
      <slot />
      <Footer />
    </div>
</template>
  
  <script>
  import Footer from "@/Components/Footer.vue";
import Navbar from "@/Components/NavBar.vue";
  
  export default {
    components: {
      Navbar,
      Footer,
    },
  };
  </script>

<style>
.layout-wrapper {
  overflow-x: clip;
}
</style>
