<template>
    <nav v-if="!hideNav">
      <div class="logo">
        <img src="/foodyML_logo.png" alt="logo" />
        <h1>FOODYML</h1>
      </div>
      <ul>
        <li><Link href="/">Sākums</Link></li>
        <li><Link href="/receptes">Receptes</Link></li>
        <li><Link href="/aireceptes">Ģenerēšana</Link></li>
        <li><Link href="/ienakt">Ienākt</Link></li>
      </ul>
      <div class="hamburger" :class="{ 'hamburger-active': menuActive }" @click="toggleNav">
        <span class="line"></span>
        <span class="line"></span>
        <span class="line"></span>
      </div>
    </nav>
    
    <div class="menubar" :class="{ active: menuActive }">
      <ul>
        <li><Link href="/" @click="toggleNav">Sākums</Link></li>
        <li><Link href="/receptes" @click="toggleNav">Receptes</Link></li>
        <li><Link href="/ienakt" @click="toggleNav">Ienākt</Link></li>
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
      // Use Inertia's $page object to access the current URL
      hideNav() {
        // Adjust the paths as needed for your logic
        return this.$page.url.startsWith("/ienakt") || this.$page.url.startsWith("/registreties");
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
nav .logo {
  display: flex;
  align-items: center;
}
nav .logo img {
  height: 35px;
  width: auto;
  margin-right: 10px;
}
nav .logo h1 {
  font-size: 1.5rem;
  background: linear-gradient(to right, #b927fc 0%, #2c64fc 100%);
  -webkit-background-clip: text;
  background-clip: text;
  -webkit-text-fill-color: transparent;
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