<template>
  <BsPageFrame
    title="Branches"
    subtitle="Manage your restaurant branches, working hours, and delivery zones."
    icon="fas fa-store"
    save-label="Save changes"
  >
    <p class="text-muted small mb-3">Branches control where orders are prepared and which menus / delivery zones apply.</p>
    <div class="d-flex justify-content-end mb-3">
      <button type="button" class="btn btn-add" disabled>
        <i class="fas fa-plus me-1"></i> Add branch
      </button>
    </div>
    <div class="table-responsive border rounded-3 overflow-hidden">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Name</th>
            <th>Code</th>
            <th>Address</th>
            <th>Cutoff</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <span class="badge bg-accent-soft text-dark me-1">Current</span>
              <strong>Main branch</strong>
            </td>
            <td><code>main</code></td>
            <td class="text-muted">—</td>
            <td>4:00 AM</td>
            <td class="text-end text-muted small">Static</td>
          </tr>
          <tr>
            <td>
              <span class="badge bg-secondary bg-opacity-10 text-secondary me-1">Switch</span>
              <strong>Example branch</strong>
            </td>
            <td><code>001</code></td>
            <td>Sample address</td>
            <td>4:00 AM</td>
            <td class="text-end text-muted small">Static</td>
          </tr>
        </tbody>
      </table>
    </div>
    <p class="mt-3 mb-0 small text-muted">
      <i class="fas fa-sync-alt me-1"></i>
      Show deleted branches — coming soon
    </p>
  </BsPageFrame>
</template>

<script setup>
import BsPageFrame from './BsPageFrame.vue'
</script>

<style scoped>
.btn-add {
  background: #00844d;
  color: #fff;
  border-radius: 999px;
  font-weight: 600;
  font-size: 0.85rem;
  padding: 0.45rem 1rem;
  border: none;
}
.bg-accent-soft {
  background: color-mix(in srgb, #00844d 18%, #fff) !important;
}
</style>
