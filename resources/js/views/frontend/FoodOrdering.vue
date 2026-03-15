<template>
  <div class="food-ordering-page mt-100">
    <!-- Hero Section -->
    <section class="hero-section  ptb-100" style="background: linear-gradient(135deg, #c62828 0%, #d32f2f 100%); color: white;">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <h1 class="hero-title mb-4">{{ $t('foodOrdering.hero.title') }}</h1>
            <p class="hero-text mb-4">{{ $t('foodOrdering.hero.subtitle') }}</p>
            <div class="d-flex gap-3 flex-wrap">
              <router-link to="/tenant-register" class="btn btn-light btn-lg px-4 py-2">{{ $t('foodOrdering.hero.startFreeTrial') }}</router-link>
              <a href="#features" class="btn btn-outline-light btn-lg px-4 py-2">{{ $t('foodOrdering.hero.learnMore') }}</a>
            </div>
          </div>
          <div class="col-lg-6 text-center">
            <img src="/Mox_files/food-ordering.jpg" alt="Restaurant Management" class="hero-image img-fluid rounded shadow-lg">
          </div>
        </div>
      </div>
    </section>

    <!-- Features Overview -->
    <section id="features" class="features-overview ptb-100">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title">{{ $t('foodOrdering.featuresOverview.title') }}</h2>
          <p class="section-subtitle">{{ $t('foodOrdering.featuresOverview.subtitle') }}</p>
        </div>
        <div class="row g-4">
          <div class="col-md-6 col-lg-3">
            <div class="feature-card text-center p-4 h-100">
              <div class="feature-icon mb-3">
                <i class="fas fa-shopping-cart fa-3x text-danger"></i>
              </div>
              <h4>{{ $t('foodOrdering.featuresOverview.onlineOrdering.title') }}</h4>
              <p>{{ $t('foodOrdering.featuresOverview.onlineOrdering.description') }}</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="feature-card text-center p-4 h-100">
              <div class="feature-icon mb-3">
                <i class="fas fa-calendar-check fa-3x text-danger"></i>
              </div>
              <h4>{{ $t('foodOrdering.featuresOverview.tableReservations.title') }}</h4>
              <p>{{ $t('foodOrdering.featuresOverview.tableReservations.description') }}</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="feature-card text-center p-4 h-100">
              <div class="feature-icon mb-3">
                <i class="fas fa-list-alt fa-3x text-danger"></i>
              </div>
              <h4>{{ $t('foodOrdering.featuresOverview.menuManagement.title') }}</h4>
              <p>{{ $t('foodOrdering.featuresOverview.menuManagement.description') }}</p>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="feature-card text-center p-4 h-100">
              <div class="feature-icon mb-3">
                <i class="fas fa-users fa-3x text-danger"></i>
              </div>
              <h4>{{ $t('foodOrdering.featuresOverview.userManagement.title') }}</h4>
              <p>{{ $t('foodOrdering.featuresOverview.userManagement.description') }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Online Ordering System -->
    <section class="ordering-system ptb-100" style="background: #f8f9fa;">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <h2 class="section-title mb-4">{{ $t('foodOrdering.onlineOrdering.title') }}</h2>
            <p class="mb-4">{{ $t('foodOrdering.onlineOrdering.subtitle') }}</p>
            <div class="feature-list">
              <div class="feature-item d-flex align-items-center mb-3">
                <i class="fas fa-check-circle text-success me-3"></i>
                <span>{{ $t('foodOrdering.onlineOrdering.feature1') }}</span>
              </div>
              <div class="feature-item d-flex align-items-center mb-3">
                <i class="fas fa-check-circle text-success me-3"></i>
                <span>{{ $t('foodOrdering.onlineOrdering.feature2') }}</span>
              </div>
              <div class="feature-item d-flex align-items-center mb-3">
                <i class="fas fa-check-circle text-success me-3"></i>
                <span>{{ $t('foodOrdering.onlineOrdering.feature3') }}</span>
              </div>
              <div class="feature-item d-flex align-items-center mb-3">
                <i class="fas fa-check-circle text-success me-3"></i>
                <span>{{ $t('foodOrdering.onlineOrdering.feature4') }}</span>
              </div>
              <div class="feature-item d-flex align-items-center mb-3">
                <i class="fas fa-check-circle text-success me-3"></i>
                <span>{{ $t('foodOrdering.onlineOrdering.feature5') }}</span>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="text-center">
              <img src="/Mox_files/online_order.jpg" alt="Online Ordering" class="img-fluid rounded shadow">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Table Reservation System -->
    <section class="reservation-system ptb-100">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6 order-lg-2">
            <h2 class="section-title mb-4">{{ $t('foodOrdering.tableReservation.title') }}</h2>
            <p class="mb-4">{{ $t('foodOrdering.tableReservation.subtitle') }}</p>
            <div class="feature-list">
              <div class="feature-item d-flex align-items-center mb-3">
                <i class="fas fa-check-circle text-success me-3"></i>
                <span>{{ $t('foodOrdering.tableReservation.feature1') }}</span>
              </div>
              <div class="feature-item d-flex align-items-center mb-3">
                <i class="fas fa-check-circle text-success me-3"></i>
                <span>{{ $t('foodOrdering.tableReservation.feature2') }}</span>
              </div>
              <div class="feature-item d-flex align-items-center mb-3">
                <i class="fas fa-check-circle text-success me-3"></i>
                <span>{{ $t('foodOrdering.tableReservation.feature3') }}</span>
              </div>
              <div class="feature-item d-flex align-items-center mb-3">
                <i class="fas fa-check-circle text-success me-3"></i>
                <span>{{ $t('foodOrdering.tableReservation.feature4') }}</span>
              </div>
              <div class="feature-item d-flex align-items-center mb-3">
                <i class="fas fa-check-circle text-success me-3"></i>
                <span>{{ $t('foodOrdering.tableReservation.feature5') }}</span>
              </div>
            </div>
          </div>
          <div class="col-lg-6 order-lg-1">
            <div class="text-center">
              <img src="/Mox_files/table-reservation.webp" alt="Table Reservation" class="img-fluid rounded shadow">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Menu Management System -->
    <section class="menu-management ptb-100" style="background: #f8f9fa;">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title">{{ $t('foodOrdering.menuManagement.title') }}</h2>
          <p class="section-subtitle">{{ $t('foodOrdering.menuManagement.subtitle') }}</p>
        </div>
        <div class="row g-4">
          <div class="col-md-6">
            <div class="menu-feature-card p-4 h-100">
                             <h4 class="mb-3"><i class="fas fa-layer-group text-danger me-2"></i>{{ $t('foodOrdering.menuManagement.categoryManagement.title') }}</h4>
               <ul class="list-unstyled">
                 <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('foodOrdering.menuManagement.categoryManagement.feature1') }}</li>
                 <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('foodOrdering.menuManagement.categoryManagement.feature2') }}</li>
                 <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('foodOrdering.menuManagement.categoryManagement.feature3') }}</li>
                 <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('foodOrdering.menuManagement.categoryManagement.feature4') }}</li>
               </ul>
            </div>
          </div>
          <div class="col-md-6">
            <div class="menu-feature-card p-4 h-100">
                             <h4 class="mb-3"><i class="fas fa-utensils text-danger me-2"></i>{{ $t('foodOrdering.menuManagement.itemManagement.title') }}</h4>
               <ul class="list-unstyled">
                 <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('foodOrdering.menuManagement.itemManagement.feature1') }}</li>
                 <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('foodOrdering.menuManagement.itemManagement.feature2') }}</li>
                 <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('foodOrdering.menuManagement.itemManagement.feature3') }}</li>
                 <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('foodOrdering.menuManagement.itemManagement.feature4') }}</li>
                 <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('foodOrdering.menuManagement.itemManagement.feature5') }}</li>
               </ul>
            </div>
          </div>
        </div>
        <div class="text-center mt-4">
          <div class="alert alert-info d-inline-block">
            <i class="fas fa-info-circle me-2"></i>
            <strong>{{ $t('foodOrdering.menuManagement.smartControl.title') }}:</strong> {{ $t('foodOrdering.menuManagement.smartControl.description') }}
          </div>
        </div>
      </div>
    </section>

    <!-- User and Role Management -->
    <section class="user-management ptb-100">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title">{{ $t('foodOrdering.userManagement.title') }}</h2>
          <p class="section-subtitle">{{ $t('foodOrdering.userManagement.subtitle') }}</p>
        </div>
        <div class="row g-4">
          <div class="col-lg-4">
            <div class="role-card text-center p-4 h-100">
              <div class="role-icon mb-3">
                <i class="fas fa-crown fa-3x text-warning"></i>
              </div>
                             <h4>{{ $t('foodOrdering.userManagement.roles.admin.title') }}</h4>
               <p>{{ $t('foodOrdering.userManagement.roles.admin.description') }}</p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="role-card text-center p-4 h-100">
              <div class="role-icon mb-3">
                <i class="fas fa-user-tie fa-3x text-info"></i>
              </div>
                             <h4>{{ $t('foodOrdering.userManagement.roles.manager.title') }}</h4>
               <p>{{ $t('foodOrdering.userManagement.roles.manager.description') }}</p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="role-card text-center p-4 h-100">
              <div class="role-icon mb-3">
                <i class="fas fa-user fa-3x text-success"></i>
              </div>
                             <h4>{{ $t('foodOrdering.userManagement.roles.staff.title') }}</h4>
               <p>{{ $t('foodOrdering.userManagement.roles.staff.description') }}</p>
            </div>
          </div>
        </div>
        <div class="row mt-5">
          <div class="col-lg-6">
            <div class="permission-card p-4">
                             <h4 class="mb-3">{{ $t('foodOrdering.userManagement.control.title') }}</h4>
               <ul class="list-unstyled">
                 <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('foodOrdering.userManagement.control.feature1') }}</li>
                 <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('foodOrdering.userManagement.control.feature2') }}</li>
                 <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('foodOrdering.userManagement.control.feature3') }}</li>
                 <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $t('foodOrdering.userManagement.control.feature4') }}</li>
               </ul>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="permission-card p-4">
                             <h4 class="mb-3">{{ $t('foodOrdering.userManagement.security.title') }}</h4>
               <ul class="list-unstyled">
                 <li class="mb-2"><i class="fas fa-shield-alt text-success me-2"></i>{{ $t('foodOrdering.userManagement.security.feature1') }}</li>
                 <li class="mb-2"><i class="fas fa-shield-alt text-success me-2"></i>{{ $t('foodOrdering.userManagement.security.feature2') }}</li>
                 <li class="mb-2"><i class="fas fa-shield-alt text-success me-2"></i>{{ $t('foodOrdering.userManagement.security.feature3') }}</li>
                 <li class="mb-2"><i class="fas fa-shield-alt text-success me-2"></i>{{ $t('foodOrdering.userManagement.security.feature4') }}</li>
               </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works ptb-100" style="background: #f8f9fa;">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title">{{ $t('foodOrdering.howItWorks.title') }}</h2>
          <p class="section-subtitle">{{ $t('foodOrdering.howItWorks.subtitle') }}</p>
        </div>
        <div class="row g-4">
          <div class="col-md-4">
            <div class="step-card text-center p-4 h-100">
              <div class="step-number mb-3">1</div>
              <h4>{{ $t('foodOrdering.howItWorks.step1.title') }}</h4>
              <p>{{ $t('foodOrdering.howItWorks.step1.description') }}</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="step-card text-center p-4 h-100">
              <div class="step-number mb-3">2</div>
              <h4>{{ $t('foodOrdering.howItWorks.step2.title') }}</h4>
              <p>{{ $t('foodOrdering.howItWorks.step2.description') }}</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="step-card text-center p-4 h-100">
              <div class="step-number mb-3">3</div>
              <h4>{{ $t('foodOrdering.howItWorks.step3.title') }}</h4>
              <p>{{ $t('foodOrdering.howItWorks.step3.description') }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section ptb-100" style="background: #c62828; color: white;">
      <div class="container text-center">
        <h2 class="mb-4">{{ $t('foodOrdering.cta.title') }}</h2>
        <p class="mb-4">{{ $t('foodOrdering.cta.subtitle') }}</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <router-link to="/tenant-register" class="btn btn-light btn-lg px-4 py-2">{{ $t('foodOrdering.cta.startFreeTrial') }}</router-link>
          <a href="/contact" class="btn btn-outline-light btn-lg px-4 py-2">{{ $t('foodOrdering.cta.contactSales') }}</a>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'FoodOrdering'
}
</script>

