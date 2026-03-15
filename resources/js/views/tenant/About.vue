<template>
  <div class="about-page mt-100 mb-100">
    <div class="container py-5">
      <div class="row">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <h1 class="mb-4">{{ $t('about.title') }}</h1>
          <p class="lead mb-4">
            {{ $t('about.subtitle') }}
          </p>
          <p class="mb-4">
            {{ $t('about.description') }}
          </p>
          <div class="stats row g-4 mb-4">
            <div class="col-6 col-md-6">
              <div class="stat-card p-3 bg-light rounded">
                <h3 class="mb-2">{{ $t('about.stat1.value') }}</h3>
                <p class="mb-0">{{ $t('about.stat1.label') }}</p>
              </div>
            </div>
            <div class="col-6 col-md-6">
              <div class="stat-card p-3 bg-light rounded">
                <h3 class="mb-2">{{ $t('about.stat2.value') }}</h3>
                <p class="mb-0">{{ $t('about.stat2.label') }}</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <img src="/mox_files/medium(2).jpg" alt="Restaurant Management" class="img-fluid rounded shadow" style="width: 100%; height: auto;">
        </div>
      </div>

      <div class="row mt-5">
        <div class="col-12">
          <h2 class="text-center mb-5">{{ $t('about.whyChoose') }}</h2>
        </div>
        <div class="col-md-4 mb-4">
          <div class="feature text-center">
            <i class="fas fa-rocket mb-3"></i>
            <h4>{{ $t('about.features.innovation.title') }}</h4>
            <p>{{ $t('about.features.innovation.desc') }}</p>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="feature text-center">
            <i class="fas fa-headset mb-3"></i>
            <h4>{{ $t('about.features.support.title') }}</h4>
            <p>{{ $t('about.features.support.desc') }}</p>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="feature text-center">
            <i class="fas fa-shield-alt mb-3"></i>
            <h4>{{ $t('about.features.reliability.title') }}</h4>
            <p>{{ $t('about.features.reliability.desc') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>

<style scoped>
.about-page {
  min-height: 60vh;
}

h1 {
  color: #333;
  font-weight: 600;
  font-size: 2.5rem;
}

.lead {
  color: #666;
  font-size: 1.1rem;
}

.stat-card {
  transition: transform 0.3s ease;
  height: 100%;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.stat-card h3 {
  color: #c62828;
  font-weight: 600;
  font-size: 2rem;
}

.stat-card p {
  margin: 0;
  font-size: 0.95rem;
}

.feature {
  padding: 1.5rem;
}

.feature i {
  font-size: 2.5rem;
  color: #c62828;
  margin-bottom: 1rem;
}

.feature h4 {
  color: #333;
  margin-bottom: 1rem;
  font-size: 1.3rem;
}

.feature p {
  color: #666;
  line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 991.98px) {
  h1 {
    font-size: 2rem;
  }

  .lead {
    font-size: 1rem;
  }

  .stat-card h3 {
    font-size: 1.75rem;
  }

  .feature i {
    font-size: 2rem;
  }

  .feature h4 {
    font-size: 1.2rem;
  }
}

@media (max-width: 767.98px) {
  .about-page {
    padding-top: 2rem;
    padding-bottom: 2rem;
  }

  h1 {
    font-size: 1.75rem;
    margin-bottom: 1.5rem;
  }

  .lead {
    font-size: 0.95rem;
    margin-bottom: 1.5rem;
  }

  .stats {
    margin-bottom: 2rem;
  }

  .stat-card {
    margin-bottom: 1rem;
  }

  .stat-card h3 {
    font-size: 1.5rem;
  }

  .stat-card p {
    font-size: 0.9rem;
  }

  .feature {
    padding: 1rem;
    margin-bottom: 2rem;
  }

  .feature i {
    font-size: 1.75rem;
  }

  .feature h4 {
    font-size: 1.1rem;
  }

  .feature p {
    font-size: 0.9rem;
  }
}

/* Android-specific fixes */
@media screen and (-webkit-min-device-pixel-ratio: 0) {
  .about-page {
    -webkit-text-size-adjust: 100%;
    -webkit-font-smoothing: antialiased;
  }

  .stat-card,
  .feature {
    -webkit-transform: translateZ(0);
    transform: translateZ(0);
  }

  img {
    -webkit-user-select: none;
    user-select: none;
    max-width: 100%;
    height: auto;
  }
}

@media (max-width: 575.98px) {
  h1 {
    font-size: 1.5rem;
  }

  .lead {
    font-size: 0.9rem;
  }

  .stat-card {
    padding: 1rem !important;
  }

  .stat-card h3 {
    font-size: 1.25rem;
  }

  .feature {
    padding: 0.75rem;
  }

  .feature i {
    font-size: 1.5rem;
  }
}
</style>
