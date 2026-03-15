<template>
  <div class="privacy-page">
    <div class="container py-5">
      <h1 class="mb-4">Privacy Policy</h1>
      <p class="lead mb-5">Last updated: March 2024</p>

      <div class="policy-section mb-5">
        <h2 class="mb-4">1. Information We Collect</h2>
        <p>We collect information that you provide directly to us, including:</p>
        <ul>
          <li>Name and contact information</li>
          <li>Restaurant details and business information</li>
          <li>Payment information</li>
          <li>Usage data and preferences</li>
        </ul>
      </div>

      <div class="policy-section mb-5">
        <h2 class="mb-4">2. How We Use Your Information</h2>
        <p>We use the information we collect to:</p>
        <ul>
          <li>Provide and maintain our services</li>
          <li>Process your transactions</li>
          <li>Send you important updates and notifications</li>
          <li>Improve our services and develop new features</li>
          <li>Protect against fraud and unauthorized access</li>
        </ul>
      </div>

      <div class="policy-section mb-5">
        <h2 class="mb-4">3. Information Sharing</h2>
        <p>We do not sell your personal information. We may share your information with:</p>
        <ul>
          <li>Service providers who assist in our operations</li>
          <li>Professional advisors and consultants</li>
          <li>Law enforcement when required by law</li>
        </ul>
      </div>

      <div class="policy-section mb-5">
        <h2 class="mb-4">4. Data Security</h2>
        <p>We implement appropriate technical and organizational measures to protect your personal information, including:</p>
        <ul>
          <li>Encryption of sensitive data</li>
          <li>Regular security assessments</li>
          <li>Access controls and authentication</li>
          <li>Secure data storage and transmission</li>
        </ul>
      </div>

      <div class="policy-section mb-5">
        <h2 class="mb-4">5. Your Rights</h2>
        <p>You have the right to:</p>
        <ul>
          <li>Access your personal information</li>
          <li>Correct inaccurate information</li>
          <li>Request deletion of your information</li>
          <li>Opt-out of marketing communications</li>
        </ul>
      </div>

      <div class="policy-section">
        <h2 class="mb-4">6. Contact Us</h2>
        <p>If you have any questions about this Privacy Policy, please contact us at:</p>
        <ul>
          <li>Email: {{ contactEmail }}</li>
          <li>Phone: {{ contactPhone }}</li>
          <li>Address: {{ contactAddress }}</li>
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
  background-color: #f8f9fa;
}

h1 {
  color: #333;
  font-weight: 600;
}

.lead {
  color: #666;
}

.policy-section h2 {
  color: #333;
  font-size: 1.5rem;
  font-weight: 600;
}

.policy-section p {
  color: #666;
  line-height: 1.6;
}

.policy-section ul {
  color: #666;
  padding-left: 1.5rem;
}

.policy-section li {
  margin-bottom: 0.5rem;
}
</style>
