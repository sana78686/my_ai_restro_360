<template>
  <div class="tdb">
    <header class="tdb__header">
      <div>
        <h1 class="tdb__title">{{ $t('dashboard') }}</h1>
        <p class="tdb__subtitle">Manage your restaurant operations.</p>
      </div>
      <div class="tdb__header-meta text-muted small">
        {{ todayLabel }}
      </div>
    </header>

    <div class="row row-cols-2 row-cols-md-3 row-cols-xl-5 g-3 mb-4">
      <div v-for="card in statCards" :key="card.key" class="col">
        <div class="tdb-stat">
          <div class="tdb-stat__icon" :class="`tdb-stat__icon--${card.tone}`">
            <i :class="card.icon" aria-hidden="true"></i>
          </div>
          <div class="tdb-stat__body">
            <div class="tdb-stat__label">{{ card.label }}</div>
            <div class="tdb-stat__value">{{ card.value }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-md-6">
        <div class="tdb-panel">
          <h2 class="tdb-panel__title">Tables</h2>
          <p class="tdb-panel__hint text-muted small mb-0">Static preview — live counts coming soon.</p>
          <div class="tdb-panel__metrics mt-3">
            <span><strong class="tdb-accent">0</strong> occupied</span>
            <span class="text-muted">·</span>
            <span><strong>—</strong> available</span>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="tdb-panel">
          <h2 class="tdb-panel__title">Reservations</h2>
          <p class="tdb-panel__hint text-muted small mb-0">Static preview — calendar sync coming soon.</p>
          <p class="tdb-panel__empty mt-3 mb-0 text-muted">None today</p>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-lg-8">
        <div class="tdb-panel tdb-panel--tall">
          <h2 class="tdb-panel__title">Sales overview</h2>
          <p class="tdb-panel__hint text-muted small">Today · by hour (placeholder)</p>
          <div class="tdb-chart-placeholder">
            <span class="text-muted">Chart will load when analytics are connected.</span>
          </div>
        </div>
      </div>
      <div class="col-lg-4 d-flex flex-column gap-3">
        <div class="tdb-profit">
          <div class="tdb-profit__label">Today&apos;s profit</div>
          <div class="tdb-profit__value">Rs 0</div>
        </div>
        <div class="tdb-panel flex-grow-1">
          <h2 class="tdb-panel__title">Order types</h2>
          <p class="tdb-panel__empty text-muted small mb-0">No order data yet</p>
        </div>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="tdb-panel">
          <h2 class="tdb-panel__title">Received payments</h2>
          <div class="tdb-panel__big">Rs 0</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="tdb-panel">
          <h2 class="tdb-panel__title">Upcoming payments</h2>
          <p class="tdb-panel__empty text-muted small mb-0">No scheduled items</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="tdb-panel">
          <h2 class="tdb-panel__title">Top selling</h2>
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="text-muted small">Today</span>
            <span class="badge tdb-pill">Static</span>
          </div>
          <p class="tdb-panel__empty text-muted small mb-0">No sales data yet</p>
        </div>
      </div>
    </div>

    <div class="tdb-panel mb-4">
      <h2 class="tdb-panel__title">Inventory health</h2>
      <div class="row g-2 mt-2">
        <div v-for="box in inventoryBoxes" :key="box.key" class="col-6 col-md-3">
          <div class="tdb-inv" :class="`tdb-inv--${box.tone}`">
            <div class="tdb-inv__label">{{ box.label }}</div>
            <div class="tdb-inv__value">{{ box.value }}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="tdb-panel tdb-panel--pl">
      <h2 class="tdb-panel__title">This month · P&amp;L</h2>
      <div class="tdb-pl row text-center g-3 mt-2">
        <div class="col-md-4">
          <div class="text-muted small">Revenue</div>
          <div class="tdb-pl__num">Rs 0</div>
        </div>
        <div class="col-md-4">
          <div class="text-muted small">Expenses</div>
          <div class="tdb-pl__num">Rs 0</div>
        </div>
        <div class="col-md-4">
          <div class="text-muted small">Net profit</div>
          <div class="tdb-pl__num tdb-accent">Rs 0</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const todayLabel = computed(() =>
  new Date().toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' })
)

const statCards = [
  { key: 'rev', label: 'Revenue', value: 'Rs 0', icon: 'fas fa-coins', tone: 'green' },
  { key: 'ord', label: 'Orders', value: '0', icon: 'fas fa-receipt', tone: 'blue' },
  { key: 'net', label: 'Net profit', value: 'Rs 0', icon: 'fas fa-chart-line', tone: 'violet' },
  { key: 'avg', label: 'Avg order', value: 'Rs 0', icon: 'fas fa-shopping-basket', tone: 'amber' },
  { key: 'act', label: 'Active now', value: '0', icon: 'fas fa-user-clock', tone: 'slate' }
]

const inventoryBoxes = [
  { key: 'tot', label: 'Total items', value: '—', tone: 'blue' },
  { key: 'ok', label: 'Healthy', value: '—', tone: 'green' },
  { key: 'low', label: 'Low stock', value: '—', tone: 'amber' },
  { key: 'out', label: 'Out of stock', value: '—', tone: 'red' }
]
</script>

<style scoped>
.tdb {
  --tdb-accent: #00844d;
  --tdb-accent-soft: color-mix(in srgb, #00844d 8%, #fff);
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  max-width: 1400px;
  margin: 0 auto;
}

.tdb__header {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-end;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.tdb__title {
  font-size: 1.5rem;
  font-weight: 800;
  color: #1a1a1a;
  margin: 0;
  letter-spacing: -0.02em;
}

.tdb__subtitle {
  margin: 0.25rem 0 0;
  color: #5e5e5e;
  font-size: 0.95rem;
}

.tdb-accent {
  color: var(--tdb-accent);
}

.tdb-stat {
  display: flex;
  align-items: center;
  gap: 0.85rem;
  background: #fff;
  border: 1px solid #e8e8e8;
  border-radius: 12px;
  padding: 1rem 1.1rem;
  height: 100%;
  min-height: 88px;
}

.tdb-stat__icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  flex-shrink: 0;
}

.tdb-stat__icon--green {
  background: color-mix(in srgb, #00844d 15%, #fff);
  color: #00844d;
}
.tdb-stat__icon--blue {
  background: color-mix(in srgb, #1565c0 12%, #fff);
  color: #1565c0;
}
.tdb-stat__icon--violet {
  background: color-mix(in srgb, #6a1b9a 12%, #fff);
  color: #6a1b9a;
}
.tdb-stat__icon--amber {
  background: color-mix(in srgb, #ef6c00 12%, #fff);
  color: #c2410c;
}
.tdb-stat__icon--slate {
  background: #eceff1;
  color: #455a64;
}

.tdb-stat__label {
  font-size: 0.75rem;
  color: #5e5e5e;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.tdb-stat__value {
  font-size: 1.25rem;
  font-weight: 800;
  color: #1a1a1a;
  line-height: 1.2;
}

.tdb-panel {
  background: #fff;
  border: 1px solid #e8e8e8;
  border-radius: 12px;
  padding: 1.15rem 1.25rem;
  height: 100%;
}

.tdb-panel--tall {
  min-height: 280px;
  display: flex;
  flex-direction: column;
}

.tdb-panel__title {
  font-size: 1rem;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 0.25rem;
}

.tdb-panel__hint {
  margin: 0;
}

.tdb-panel__metrics {
  font-size: 0.9rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.35rem;
  align-items: center;
}

.tdb-panel__empty {
  font-size: 0.9rem;
}

.tdb-panel__big {
  font-size: 1.5rem;
  font-weight: 800;
  margin-top: 0.5rem;
  color: #1a1a1a;
}

.tdb-chart-placeholder {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 1rem;
  min-height: 180px;
  border-radius: 10px;
  background: #fafafa;
  border: 1px dashed #ddd;
  font-size: 0.9rem;
  text-align: center;
  padding: 1rem;
}

.tdb-profit {
  background: linear-gradient(135deg, #00844d 0%, #006b3f 100%);
  color: #fff;
  border-radius: 12px;
  padding: 1.25rem 1.35rem;
  box-shadow: 0 4px 20px rgba(0, 132, 77, 0.2);
}

.tdb-profit__label {
  font-size: 0.8rem;
  opacity: 0.92;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.tdb-profit__value {
  font-size: 1.65rem;
  font-weight: 800;
  margin-top: 0.35rem;
}

.tdb-pill {
  background: var(--tdb-accent-soft);
  color: var(--tdb-accent);
  font-weight: 600;
  border-radius: 999px;
  padding: 0.35rem 0.65rem;
}

.tdb-inv {
  border-radius: 10px;
  padding: 0.9rem 1rem;
  text-align: center;
}

.tdb-inv__label {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  opacity: 0.9;
}

.tdb-inv__value {
  font-size: 1.35rem;
  font-weight: 800;
  margin-top: 0.25rem;
}

.tdb-inv--blue {
  background: color-mix(in srgb, #1565c0 12%, #fff);
  color: #0d47a1;
}
.tdb-inv--green {
  background: color-mix(in srgb, #00844d 14%, #fff);
  color: #006b3f;
}
.tdb-inv--amber {
  background: color-mix(in srgb, #ef6c00 14%, #fff);
  color: #b45309;
}
.tdb-inv--red {
  background: color-mix(in srgb, #c62828 12%, #fff);
  color: #8b1a1a;
}

.tdb-panel--pl {
  border-color: #e0e0e0;
}

.tdb-pl__num {
  font-size: 1.35rem;
  font-weight: 800;
  color: #1a1a1a;
  margin-top: 0.25rem;
}

</style>
