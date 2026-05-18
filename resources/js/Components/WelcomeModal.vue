<template>
  <Teleport to="body">
    <div v-if="visible" class="wm-overlay" @click.self="dismiss">
      <div class="wm-card glass-card">

        <button class="wm-skip" @click="dismiss">Izlaist</button>

        <div class="wm-slides">
          <transition name="slide-fade" mode="out-in">
            <div :key="step" class="wm-slide">
              <div class="wm-icon">{{ slides[step].icon }}</div>
              <h2 class="wm-title gradient-text">{{ slides[step].title }}</h2>
              <p class="wm-body">{{ slides[step].body }}</p>
              <a
                v-if="slides[step].link"
                :href="slides[step].link"
                class="wm-link"
                @click="dismiss"
              >{{ slides[step].linkLabel }} →</a>
            </div>
          </transition>
        </div>

        <div class="wm-dots">
          <span
            v-for="(_, i) in slides"
            :key="i"
            class="wm-dot"
            :class="{ active: i === step }"
            @click="step = i"
          />
        </div>

        <div class="wm-actions">
          <button v-if="step > 0" class="wm-btn wm-btn--ghost" @click="step--">Atpakaļ</button>
          <span v-else />
          <button v-if="step < slides.length - 1" class="wm-btn wm-btn--primary" @click="step++">Tālāk</button>
          <button v-else class="wm-btn wm-btn--primary" @click="dismiss">Sākt!</button>
        </div>

      </div>
    </div>
  </Teleport>
</template>

<script>
export default {
  name: 'WelcomeModal',
  props: {
    userId: { type: [Number, String], required: true },
  },
  data() {
    return {
      visible: false,
      step: 0,
      slides: [
        {
          icon: '👋',
          title: 'Laipni lūdzam FOODYML!',
          body: 'Tavs personalizētais recepšu palīgs. Ļauj mums īsi pastāstīt, ko vari darīt šeit.',
          link: null,
        },
        {
          icon: '🥗',
          title: 'Iestatiet savu profilu',
          body: 'Profila sadaļā vari pievienot savas alerģijas un uztura ierobežojumus. Receptes automātiski filtrēsies atbilstoši Tavām vajadzībām.',
          link: '/profils',
          linkLabel: 'Doties uz profilu',
        },
        {
          icon: '❤️',
          title: 'Pārlūkojiet receptes',
          body: 'Recepšu katalogā vari meklēt, filtrēt un saglabāt iecienītākās receptes favorītos, lai tās vienmēr būtu rokai.',
          link: '/receptes',
          linkLabel: 'Skatīt receptes',
        },
        {
          icon: '🛒',
          title: 'Iepirkumu saraksts un AI',
          body: 'Izvēlies vairākas receptes un automātiski saņem apkopotu iepirkumu sarakstu. Vai arī izmēģini mūsu AI, lai izveidotu recepti no produktiem, kas Tev ir pa rokai!',
          link: '/iepirkumi',
          linkLabel: 'Izmēģināt',
        },
      ],
    };
  },
  mounted() {
    const key = `foodyml_welcomed_${this.userId}`;
    if (!localStorage.getItem(key)) {
      this.visible = true;
    }
  },
  methods: {
    dismiss() {
      localStorage.setItem(`foodyml_welcomed_${this.userId}`, '1');
      this.visible = false;
    },
  },
};
</script>

<style scoped>
.wm-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  z-index: 3000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  animation: fadeIn 0.25s ease;
}

.wm-card {
  width: 100%;
  max-width: 460px;
  padding: 2rem 2rem 1.5rem;
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  /* Override glass-card transparency — modal needs a solid background */
  background: #fffaf5 !important;
  transform: none !important;
}

.wm-skip {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: none;
  border: none;
  font-size: 0.8rem;
  color: #999;
  cursor: pointer;
  transition: color 0.2s;
}
.wm-skip:hover { color: #5a3010; }

.wm-slides {
  min-height: 170px;
  display: flex;
  align-items: center;
}

.wm-slide {
  text-align: center;
  width: 100%;
}

.wm-icon {
  font-size: 2.5rem;
  margin-bottom: 0.75rem;
}

.wm-title {
  font-size: 1.4rem;
  font-weight: 800;
  margin-bottom: 0.6rem;
}

.wm-body {
  font-size: 0.95rem;
  color: #5a3010;
  line-height: 1.6;
  margin: 0;
}

.wm-link {
  display: inline-block;
  margin-top: 0.75rem;
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--primary-color);
  text-decoration: none;
  transition: opacity 0.2s;
}
.wm-link:hover { opacity: 0.75; }

.wm-dots {
  display: flex;
  justify-content: center;
  gap: 0.4rem;
}

.wm-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: rgba(255, 107, 53, 0.2);
  cursor: pointer;
  transition: background 0.2s;
}
.wm-dot.active {
  background: var(--primary-color);
  width: 22px;
  border-radius: 4px;
}

.wm-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 0.75rem;
}

.wm-btn {
  padding: 0.55rem 1.4rem;
  border-radius: 12px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
}

.wm-btn--primary {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  box-shadow: 0 3px 12px rgba(255, 107, 53, 0.3);
}
.wm-btn--primary:hover { transform: translateY(-1px); box-shadow: 0 5px 16px rgba(255, 107, 53, 0.4); }

.wm-btn--ghost {
  background: transparent;
  color: #5a3010;
  border: 1.5px solid rgba(255, 107, 53, 0.35);
}
.wm-btn--ghost:hover { border-color: var(--primary-color); color: var(--primary-color); }

.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.2s ease;
}
.slide-fade-enter-from { opacity: 0; transform: translateX(16px); }
.slide-fade-leave-to  { opacity: 0; transform: translateX(-16px); }

@keyframes fadeIn {
  from { opacity: 0; }
  to   { opacity: 1; }
}
</style>
