<template>
  <div id="app">
    <router-view></router-view>
  </div>
</template>

<script>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

export default {
  name: 'App',
  setup() {
    const route = useRoute();

    const isAuthPage = computed(() => {
      // Hide frontend navbar for auth pages and admin pages
      return route.path.includes('/login') ||
             route.path.includes('/register') ||
             route.path.startsWith('/dashboard');
    });

    return {
      isAuthPage
    };
  }
};
</script>

<style>
@import 'bootstrap/dist/css/bootstrap.min.css';
@import '@fortawesome/fontawesome-free/css/all.min.css';

body {
  margin: 0;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

#app {
  min-height: 100vh;
}

:root {
  --primary-color: #ff4c3b;
  --secondary-color: #2c3e50;
  --text-color: #333;
  --light-bg: #f8f9fa;
}

.btn-primary {
  background-color: var(--primary-color);
  border-color: var(--primary-color);
  padding: 10px 25px;
  border-radius: 5px;
  font-weight: 500;
}

.btn-primary:hover {
  background-color: #e6392a;
  border-color: #e6392a;
}

.mt-100 {
  margin-top: 100px;
}
.mb-100 {
  margin-bottom: 100px;
}
.mt-50 {
  margin-top: 50px;
}
.mb-50 {
  margin-bottom: 50px;
}
.mtb-50 {
  margin-bottom: 50px;
  margin-top: 50px;
}
.mtb-100 {
  margin-bottom: 100px;
  margin-top: 100px;
}
.pt-50 {
  padding-top: 50px;
}
.pb-50 {
  padding-bottom: 50px;
}
.ptb-50 {
  padding-bottom: 50px;
  padding-top: 50px;
}
.ptb-100 {
  padding-bottom: 100px;
  padding-top: 100px;
}
.mt-4-5 {
  margin-top: 2.5rem;
}
</style>
