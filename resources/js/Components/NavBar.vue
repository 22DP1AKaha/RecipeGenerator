<template>
  <nav v-if="!hideNav" class="glass-navbar">
    <div class="container-fluid px-4">
      <div class="d-flex justify-content-between align-items-center py-3">
        <Link :href="route('home')" class="logo">
          <img src="/foodyML_logo.png" alt="FoodyML Logo" />
          <h1 class="gradient-text mb-0">FOODYML</h1>
        </Link>

        <ul class="desktop-menu d-none d-md-flex list-unstyled mb-0">
          <li><Link :href="route('home')" class="nav-link-glass">Sākums</Link></li>
          <li><Link :href="route('receptes')" class="nav-link-glass">Receptes</Link></li>
          <li><Link :href="route('aireceptes')" class="nav-link-glass">Ģenerēšana</Link></li>
          <li v-if="isUserLoggedIn" class="dropdown position-relative">
            <a class="nav-link-glass dropdown-toggle">Profils</a>
            <div class="dropdown-content glass-card">
              <Link :href="route('profile.edit')" class="dropdown-item-glass">Profils</Link>
              <Link :href="route('logout')" method="post" class="dropdown-item-glass">Iziet</Link>
            </div>
          </li>
          <li v-else>
            <Link :href="route('login')" class="glass-btn-small">Ienākt</Link>
          </li>
        </ul>

        <div class="hamburger d-md-none" :class="{ 'hamburger-active': menuActive }" @click="toggleNav">
          <span class="line"></span>
          <span class="line"></span>
          <span class="line"></span>
        </div>
      </div>
    </div>
  </nav>

  <div class="menubar glass-card" :class="{ active: menuActive }">
    <ul class="list-unstyled">
      <li><Link :href="route('home')" @click="toggleNav" class="mobile-link">Sākums</Link></li>
      <li><Link :href="route('receptes')" @click="toggleNav" class="mobile-link">Receptes</Link></li>
      <li><Link :href="route('aireceptes')" @click="toggleNav" class="mobile-link">Ģenerēšana</Link></li>
      <li v-if="isUserLoggedIn" class="mobile-dropdown">
        <a @click="mobileDropdownOpen = !mobileDropdownOpen" class="mobile-link">Profils</a>
        <div class="mobile-dropdown-content" v-show="mobileDropdownOpen">
          <Link :href="route('profile.edit')" @click="toggleNav; mobileDropdownOpen = false" class="mobile-link-sub">Profils</Link>
          <Link :href="route('logout')" method="post" @click="toggleNav; mobileDropdownOpen = false" class="mobile-link-sub">Iziet</Link>
        </div>
      </li>
      <li v-else>
        <Link :href="route('login')" @click="toggleNav" class="mobile-link">Ienākt</Link>
      </li>
    </ul>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';

export default {
  name: "Navbar",
  components: { Link },
  data() {
    return {
      menuActive: false,
      mobileDropdownOpen: false,
    };
  },
  computed: {
    hideNav() {
      const url = this.$page.url;
      return url.startsWith("/ienakt") || url.startsWith("/registreties");
    },
    isUserLoggedIn() {
      return this.$page.props.auth.user !== null;
    },
  },
  methods: {
    toggleNav() {
      this.menuActive = !this.menuActive;
      this.mobileDropdownOpen = false;
    },
  },
};
</script>

<style scoped>
.logo {
  display: flex;
  align-items: center;
  text-decoration: none;
  cursor: pointer;
  transition: transform 0.3s ease;
}

.logo:hover {
  transform: scale(1.05);
}

.logo img {
  height: 40px;
  width: auto;
  margin-right: 12px;
  filter: drop-shadow(0 2px 4px rgba(255, 107, 53, 0.3));
}

.logo h1 {
  font-size: 1.5rem;
  font-weight: 700;
}

.desktop-menu {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.nav-link-glass {
  text-decoration: none;
  color: var(--warm-dark);
  font-size: 1rem;
  font-weight: 500;
  padding: 0.5rem 1rem;
  border-radius: 12px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
}

.nav-link-glass:hover {
  background: rgba(255, 107, 53, 0.1);
  color: var(--primary-color);
  transform: translateY(-2px);
}

.glass-btn-small {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 12px;
  padding: 0.5rem 1.5rem;
  color: white;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(255, 107, 53, 0.25);
}

.glass-btn-small:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(255, 107, 53, 0.35);
  color: white;
}

.dropdown-content {
  display: none;
  position: absolute;
  min-width: 180px;
  top: calc(100% + 8px);
  right: 0;
  padding: 0.5rem;
  z-index: 1000;
}

.dropdown:hover .dropdown-content {
  display: block;
}

.dropdown-item-glass {
  display: block;
  padding: 0.75rem 1rem;
  color: var(--warm-dark);
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.2s ease;
  font-weight: 500;
}

.dropdown-item-glass:hover {
  background: rgba(255, 107, 53, 0.15);
  color: var(--primary-color);
  transform: translateX(4px);
}

.hamburger {
  cursor: pointer;
  z-index: 1001;
}

.hamburger .line {
  width: 28px;
  height: 3px;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  display: block;
  margin: 6px 0;
  border-radius: 2px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hamburger-active {
  transform: rotate(45deg);
}

.hamburger-active .line:nth-child(2) {
  opacity: 0;
  width: 0;
}

.hamburger-active .line:nth-child(1) {
  transform: translateY(9px);
}

.hamburger-active .line:nth-child(3) {
  transform: translateY(-9px) rotate(90deg);
}

.menubar {
  position: fixed;
  top: 0;
  left: -70%;
  width: 70%;
  max-width: 320px;
  height: 100vh;
  padding: 6rem 2rem 2rem;
  transition: left 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  z-index: 1000;
  overflow-y: auto;
}

.menubar.active {
  left: 0;
}

.menubar ul li {
  margin-bottom: 1rem;
}

.mobile-link {
  display: block;
  padding: 1rem;
  color: var(--warm-dark);
  text-decoration: none;
  border-radius: 12px;
  font-weight: 500;
  font-size: 1.1rem;
  transition: all 0.3s ease;
}

.mobile-link:hover {
  background: rgba(255, 107, 53, 0.1);
  color: var(--primary-color);
  transform: translateX(8px);
}

.mobile-dropdown-content {
  padding-left: 1rem;
  margin-top: 0.5rem;
}

.mobile-link-sub {
  display: block;
  padding: 0.75rem 1rem;
  color: var(--warm-dark);
  text-decoration: none;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.mobile-link-sub:hover {
  background: rgba(255, 107, 53, 0.1);
  color: var(--primary-color);
}
</style>