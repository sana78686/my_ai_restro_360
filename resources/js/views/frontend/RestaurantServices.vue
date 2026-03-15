<template>
  <div class="restaurant-services-page mt-100">
    <!-- Hero Section -->
    <section class="hero-section ptb-100" style="background: linear-gradient(135deg, #c62828 0%, #d32f2f 100%); color: white;">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <h1 class="hero-title mb-4">{{ $t('restaurantServices.hero.title') }}</h1>
            <p class="hero-text mb-4">{{ $t('restaurantServices.hero.subtitle') }}</p>
            <div class="d-flex gap-3 flex-wrap">
              <router-link to="/tenant-register" class="btn btn-light btn-lg px-4 py-2">{{ $t('restaurantServices.hero.startFreeTrial') }}</router-link>
              <a href="#services" class="btn btn-outline-light btn-lg px-4 py-2">{{ $t('restaurantServices.hero.viewAllServices') }}</a>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="text-center">
              <img src="/Mox_files/food-odering.png" alt="Restaurant Services" class="img-fluid rounded " style="max-width: 100%;">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Main Services Overview -->
    <section id="services" class="main-services ptb-100">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title">{{ $t('restaurantServices.mainServices.title') }}</h2>
          <p class="section-subtitle">{{ $t('restaurantServices.mainServices.subtitle') }}</p>
        </div>

        <div class="row g-4">
          <!-- Website Builder -->
          <div class="col-lg-6">
            <div class="service-card p-4 h-100" style="background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); border-left: 5px solid #c62828;">
              <div class="d-flex align-items-center mb-3">
                <div class="service-icon me-3" style="width: 60px; height: 60px; background: #c62828; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                  <i class="fas fa-globe text-white fa-2x"></i>
                </div>
                <h3 class="mb-0" style="color: #1a2734; font-size: 1.5rem;">{{ $t('restaurantServices.mainServices.websiteBuilder.title') }}</h3>
              </div>
              <p class="mb-3" style="color: #666; font-size: 1.1rem;">{{ $t('restaurantServices.mainServices.websiteBuilder.subtitle') }}</p>
              <ul class="list-unstyled">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.websiteBuilder.feature1') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.websiteBuilder.feature2') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.websiteBuilder.feature3') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.websiteBuilder.feature4') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.websiteBuilder.feature5') }}</li>
              </ul>
            </div>
          </div>

          <!-- Online Ordering -->
          <div class="col-lg-6">
            <div class="service-card p-4 h-100" style="background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); border-left: 5px solid #c62828;">
              <div class="d-flex align-items-center mb-3">
                <div class="service-icon me-3" style="width: 60px; height: 60px; background: #c62828; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                  <i class="fas fa-shopping-cart text-white fa-2x"></i>
                </div>
                <h3 class="mb-0" style="color: #1a2734; font-size: 1.5rem;">{{ $t('restaurantServices.mainServices.onlineOrdering.title') }}</h3>
              </div>
              <p class="mb-3" style="color: #666; font-size: 1.1rem;">{{ $t('restaurantServices.mainServices.onlineOrdering.subtitle') }}</p>
              <ul class="list-unstyled">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.onlineOrdering.feature1') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.onlineOrdering.feature2') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.onlineOrdering.feature3') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.onlineOrdering.feature4') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.onlineOrdering.feature5') }}</li>
              </ul>
            </div>
          </div>

          <!-- Table Reservations -->
          <div class="col-lg-6">
            <div class="service-card p-4 h-100" style="background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); border-left: 5px solid #c62828;">
              <div class="d-flex align-items-center mb-3">
                <div class="service-icon me-3" style="width: 60px; height: 60px; background: #c62828; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                  <i class="fas fa-calendar-alt text-white fa-2x"></i>
                </div>
                <h3 class="mb-0" style="color: #1a2734; font-size: 1.5rem;">{{ $t('restaurantServices.mainServices.tableReservation.title') }}</h3>
              </div>
              <p class="mb-3" style="color: #666; font-size: 1.1rem;">{{ $t('restaurantServices.mainServices.tableReservation.subtitle') }}</p>
              <ul class="list-unstyled">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.tableReservation.feature1') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.tableReservation.feature2') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.tableReservation.feature3') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.tableReservation.feature4') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.tableReservation.feature5') }}</li>
              </ul>
            </div>
          </div>

          <!-- Menu Management -->
          <div class="col-lg-6">
            <div class="service-card p-4 h-100" style="background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); border-left: 5px solid #c62828;">
              <div class="d-flex align-items-center mb-3">
                <div class="service-icon me-3" style="width: 60px; height: 60px; background: #c62828; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                  <i class="fas fa-utensils text-white fa-2x"></i>
                </div>
                <h3 class="mb-0" style="color: #1a2734; font-size: 1.5rem;">{{ $t('restaurantServices.mainServices.menuManagement.title') }}</h3>
              </div>
              <p class="mb-3" style="color: #666; font-size: 1.1rem;">{{ $t('restaurantServices.mainServices.menuManagement.subtitle') }}</p>
              <ul class="list-unstyled">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.menuManagement.feature1') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.menuManagement.feature2') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.menuManagement.feature3') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.menuManagement.feature4') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.mainServices.menuManagement.feature5') }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Additional Services -->
    <section class="additional-services ptb-100" style="background: #f8f9fa;">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title">{{ $t('restaurantServices.additionalServices.title') }}</h2>
          <p class="section-subtitle">{{ $t('restaurantServices.additionalServices.subtitle') }}</p>
        </div>

        <div class="row g-4">
          <!-- User Management -->
          <div class="col-md-6 col-lg-4">
            <div class="service-card p-4 h-100 text-center" style="background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
              <div class="service-icon mx-auto mb-3" style="width: 80px; height: 80px; background: #c62828; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-users text-white fa-2x"></i>
              </div>
              <h4 class="mb-3" style="color: #1a2734;">{{ $t('restaurantServices.additionalServices.userManagement.title') }}</h4>
              <p class="mb-3" style="color: #666;">{{ $t('restaurantServices.additionalServices.userManagement.subtitle') }}</p>
              <ul class="list-unstyled text-start">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.userManagement.feature1') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.userManagement.feature2') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.userManagement.feature3') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.userManagement.feature4') }}</li>
              </ul>
            </div>
          </div>

          <!-- Inventory Management -->
          <div class="col-md-6 col-lg-4">
            <div class="service-card p-4 h-100 text-center" style="background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
              <div class="service-icon mx-auto mb-3" style="width: 80px; height: 80px; background: #c62828; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-boxes text-white fa-2x"></i>
              </div>
              <h4 class="mb-3" style="color: #1a2734;">{{ $t('restaurantServices.additionalServices.inventoryManagement.title') }}</h4>
              <p class="mb-3" style="color: #666;">{{ $t('restaurantServices.additionalServices.inventoryManagement.subtitle') }}</p>
              <ul class="list-unstyled text-start">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.inventoryManagement.feature1') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.inventoryManagement.feature2') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.inventoryManagement.feature3') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.inventoryManagement.feature4') }}</li>
              </ul>
            </div>
          </div>

          <!-- Analytics & Reports -->
          <div class="col-md-6 col-lg-4">
            <div class="service-card p-4 h-100 text-center" style="background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
              <div class="service-icon mx-auto mb-3" style="width: 80px; height: 80px; background: #c62828; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-chart-bar text-white fa-2x"></i>
              </div>
              <h4 class="mb-3" style="color: #1a2734;">{{ $t('restaurantServices.additionalServices.analytics.title') }}</h4>
              <p class="mb-3" style="color: #666;">{{ $t('restaurantServices.additionalServices.analytics.subtitle') }}</p>
              <ul class="list-unstyled text-start">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.analytics.feature1') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.analytics.feature2') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.analytics.feature3') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.analytics.feature4') }}</li>
              </ul>
            </div>
          </div>

          <!-- Payment Processing -->
          <div class="col-md-6 col-lg-4">
            <div class="service-card p-4 h-100 text-center" style="background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
              <div class="service-icon mx-auto mb-3" style="width: 80px; height: 80px; background: #c62828; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-credit-card text-white fa-2x"></i>
              </div>
              <h4 class="mb-3" style="color: #1a2734;">{{ $t('restaurantServices.additionalServices.paymentProcessing.title') }}</h4>
              <p class="mb-3" style="color: #666;">{{ $t('restaurantServices.additionalServices.paymentProcessing.subtitle') }}</p>
              <ul class="list-unstyled text-start">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.paymentProcessing.feature1') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.paymentProcessing.feature2') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.paymentProcessing.feature3') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.paymentProcessing.feature4') }}</li>
              </ul>
            </div>
          </div>

          <!-- Customer Management -->
          <div class="col-md-6 col-lg-4">
            <div class="service-card p-4 h-100 text-center" style="background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
              <div class="service-icon mx-auto mb-3" style="width: 80px; height: 80px; background: #c62828; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-friends text-white fa-2x"></i>
              </div>
              <h4 class="mb-3" style="color: #1a2734;">{{ $t('restaurantServices.additionalServices.customerManagement.title') }}</h4>
              <p class="mb-3" style="color: #666;">{{ $t('restaurantServices.additionalServices.customerManagement.subtitle') }}</p>
              <ul class="list-unstyled text-start">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.customerManagement.feature1') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.customerManagement.feature2') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.customerManagement.feature3') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.customerManagement.feature4') }}</li>
              </ul>
            </div>
          </div>

          <!-- Mobile App -->
          <div class="col-md-6 col-lg-4">
            <div class="service-card p-4 h-100 text-center" style="background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
              <div class="service-icon mx-auto mb-3" style="width: 80px; height: 80px; background: #c62828; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-mobile-alt text-white fa-2x"></i>
              </div>
              <h4 class="mb-3" style="color: #1a2734;">{{ $t('restaurantServices.additionalServices.mobileApp.title') }}</h4>
              <p class="mb-3" style="color: #666;">{{ $t('restaurantServices.additionalServices.mobileApp.subtitle') }}</p>
              <ul class="list-unstyled text-start">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.mobileApp.feature1') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.mobileApp.feature2') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.mobileApp.feature3') }}</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('restaurantServices.additionalServices.mobileApp.feature4') }}</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Information -->
    <!-- <section class="pricing-info ptb-100">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <h2 class="section-title mb-4">{{ $t('restaurantServices.pricing.title') }}</h2>
            <p class="mb-4" style="font-size: 1.1rem; color: #666;">{{ $t('restaurantServices.pricing.subtitle') }}</p>
            <ul class="list-unstyled">
              <li class="mb-3 d-flex align-items-center">
                <i class="fas fa-check-circle text-success me-3" style="font-size: 1.2rem;"></i>
                <span>{{ $t('restaurantServices.pricing.feature1') }}</span>
              </li>
              <li class="mb-3 d-flex align-items-center">
                <i class="fas fa-check-circle text-success me-3" style="font-size: 1.2rem;"></i>
                <span>{{ $t('restaurantServices.pricing.feature2') }}</span>
              </li>
              <li class="mb-3 d-flex align-items-center">
                <i class="fas fa-check-circle text-success me-3" style="font-size: 1.2rem;"></i>
                <span>{{ $t('restaurantServices.pricing.feature3') }}</span>
              </li>
              <li class="mb-3 d-flex align-items-center">
                <i class="fas fa-check-circle text-success me-3" style="font-size: 1.2rem;"></i>
                <span>{{ $t('restaurantServices.pricing.feature4') }}</span>
              </li>
            </ul>
            <router-link to="/pricing" class="btn btn-danger btn-lg px-4 py-2">{{ $t('restaurantServices.pricing.viewPricingPlans') }}</router-link>
          </div>
          <div class="col-lg-6">
            <div class="text-center">
              <img src="/Mox_files/pricing-illustration.jpg" alt="Pricing" class="img-fluid rounded shadow" style="max-width: 100%;">
            </div>
          </div>
        </div>
      </div>
    </section> -->

    <!-- Call to Action -->
    <section class="cta-section ptb-100" style="background: #c62828; color: white;">
      <div class="container text-center">
        <h2 class="mb-4">{{ $t('restaurantServices.cta.title') }}</h2>
        <p class="mb-4" style="font-size: 1.2rem;">{{ $t('restaurantServices.cta.subtitle') }}</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <router-link to="/tenant-register" class="btn btn-light btn-lg px-4 py-2">{{ $t('restaurantServices.cta.startFreeTrial') }}</router-link>
          <a href="/contact" class="btn btn-outline-light btn-lg px-4 py-2">{{ $t('restaurantServices.cta.contactSales') }}</a>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'RestaurantServices'
}
</script>

