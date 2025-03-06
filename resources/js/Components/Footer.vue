<template>
    <footer class="footer" v-if="!hideFooter">
      <div class="footer-container">
        <!-- Logo and Company Name -->
        <div class="logo">
          <img src="/foodyML_logo.png" alt="FoodyML Logo" />
          <h1>FOODYML</h1>
        </div>
        
        <!-- Footer Text -->
        <p class="footer-text">&copy; 2025 FOODYML. Visas tiesības aizsargātas.</p>
      </div>
    </footer>
  </template>
  
  <script>
  export default {
    name: "Footer",
    computed: {
      hideFooter() {
        // Use Inertia's $page.url instead of $route.path
        const url = this.$page.url;
        return (
          url === "/ienakt" ||
          url === "/registreties" ||
          url === "/aizmirsu-paroli" ||
          url === "/aireceptes"
        );
      },
    },
  };
  </script>
  
  <style scoped>
  * {
    font-family: monospace; 
  }
  
  .footer {
    padding: 20px 0;
    text-align: center;
  }
  
  .footer-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }
  
  .logo {
    display: flex;
    align-items: center;
  }
  
  .logo img {
    height: 35px;
    width: auto;
    margin-right: 10px;
  }
  
  .logo h1 {
    font-size: 1.5rem;
    background: linear-gradient(to right, #b927fc 0%, #2c64fc 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  
  .footer-text {
    font-size: 0.9rem;
    color: #555; /* A subtle gray for the text */
  }
  </style>