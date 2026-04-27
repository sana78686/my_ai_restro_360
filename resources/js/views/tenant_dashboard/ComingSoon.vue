<template>
  <div class="settings-page">
    <div class="card border-0 shadow-sm">
      <div class="card-body py-5 text-center">
        <i class="fas fa-hard-hat fa-2x text-muted mb-3" aria-hidden="true"></i>
        <h4 class="mb-2">{{ pageTitle }}</h4>
        <p class="text-muted mb-0">This area is not available yet. Check back soon.</p>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

export default {
  name: 'ComingSoon',
  setup() {
    const route = useRoute();
    const pageTitle = computed(() => route.meta.title || 'Page');
    return { pageTitle };
  }
};
</script>