<style scoped>
.restaurant-services-page {
  overflow-x: hidden;
}

.hero-section {
  padding-top: 120px;
  padding-bottom: 80px;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: bold;
  line-height: 1.2;
}

.hero-text {
  font-size: 1.3rem;
  line-height: 1.6;
}

.section-title {
  font-size: 2.8rem;
  font-weight: bold;
  color: #1a2734;
  margin-bottom: 1rem;
}

.section-subtitle {
  font-size: 1.2rem;
  color: #666;
  margin-bottom: 3rem;
}

.service-card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.service-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

.service-icon {
  transition: transform 0.3s ease;
}

.service-card:hover .service-icon {
  transform: scale(1.1);
}

/* Responsive Design */
@media (max-width: 991.98px) {
  .hero-title {
    font-size: 2.5rem;
  }

  .section-title {
    font-size: 2.2rem;
  }

  .hero-text {
    font-size: 1.1rem;
  }
}

@media (max-width: 768px) {
  .hero-section {
    padding-top: 100px;
    padding-bottom: 60px;
  }

  .hero-title {
    font-size: 2rem;
  }

  .section-title {
    font-size: 1.8rem;
  }

  .cta-section .btn {
    width: 100%;
    margin-bottom: 1rem;
  }
}

@media (max-width: 576px) {
  .hero-title {
    font-size: 1.8rem;
  }

  .section-title {
    font-size: 1.5rem;
  }

  .hero-text {
    font-size: 1rem;
  }
}
</style>
