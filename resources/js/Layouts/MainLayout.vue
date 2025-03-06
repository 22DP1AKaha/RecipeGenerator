<template>
    <div>
      <Navbar />
      <slot /> <!-- This is where the page content will go -->
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
  