<template>
  <div class="frontend-layout">
    <!-- Top Bar -->
    <div class="top-bar bg-dark text-light py-2">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-8">
            <ul class="list-inline mb-0">
              <li class="list-inline-item me-3">
                <a href="mailto:contact@example.com" class="text-light text-decoration-none">
                  <i class="fas fa-envelope me-1"></i> {{ $t('topbar.email') }}
                </a>
              </li>
              <li class="list-inline-item">
                <a href="tel:+1234567890" class="text-light text-decoration-none">
                  <i class="fas fa-phone me-1"></i> {{ $t('topbar.phone') }}
                </a>
              </li>
            </ul>
          </div>
          <div class="col-md-4 text-end">
            <language-switcher />
          </div>
        </div>
      </div>
    </div>

    <!-- Header -->
    <header class="header">
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
          <router-link class="navbar-brand" to="/">
            <img src="/images/logo.png" alt="Logo" class="logo" />
          </router-link>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
              <li class="nav-item">
                <router-link class="nav-link" :to="{ name: 'food-ordering' }">{{ $t('nav.foodOrdering') }}</router-link>
              </li>
              <li class="nav-item">
                <router-link class="nav-link" :to="{ name: 'restaurant-services' }">{{ $t('nav.restaurantServices') }}</router-link>
              </li>
              <li class="nav-item">
                <router-link class="nav-link" :to="{ name: 'pricing' }">{{ $t('nav.pricing') }}</router-link>
              </li>
              <li class="nav-item">
                <router-link class="nav-link" :to="{ name: 'contact' }">{{ $t('nav.logintorestaurant') }}</router-link>
              </li>
              <li class="nav-item">
                <router-link class="nav-link btn btn-primary text-white" :to="{ name: 'tenant-register' }">
                  {{ $t('nav.getAccess') }}
                </router-link>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
      <router-view></router-view>
    </main>

    <!-- Footer -->
    <footer class="footer bg-dark text-light py-5">
      <div class="container">
        <div class="row">
          <div class="col-md-4 mb-4">
            <h5 class="mb-3">{{ $t('footer.aboutUs') }}</h5>
            <p>{{ $t('footer.aboutText') }}</p>
          </div>
          <div class="col-md-4 mb-4">
            <h5 class="mb-3">{{ $t('footer.quickLinks.title') }}</h5>
            <ul class="list-unstyled">
              <li class="mb-2">
                <router-link class="text-light text-decoration-none" :to="{ name: 'about' }">{{ $t('footer.aboutUs') }}</router-link>
              </li>
              <li class="mb-2">
                <router-link class="text-light text-decoration-none" :to="{ name: 'contact' }">{{ $t('footer.contactUs') }}</router-link>
              </li>
              <li class="mb-2">
                <router-link class="text-light text-decoration-none" :to="{ name: 'privacy' }">{{ $t('footer.privacy') }}</router-link>
              </li>
              <li class="mb-2">
                <router-link class="text-light text-decoration-none" :to="{ name: 'terms' }">{{ $t('footer.terms') }}</router-link>
              </li>
            </ul>
          </div>
          <div class="col-md-4 mb-4">
            <h5 class="mb-3">{{ $t('footer.contactInfo') }}</h5>
            <ul class="list-unstyled">
              <li class="mb-2">
                <i class="fas fa-map-marker-alt me-2"></i> {{ $t('footer.address') }}
              </li>
              <li class="mb-2">
                <i class="fas fa-phone me-2"></i> {{ $t('topbar.phone') }}
              </li>
              <li class="mb-2">
                <i class="fas fa-envelope me-2"></i> {{ $t('topbar.email') }}
              </li>
            </ul>
          </div>
        </div>
        <hr class="my-4 bg-light">
        <div class="text-center">
          <p class="mb-0">{{ $t('footer.copyright') }}</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import LanguageSwitcher from '../components/frontend/LanguageSwitcher.vue'
</script>

<style scoped>
.frontend-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.top-bar {
  font-size: 0.9rem;
  background-color: #1a1a1a !important;
  padding: 0.5rem 0;
}

.top-bar a {
  color: rgba(255, 255, 255, 0.8) !important;
  transition: color 0.3s ease;
}

.top-bar a:hover {
  color: #d4af37 !important;
  text-decoration: none;
}

.top-bar i {
  color: #d4af37;
}

.header {
  position: sticky;
  top: 0;
  z-index: 1000;
  background-color: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.navbar {
  padding: 1rem 0;
}

.logo {
  height: 40px;
  width: auto;
}

.nav-link {
  font-weight: 500;
  color: #333;
  margin: 0 0.5rem;
  transition: color 0.3s ease;
}

.nav-link:hover {
  color: #d4af37;
}

.nav-link.btn {
  padding: 0.5rem 1.5rem;
  margin-left: 1rem;
  background-color: #d4af37;
  border-color: #d4af37;
}

.nav-link.btn:hover {
  background-color: #c4a030;
  border-color: #c4a030;
  color: white;
}

.main-content {
  flex: 1;
}

.footer {
  margin-top: auto;
}

.footer h5 {
  color: #d4af37;
}

@media (max-width: 768px) {
  .top-bar {
    text-align: center;
  }

  .top-bar .col-md-4 {
    margin-top: 0.5rem;
  }

  .navbar-nav {
    text-align: center;
  }

  .nav-link {
    margin: 0.5rem 0;
  }

  .nav-link.btn {
    margin: 0.5rem auto;
    display: inline-block;
  }
}
</style> 