<template>
  <nav v-if="!hideNav">
    <Link :href="route('home')" class="logo">
      <img src="/foodyML_logo.png" alt="FoodyML Logo" />
      <h1>FOODYML</h1>
    </Link>

    <ul class="desktop-menu">
      <li><Link :href="route('home')">Sākums</Link></li>
      <li><Link :href="route('receptes')">Receptes</Link></li>
      <li><Link :href="route('aireceptes')">Ģenerēšana</Link></li>
      <li v-if="isUserLoggedIn">
        <Link :href="route('profile.edit')">Profils</Link>
      </li>
      <li v-else>
        <Link :href="route('login')">Ienākt</Link>
      </li>
    </ul>

    <div class="hamburger" :class="{ 'hamburger-active': menuActive }" @click="toggleNav">
      <span class="line"></span>
      <span class="line"></span>
      <span class="line"></span>
    </div>
  </nav>

  <div class="menubar" :class="{ active: menuActive }">
    <ul>
      <li><Link :href="route('home')" @click="toggleNav">Sākums</Link></li>
      <li><Link :href="route('receptes')" @click="toggleNav">Receptes</Link></li>
      <li><Link :href="route('aireceptes')" @click="toggleNav">Ģenerēšana</Link></li>
      <li v-if="isUserLoggedIn">
        <Link :href="route('profile.edit')" @click="toggleNav">Profils</Link>
      </li>
      <li v-else>
        <Link :href="route('login')" @click="toggleNav">Ienākt</Link>
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
    };
  },
  computed: {
    hideNav() {
      // hide on login / register pages
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
    },
  },
};
</script>

<style scoped>
*{
  font-family: monospace;
}
nav {
  padding: 5px 5%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px,
  rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
  z-index: 1;
}
.logo {
  display: flex;
  align-items: center;
  text-decoration: none;
  cursor: pointer;
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
  margin: 0;
}

nav ul {
  list-style: none;
  display: flex;
}
nav ul li {
  margin-left: 1.5rem;
}
nav ul li a {
  text-decoration: none;
  color: #000;
  font-size: 1.2rem;
  padding: 4px 8px;
  border-radius: 5px;
}

nav ul li a:hover {
  background-color: rgb(243, 213, 170);
}

.hamburger {
  display: none;
  cursor: pointer;
}

.hamburger .line {
  width: 25px;
  height: 1px;
  background-color: #1f1f1f;
  display: block;
  margin: 7px auto;
  transition: all 0.3s ease-in-out;
}
.hamburger-active {
  transition: all 0.3s ease-in-out;
  transition-delay: 0.6s;
  transform: rotate(45deg);
}

.hamburger-active .line:nth-child(2) {
  width: 0px;
}

.hamburger-active .line:nth-child(1),
.hamburger-active .line:nth-child(3) {
  transition-delay: 0.3s;
}

.hamburger-active .line:nth-child(1) {
  transform: translateY(12px);
}

.hamburger-active .line:nth-child(3) {
  transform: translateY(-5px) rotate(90deg);
}

.menubar {
  position: absolute;
  top: 0;
  left: -60%;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  width: 60%;
  height: 100vh;
  padding: 20% 0;
  background: rgba(255, 255, 255);
  transition: all 0.5s ease-in;
  z-index: 2;
}
.active {
  left: 0;
  box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
}

.menubar ul {
  padding: 0;
  list-style: none;
}
.menubar ul li {
  margin-bottom: 32px;
}

.menubar ul li a {
  text-decoration: none;
  color: #000;
  font-size: 95%;
  font-weight: 400;
  padding: 5px 10px;
  border-radius: 5px;
}

.menubar ul li a:hover {
  background-color: #f5f5f5;
}
@media screen and (max-width: 790px) {
  .hamburger {
      display: block;
  }
  nav ul {
      display: none;
  }
}
</style>