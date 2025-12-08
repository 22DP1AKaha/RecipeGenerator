<template>
    <footer class="footer glass-card" v-if="!hideFooter">
      <div class="container">
        <div class="row align-items-center justify-content-center text-center">
          <div class="col-12 mb-3">
            <div class="logo">
              <img src="/foodyML_logo.png" alt="FoodyML Logo" />
              <h1 class="gradient-text mb-0">FOODYML</h1>
            </div>
          </div>
          <div class="col-12">
            <p class="footer-text mb-0">&copy; 2025 FOODYML. Visas tiesības aizsargātas.</p>
          </div>
        </div>
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
  .footer {
    margin-top: 4rem;
    padding: 2rem 1rem;
    border-top: 1px solid var(--glass-border);
  }

  .logo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
  }

  .logo img {
    height: 40px;
    width: auto;
    filter: drop-shadow(0 2px 4px rgba(255, 107, 53, 0.3));
  }

  .logo h1 {
    font-size: 1.5rem;
    font-weight: 700;
  }

  .footer-text {
    font-size: 0.95rem;
    color: var(--warm-dark);
    opacity: 0.7;
  }

  @media (max-width: 768px) {
    .footer {
      margin-top: 3rem;
      padding: 1.5rem 1rem;
    }

    .logo h1 {
      font-size: 1.25rem;
    }

    .footer-text {
      font-size: 0.85rem;
    }
  }
  </style>