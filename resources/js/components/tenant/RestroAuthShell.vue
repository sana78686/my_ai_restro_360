<template>
  <div class="restro-auth">
    <div class="restro-auth__wrap">
      <main class="restro-auth__grid" :aria-label="ariaLabel || undefined">
        <div class="restro-auth__col restro-auth__col--form">
          <div class="restro-auth__form-card">
            <h2 v-if="title" class="restro-auth__title">{{ title }}</h2>
            <p v-if="subtitle" class="restro-auth__sub">{{ subtitle }}</p>
            <div class="restro-auth__form-body">
              <slot />
            </div>
          </div>
        </div>
        <aside class="restro-auth__col restro-auth__col--content" aria-label="About AiRestro 360">
          <h1 class="restro-auth__headline restro-auth__headline--split">
            <span class="restro-auth__headline-line restro-auth__headline-line--ink">
              {{ $t('home.landing.headlineBeforeBrand') }}
            </span>
            <span class="restro-auth__headline-line restro-auth__headline-line--brand">
              {{ $t('home.landing.headlineBrand') }}
            </span>
          </h1>
          <ul class="restro-auth__bullets">
            <li v-for="k in bulletKeys" :key="k" class="restro-auth__bullet">
              {{ $t('home.landing.' + k) }}
            </li>
          </ul>
          <p class="restro-auth__copy">{{ $t('home.landing.copy1') }}</p>
          <p class="restro-auth__copy restro-auth__copy--lead">
            <strong>{{ $t('home.landing.copyLead') }}</strong>
          </p>
          <p class="restro-auth__partners">{{ $t('home.landing.partnersLine') }}</p>
          <div class="restro-auth__logos" role="list">
            <div
              v-for="(name, i) in brandKeys"
              :key="i"
              class="restro-auth__logo-pill"
              role="listitem"
            >
              {{ $t('home.landing.' + name) }}
            </div>
          </div>
        </aside>
      </main>
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, default: '' },
  subtitle: { type: String, default: '' },
  ariaLabel: { type: String, default: '' }
})

const bulletKeys = ['bullet1', 'bullet2', 'bullet3']
const brandKeys = ['brandA', 'brandB', 'brandC']
</script>

<style scoped>
.restro-auth {
  --restro-accent: #00844d;
  --restro-ink: #111;
  --restro-border: #d0d0d0;
  --restro-body: #5e5e5e;
  position: relative;
  overflow-x: hidden;
  background: #fff;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  padding-bottom: 1.5rem;
}

.restro-auth__wrap {
  position: relative;
  z-index: 1;
  max-width: 1200px;
  margin: 0 auto;
  padding: 1.25rem 1.25rem 2rem;
}

.restro-auth__grid {
  display: grid;
  grid-template-columns: minmax(0, 7fr) minmax(0, 5fr);
  gap: 1.5rem 2rem;
  align-items: start;
}

.restro-auth__form-card {
  background: #fff;
  border-radius: 12px;
  padding: 1.35rem 1.4rem 1.65rem;
  border: 1px solid #d8d8d8;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
  max-width: 100%;
}

.restro-auth__title {
  font-size: 1.1rem;
  font-weight: 800;
  color: #1a1a1a;
  margin: 0 0 0.35rem;
  line-height: 1.3;
  letter-spacing: -0.02em;
}

.restro-auth__sub {
  margin: 0 0 1rem;
  font-size: 0.88rem;
  color: var(--restro-body);
  line-height: 1.45;
}

.restro-auth__form-body {
  min-width: 0;
}

.restro-auth__col--content {
  z-index: 2;
  padding-top: 0.25rem;
  max-width: 100%;
  align-self: start;
}

.restro-auth__headline {
  font-size: clamp(1.05rem, 2.2vw, 1.4rem);
  font-weight: 800;
  letter-spacing: 0.04em;
  line-height: 1.25;
  text-transform: uppercase;
  color: #1a1a1a;
  margin: 0 0 1rem;
}
.restro-auth__headline--split {
  line-height: 1.05;
  letter-spacing: 0.02em;
  font-size: clamp(1.1rem, 2.1vw, 1.6rem);
}
.restro-auth__headline-line {
  display: block;
  margin-bottom: 0.12em;
}
.restro-auth__headline-line--ink {
  color: #111;
}
.restro-auth__headline-line--brand {
  color: #00844d;
}

.restro-auth__bullets {
  margin: 0 0 1.1rem 1.1rem;
  padding: 0;
  color: #2d2d2d;
  font-size: 0.95rem;
  line-height: 1.55;
  list-style: disc;
}
.restro-auth__bullet {
  margin-bottom: 0.5rem;
}
.restro-auth__bullet::marker {
  color: var(--restro-accent);
}

