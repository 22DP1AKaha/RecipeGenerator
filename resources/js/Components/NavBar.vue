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
            <a class="nav-link-glass dropdown-toggle" @click.stop="profileDropdownOpen = !profileDropdownOpen">Profils</a>
            <div class="dropdown-content glass-card" :style="{ display: profileDropdownOpen ? 'block' : 'none' }">
              <Link :href="route('profile.edit')" class="dropdown-item-glass">Profils</Link>
              <Link :href="route('logout')" method="post" as="button" class="dropdown-item-glass">Iziet</Link>
            </div>
          </li>
          <li v-else>
            <Link :href="route('login')" class="glass-btn-small">Ienākt</Link>
          </li>
          <li v-if="isAdmin" class="dropdown position-relative">
            <button class="cogwheel-btn" :class="{ active: adminDropdownOpen }" @click.stop="adminDropdownOpen = !adminDropdownOpen" title="Administrācija">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
              </svg>
            </button>
            <div class="dropdown-content glass-card" :style="{ display: adminDropdownOpen ? 'block' : 'none' }">
              <Link :href="route('admin.recipes.index')" class="dropdown-item-glass">Receptes</Link>
              <Link :href="route('admin.users.index')" class="dropdown-item-glass">Lietotāji</Link>
              <Link :href="route('admin.email')" class="dropdown-item-glass">Sūtīt e-pastu</Link>
            </div>
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
          <Link :href="route('logout')" method="post" as="button" @click="toggleNav; mobileDropdownOpen = false" class="mobile-link-sub">Iziet</Link>
        </div>
      </li>
      <li v-else>
        <Link :href="route('login')" @click="toggleNav" class="mobile-link">Ienākt</Link>
      </li>
      <li v-if="isAdmin" class="mobile-dropdown">
        <a @click="mobileAdminOpen = !mobileAdminOpen" class="mobile-link mobile-admin-link">⚙ Administrācija</a>
        <div class="mobile-dropdown-content" v-show="mobileAdminOpen">
          <Link :href="route('admin.recipes.index')" @click="toggleNav" class="mobile-link-sub">Receptes</Link>
          <Link :href="route('admin.users.index')" @click="toggleNav" class="mobile-link-sub">Lietotāji</Link>
          <Link :href="route('admin.email')" @click="toggleNav" class="mobile-link-sub">Sūtīt e-pastu</Link>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  name: "Navbar",
  components: { Link },
  data() {
    return {
      menuActive: false,
      mobileDropdownOpen: false,
      mobileAdminOpen: false,
      profileDropdownOpen: false,
      adminDropdownOpen: false,
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
    isAdmin() {
      return this.$page.props.auth.is_admin === true;
    },
  },
  methods: {
    toggleNav() {
      this.menuActive = !this.menuActive;
      this.mobileDropdownOpen = false;
      this.mobileAdminOpen = false;
    },
    closeDropdowns(e) {
      if (!this.$el.contains(e.target)) {
        this.profileDropdownOpen = false;
        this.adminDropdownOpen = false;
      }
    },
  },
  mounted() {
    document.addEventListener('click', this.closeDropdowns);
  },
  beforeUnmount() {
    document.removeEventListener('click', this.closeDropdowns);
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

.cogwheel-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 38px;
  height: 38px;
  border: none;
  background: rgba(255, 107, 53, 0.08);
  border-radius: 10px;
  color: var(--warm-dark);
  cursor: pointer;
  transition: all 0.3s ease;
}

.cogwheel-btn:hover,
.cogwheel-btn.active {
  background: rgba(255, 107, 53, 0.18);
  color: var(--primary-color);
  transform: rotate(45deg);
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


.dropdown-item-glass {
  display: block;
  width: 100%;
  padding: 0.75rem 1rem;
  color: var(--warm-dark);
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.2s ease;
  font-weight: 500;
  background: none;
  border: none;
  text-align: left;
  font-size: 1rem;
  cursor: pointer;
  box-sizing: border-box;
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
  cursor: pointer;
}

.mobile-link:hover {
  background: rgba(255, 107, 53, 0.1);
  color: var(--primary-color);
  transform: translateX(8px);
}

.mobile-admin-link {
  color: var(--primary-color);
  font-weight: 600;
}

.mobile-dropdown-content {
  padding-left: 1rem;
  margin-top: 0.5rem;
}

.mobile-link-sub {
  display: block;
  width: 100%;
  padding: 0.75rem 1rem;
  color: var(--warm-dark);
  text-decoration: none;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: none;
  border: none;
  text-align: left;
  cursor: pointer;
  box-sizing: border-box;
}

.mobile-link-sub:hover {
  background: rgba(255, 107, 53, 0.1);
  color: var(--primary-color);
}
</style>
