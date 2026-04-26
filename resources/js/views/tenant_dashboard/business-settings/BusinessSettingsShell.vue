<template>
  <div class="bss">
    <div class="bss__grid">
      <aside class="bss__nav" aria-label="Business settings sections">
        <router-link
          v-for="item in items"
          :key="item.to"
          :to="item.to"
          class="bss__link"
          active-class="bss__link--active"
        >
          <i :class="item.icon" class="bss__ico" aria-hidden="true"></i>
          <span class="bss__label">{{ item.label }}</span>
        </router-link>
      </aside>
      <section class="bss__content">
        <router-view />
      </section>
    </div>
  </div>
</template>

<script setup>
const items = [
  { to: '/dashboard/settings/general', label: 'General', icon: 'fas fa-cog' },
  { to: '/dashboard/settings/branches', label: 'Branches', icon: 'fas fa-store' },
  { to: '/dashboard/settings/bill', label: 'Bill Settings', icon: 'fas fa-file-invoice' },
  { to: '/dashboard/settings/discounts', label: 'Discounts', icon: 'fas fa-percent' },
  { to: '/dashboard/settings/payments', label: 'Payment Accounts', icon: 'fas fa-wallet' }
]
</script>

<style scoped>
.bss {
  --bss-accent: #00844d;
  --bss-border: #e2e8f0;
  --bss-muted: #64748b;
  --bss-surface: #f8fafc;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  background: #fff;
  min-height: 100%;
}

.bss__grid {
  display: flex;
  gap: clamp(1rem, 3vw, 1.75rem);
  align-items: flex-start;
}

.bss__nav {
  width: min(188px, 32vw);
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
  padding: 0.4rem;
  background: #fff;
  border: 1px solid var(--bss-border);
  border-radius: 12px;
}

.bss__link {
  display: flex;
  align-items: center;
  gap: 0.55rem;
  padding: 0.5rem 0.65rem;
  border-radius: 10px;
  text-decoration: none;
  color: #334155;
  font-size: 0.8rem;
  font-weight: 500;
  border: 1px solid transparent;
  transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
}

.bss__link:hover {
  background: var(--bss-surface);
  color: #0f172a;
}

.bss__link--active {
  border-color: color-mix(in srgb, var(--bss-accent) 45%, var(--bss-border));
  background: color-mix(in srgb, var(--bss-accent) 11%, #fff);
  color: var(--bss-accent);
  font-weight: 700;
}

.bss__ico {
  width: 1.05rem;
  text-align: center;
  color: var(--bss-accent);
  flex-shrink: 0;
}

.bss__link--active .bss__ico {
  color: var(--bss-accent);
}

.bss__label {
  line-height: 1.3;
}

.bss__content {
  flex: 1;
  min-width: 0;
}

@media (max-width: 767.98px) {
  .bss__grid {
    flex-direction: column;
  }
  .bss__nav {
    width: 100%;
    flex-direction: row;
    flex-wrap: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    gap: 0.35rem;
    padding: 0.35rem;
    scrollbar-width: thin;
  }
  .bss__link {
    flex: 0 0 auto;
    white-space: nowrap;
  }
}
</style>
