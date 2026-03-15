<template>
  <div class="privacy-page mt-100 mb-100">
    <div class="container py-5">
      <h1 class="mb-4">{{ $t('privacy.title') }}</h1>
      <p class="lead mb-5">{{ $t('privacy.lastUpdated') }}</p>

      <div class="policy-section mb-5">
        <h2 class="mb-4">{{ $t('privacy.section1Title') }}</h2>
        <p>{{ $t('privacy.section1Intro') }}</p>
        <ul>
          <li>{{ $t('privacy.section1Item1') }}</li>
          <li>{{ $t('privacy.section1Item2') }}</li>
          <li>{{ $t('privacy.section1Item3') }}</li>
          <li>{{ $t('privacy.section1Item4') }}</li>
        </ul>
      </div>

      <div class="policy-section mb-5">
        <h2 class="mb-4">{{ $t('privacy.section2Title') }}</h2>
        <p>{{ $t('privacy.section2Intro') }}</p>
        <ul>
          <li>{{ $t('privacy.section2Item1') }}</li>
          <li>{{ $t('privacy.section2Item2') }}</li>
          <li>{{ $t('privacy.section2Item3') }}</li>
          <li>{{ $t('privacy.section2Item4') }}</li>
          <li>{{ $t('privacy.section2Item5') }}</li>
        </ul>
      </div>

      <div class="policy-section mb-5">
        <h2 class="mb-4">{{ $t('privacy.section3Title') }}</h2>
        <p>{{ $t('privacy.section3Intro') }}</p>
        <ul>
          <li>{{ $t('privacy.section3Item1') }}</li>
          <li>{{ $t('privacy.section3Item2') }}</li>
          <li>{{ $t('privacy.section3Item3') }}</li>
        </ul>
      </div>

      <div class="policy-section mb-5">
        <h2 class="mb-4">{{ $t('privacy.section4Title') }}</h2>
        <p>{{ $t('privacy.section4Intro') }}</p>
        <ul>
          <li>{{ $t('privacy.section4Item1') }}</li>
          <li>{{ $t('privacy.section4Item2') }}</li>
          <li>{{ $t('privacy.section4Item3') }}</li>
          <li>{{ $t('privacy.section4Item4') }}</li>
        </ul>
      </div>

      <div class="policy-section mb-5">
        <h2 class="mb-4">{{ $t('privacy.section5Title') }}</h2>
        <p>{{ $t('privacy.section5Intro') }}</p>
        <ul>
          <li>{{ $t('privacy.section5Item1') }}</li>
          <li>{{ $t('privacy.section5Item2') }}</li>
          <li>{{ $t('privacy.section5Item3') }}</li>
          <li>{{ $t('privacy.section5Item4') }}</li>
        </ul>
      </div>

      <div class="policy-section">
        <h2 class="mb-4">{{ $t('privacy.section6Title') }}</h2>
        <p>{{ $t('privacy.section6Intro') }}</p>
        <ul>
          <li>{{ $t('privacy.email') }}: {{ contactEmail }}</li>
          <li>{{ $t('privacy.phone') }}: {{ contactPhone }}</li>
          <li>{{ $t('privacy.address') }}: {{ contactAddress }}</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { contactEmail, contactPhone, contactAddress } from '../../utils/contact.js'
</script>

<style scoped>
.privacy-page {
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

.policy-section {
  margin-bottom: 3rem;
}

.policy-section h2 {
  color: #333;
  font-size: 1.5rem;
  font-weight: 600;
  margin-bottom: 1rem;
}

.policy-section p {
  color: #666;
  line-height: 1.6;
  margin-bottom: 1rem;
}

.policy-section ul {
  color: #666;
  padding-left: 1.5rem;
  margin-bottom: 1rem;
}

.policy-section li {
  margin-bottom: 0.5rem;
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

  .policy-section h2 {
    font-size: 1.3rem;
  }
}

@media (max-width: 767.98px) {
  .privacy-page {
    padding-top: 2rem;
    padding-bottom: 2rem;
  }

  h1 {
    font-size: 1.75rem;
    margin-bottom: 1.5rem;
  }

  .lead {
    font-size: 0.95rem;
    margin-bottom: 2rem;
  }

  .policy-section {
    margin-bottom: 2rem;
  }

  .policy-section h2 {
    font-size: 1.2rem;
  }

  .policy-section p {
    font-size: 0.95rem;
  }

  .policy-section ul {
    padding-left: 1.25rem;
  }

  .policy-section li {
    font-size: 0.9rem;
  }
}

/* Android-specific fixes */
@media screen and (-webkit-min-device-pixel-ratio: 0) {
  .privacy-page {
    -webkit-text-size-adjust: 100%;
    -webkit-font-smoothing: antialiased;
  }

  .policy-section {
    -webkit-hyphens: auto;
    hyphens: auto;
    word-wrap: break-word;
    overflow-wrap: break-word;
  }
}

@media (max-width: 575.98px) {
  h1 {
    font-size: 1.5rem;
  }

  .lead {
    font-size: 0.9rem;
  }

  .policy-section h2 {
    font-size: 1.1rem;
  }

  .policy-section p,
  .policy-section li {
    font-size: 0.85rem;
  }
}
</style>
