<template>
  <div class="terms-page">
    <div class="container py-5">
      <h1 class="mb-4">Terms of Service</h1>
      <p class="lead mb-5">Last updated: March 2024</p>

      <div class="terms-section mb-5">
        <h2 class="mb-4">1. Acceptance of Terms</h2>
        <p>By accessing and using our services, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our services.</p>
      </div>

      <div class="terms-section mb-5">
        <h2 class="mb-4">2. Service Description</h2>
        <p>We provide restaurant management software services, including but not limited to:</p>
        <ul>
          <li>Order management system</li>
          <li>Menu management tools</li>
          <li>Customer relationship management</li>
          <li>Analytics and reporting</li>
          <li>Payment processing integration</li>
        </ul>
      </div>

      <div class="terms-section mb-5">
        <h2 class="mb-4">3. User Responsibilities</h2>
        <p>As a user of our services, you agree to:</p>
        <ul>
          <li>Provide accurate and complete information</li>
          <li>Maintain the security of your account credentials</li>
          <li>Comply with all applicable laws and regulations</li>
          <li>Use the services only for their intended purpose</li>
          <li>Not engage in any unauthorized or harmful activities</li>
        </ul>
      </div>

      <div class="terms-section mb-5">
        <h2 class="mb-4">4. Subscription and Payments</h2>
        <p>Our service is provided on a subscription basis:</p>
        <ul>
          <li>Subscription fees are billed monthly or annually</li>
          <li>Payments are non-refundable</li>
          <li>We reserve the right to modify pricing with notice</li>
          <li>Failure to pay may result in service suspension</li>
        </ul>
      </div>

      <div class="terms-section mb-5">
        <h2 class="mb-4">5. Intellectual Property</h2>
        <p>All content and materials available through our services are protected by intellectual property rights. You may not:</p>
        <ul>
          <li>Copy or reproduce any part of our services</li>
          <li>Modify or create derivative works</li>
          <li>Distribute or publicly display content</li>
          <li>Reverse engineer or decompile the software</li>
        </ul>
      </div>

      <div class="terms-section mb-5">
        <h2 class="mb-4">6. Limitation of Liability</h2>
        <p>We strive to provide reliable services but cannot guarantee uninterrupted operation. We are not liable for:</p>
        <ul>
          <li>Service interruptions or downtime</li>
          <li>Data loss or corruption</li>
          <li>Indirect or consequential damages</li>
          <li>Third-party actions or content</li>
        </ul>
      </div>

      <div class="terms-section">
        <h2 class="mb-4">7. Contact Information</h2>
        <p>For questions about these Terms of Service, please contact us at:</p>
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
.terms-page {
  background-color: #f8f9fa;
}

h1 {
  color: #333;
  font-weight: 600;
}

.lead {
  color: #666;
}

.terms-section h2 {
  color: #333;
  font-size: 1.5rem;
  font-weight: 600;
}

.terms-section p {
  color: #666;
  line-height: 1.6;
}

.terms-section ul {
  color: #666;
  padding-left: 1.5rem;
}

.terms-section li {
  margin-bottom: 0.5rem;
}
</style>
