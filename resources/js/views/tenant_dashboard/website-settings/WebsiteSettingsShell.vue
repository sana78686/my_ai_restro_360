<template>
  <div class="wss">
    <div class="wss__topbar">
      <div class="wss__url">
        <span class="wss__url-label">Website URL</span>
        <a href="#" class="wss__url-link" @click.prevent>
          https://your-restaurant.example.com
          <i class="fas fa-external-link-alt ms-1 small"></i>
        </a>
      </div>
      <div class="wss__vis d-flex align-items-center gap-2">
        <span class="small text-muted text-uppercase fw-bold" style="letter-spacing: 0.06em">Visibility</span>
        <div class="form-check form-switch m-0">
          <input class="form-check-input wss__switch" type="checkbox" checked disabled id="visSwitch" />
          <label class="form-check-label small fw-semibold text-success" for="visSwitch">Visible</label>
        </div>
      </div>
    </div>

    <div class="wss__grid">
      <aside class="wss__nav" aria-label="Website settings">
        <router-link
          v-for="item in items"
          :key="item.to"
          :to="item.to"
          class="wss__link"
          active-class="wss__link--active"
        >
          <i :class="item.icon" class="wss__ico" aria-hidden="true"></i>
          <span class="flex-grow-1">{{ item.label }}</span>
          <span class="wss__dot" :class="item.done ? 'wss__dot--on' : 'wss__dot--off'" aria-hidden="true"></span>
        </router-link>
      </aside>
      <section class="wss__content">
        <router-view />
      </section>
    </div>
  </div>
</template>

<script setup>
const items = [
  { to: '/dashboard/website-settings/template', label: 'Template', icon: 'fas fa-layer-group', done: true },
  { to: '/dashboard/website-settings/branding', label: 'Branding', icon: 'fas fa-palette', done: true },
  { to: '/dashboard/website-settings/seo', label: 'SEO', icon: 'fas fa-search', done: true },
  { to: '/dashboard/website-settings/domain', label: 'Domain', icon: 'fas fa-link', done: true },
  { to: '/dashboard/website-settings/contact', label: 'Contact', icon: 'fas fa-address-book', done: true },
  { to: '/dashboard/website-settings/hero', label: 'Hero', icon: 'fas fa-image', done: true },
  { to: '/dashboard/website-settings/theme', label: 'Theme', icon: 'fas fa-star', done: true },
  { to: '/dashboard/website-settings/social', label: 'Social media', icon: 'fas fa-share-alt', done: false },
  { to: '/dashboard/website-settings/hours', label: 'Opening hours', icon: 'fas fa-clock', done: true },
  { to: '/dashboard/website-settings/sections', label: 'Website sections', icon: 'fas fa-th-large', done: true },
  { to: '/dashboard/website-settings/preferences', label: 'Settings', icon: 'fas fa-sliders-h', done: true }
]
</script>

<style scoped>
.wss {
  --a: #00844d;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background: #fff;
}

.wss__topbar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 0.75rem 1rem;
  margin: -0.25rem 0 1.25rem;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  background: #f8fafc;
}

.wss__url-label {
  display: block;
  font-size: 0.65rem;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: #00844d;
  margin-bottom: 0.15rem;
}

.wss__url-link {
  font-size: 0.88rem;
  font-weight: 600;
  color: #1a1a1a;
  text-decoration: none;
}

.wss__url-link:hover {
  color: var(--a);
}

.wss__switch {
  width: 2.5rem;
  height: 1.25rem;
  cursor: pointer;
}

.wss__switch:checked {
  background-color: var(--a);
  border-color: var(--a);
}

.wss__grid {
  display: flex;
  gap: 1.5rem;
  align-items: flex-start;
}

.wss__nav {
  width: min(200px, 36vw);
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  gap: 0.12rem;
  padding: 0.4rem;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
}

.wss__link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.45rem 0.55rem;
  border-radius: 10px;
  text-decoration: none;
  color: #334155;
  font-size: 0.78rem;
  font-weight: 500;
  border: 1px solid transparent;
  transition: background 0.15s ease, border-color 0.15s ease;
}

.wss__link:hover {
  background: #f1f5f9;
}

.wss__link--active {
  border-color: color-mix(in srgb, var(--a) 40%, #e2e8f0);
  background: color-mix(in srgb, var(--a) 10%, #fff);
  color: var(--a);
  font-weight: 700;
}

.wss__ico {
  width: 1.1rem;
  text-align: center;
  color: var(--a);
}

.wss__dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}
.wss__dot--on {
  background: var(--a);
}
.wss__dot--off {
  border: 2px solid #bbb;
  background: transparent;
}

.wss__content {
  flex: 1;
  min-width: 0;
}

@media (max-width: 767.98px) {
  .wss__grid {
    flex-direction: column;
  }
  .wss__nav {
    width: 100%;
    flex-direction: row;
    flex-wrap: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    gap: 0.35rem;
    scrollbar-width: thin;
  }
  .wss__link {
    flex: 0 0 auto;
    white-space: nowrap;
  }
}
</style>