.restro-auth__copy {
  font-size: clamp(0.92rem, 1.15vw, 1rem);
  line-height: 1.6;
  color: var(--restro-body);
  margin: 0 0 0.9rem;
  max-width: 38em;
}
.restro-auth__copy--lead {
  margin-bottom: 1.15rem;
}
.restro-auth__partners {
  font-size: 0.88rem;
  color: #666;
  line-height: 1.5;
  margin: 0 0 0.75rem;
}
.restro-auth__logos {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.6rem;
}
.restro-auth__logo-pill {
  min-height: 2.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  font-size: 0.72rem;
  font-weight: 600;
  color: #555;
  background: #fff;
  border: 1px solid #e3e3e3;
  border-radius: 8px;
  padding: 0.45rem 0.4rem;
  line-height: 1.2;
}

@media (max-width: 991.98px) {
  .restro-auth__grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  .restro-auth__logos {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 576px) {
  .restro-auth__wrap {
    padding: 1rem 0.9rem 1.5rem;
  }
}
</style>

<!-- Form primitives shared by login / forgot / reset (matches register Home.vue) -->
<style>
.restro-auth .restro-form {
  display: flex;
  flex-direction: column;
  gap: 0.72rem;
}
.restro-auth .restro-form__row {
  display: grid;
  gap: 0.75rem 1.15rem;
}
.restro-auth .restro-form__row--2 {
  grid-template-columns: 1fr 1fr;
}
.restro-auth .restro-form__field {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  min-width: 0;
}
.restro-auth .restro-form__label-row {
  display: flex;
  align-items: center;
  gap: 0.35rem;
}
.restro-auth .restro-form__label {
  font-size: 0.8rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}
.restro-auth .restro-form__input,
.restro-auth .restro-form__select {
  width: 100%;
  min-width: 0;
  border: 1px solid #d0d0d0;
  border-radius: 8px;
  padding: 0.55rem 0.7rem;
  font-size: 0.95rem;
  background: #fff;
  color: #111;
  box-sizing: border-box;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.restro-auth .restro-form__input::placeholder {
  color: #9a9a9a;
  opacity: 1;
  font-size: 0.9rem;
}
.restro-auth .restro-form__input:focus,
.restro-auth .restro-form__select:focus {
  outline: none;
  box-shadow: 0 0 0 2px color-mix(in srgb, #00844d 25%, #fff);
  border-color: #00844d;
}
.restro-auth .restro-form__field--pw {
  position: relative;
}
.restro-auth .restro-form__pw-toggle {
  position: absolute;
  right: 0.45rem;
  bottom: 0.38rem;
  border: none;
  background: transparent;
  color: #666;
  font-size: 0.75rem;
  font-weight: 600;
  cursor: pointer;
  padding: 0.2rem 0.35rem;
  border-radius: 4px;
}
.restro-auth .restro-form__pw-toggle:hover {
  color: #00844d;
}
.restro-auth .restro-form__input--with-toggle {
  padding-right: 4.2rem;
}
.restro-auth .restro-form__check-row {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  font-size: 0.8rem;
  color: #333;
}
.restro-auth .restro-form__checkbox {
  width: 1rem;
  height: 1rem;
  accent-color: #00844d;
}
.restro-auth .restro-form__link {
  color: #00844d;
  font-weight: 600;
  text-decoration: none;
  font-size: 0.85rem;
  align-self: flex-end;
  margin-top: 0.15rem;
}
.restro-auth .restro-form__link:hover {
  text-decoration: underline;
}
.restro-auth .restro-form__msg {
  margin: 0 0 0.5rem;
  font-size: 0.88rem;
  line-height: 1.45;
  padding: 0.55rem 0.7rem;
  border-radius: 8px;
}
.restro-auth .restro-form__msg--error {
  color: #6e1414;
  background: #fff0f0;
  border: 1px solid #e8b4b4;
}
.restro-auth .restro-form__msg--info {
  color: #1a3d66;
  background: #f0f6ff;
  border: 1px solid #b8cceb;
}
.restro-auth .restro-form__submit {
  width: 100%;
  border: none;
  border-radius: 999px;
  background: #00844d;
  color: #fff;
  font-weight: 700;
  font-size: 0.9rem;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  padding: 0.72rem 1rem;
  cursor: pointer;
  margin-top: 0.25rem;
  transition: filter 0.15s, opacity 0.15s;
}
.restro-auth .restro-form__submit:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #00844d;
}
.restro-auth .restro-form__submit:hover:not(:disabled) {
  filter: brightness(0.95);
}
.restro-auth .restro-form__submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.restro-auth .restro-form__subbtn {
  border: none;
  background: none;
  color: #00844d;
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  padding: 0.15rem 0;
  text-decoration: underline;
}
.restro-auth .restro-form__subbtn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  text-decoration: none;
}
.restro-auth .restro-form__hr {
  border: none;
  border-top: 1px solid #e8e8e8;
  margin: 0.4rem 0 0.6rem;
}
.restro-auth .restro-form__verify {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.restro-auth .restro-form__verify-title {
  font-size: 0.85rem;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}
.restro-auth .restro-form__verify-hint {
  font-size: 0.8rem;
  color: #5e5e5e;
  margin: 0;
  line-height: 1.4;
}
@media (max-width: 576px) {
  .restro-auth .restro-form__row--2 {
    grid-template-columns: 1fr;
  }
}
</style>