<style scoped>
.food-ordering-page {
  overflow-x: hidden;
}

/* Hero Section */
.hero-section {
  min-height: 60vh;
  display: flex;
  align-items: center;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: bold;
  line-height: 1.2;
}

.hero-text {
  font-size: 1.25rem;
  opacity: 0.9;
}

.hero-image {
  max-width: 100%;
  height: auto;
}

/* Section Titles */
.section-title {
  font-size: 2.5rem;
  font-weight: bold;
  color: #1a2734;
  margin-bottom: 1rem;
}

.section-subtitle {
  font-size: 1.1rem;
  color: #666;
  margin-bottom: 2rem;
}

/* Feature Cards */
.feature-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  transition: transform 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-5px);
}

.feature-icon {
  color: #c62828;
}

/* Menu Management */
.menu-feature-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  border-left: 4px solid #c62828;
}

/* Role Cards */
.role-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  transition: transform 0.3s ease;
}

.role-card:hover {
  transform: translateY(-5px);
}

.role-icon {
  color: #c62828;
}

/* Permission Cards */
.permission-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  height: 100%;
}

/* Step Cards */
.step-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  transition: transform 0.3s ease;
}

.step-card:hover {
  transform: translateY(-5px);
}

.step-number {
  width: 60px;
  height: 60px;
  background: #c62828;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  font-weight: bold;
  margin: 0 auto;
}

/* Feature Lists */
.feature-list {
  margin-top: 1rem;
}

.feature-item {
  font-size: 1rem;
}

/* Responsive Design */
@media (max-width: 991.98px) {
  .hero-title {
    font-size: 2.5rem;
  }

  .section-title {
    font-size: 2rem;
  }

  .hero-text {
    font-size: 1.1rem;
  }
}

@media (max-width: 768px) {
  .hero-section {
    min-height: 50vh;
    padding: 3rem 0;
  }

  .hero-title {
    font-size: 2rem;
  }

  .section-title {
    font-size: 1.8rem;
  }

  .hero-text {
    font-size: 1rem;
  }

  .cta-section .btn {
    width: 100%;
    margin-bottom: 1rem;
  }
}

@media (max-width: 576px) {
  .hero-title {
    font-size: 1.5rem;
  }

  .section-title {
    font-size: 1.5rem;
  }

  .hero-text {
    font-size: 0.9rem;
  }

  .feature-card,
  .menu-feature-card,
  .role-card,
  .permission-card,
  .step-card {
    padding: 1.5rem;
  }

  .step-number {
    width: 50px;
    height: 50px;
    font-size: 1.2rem;
  }
}
</style>
