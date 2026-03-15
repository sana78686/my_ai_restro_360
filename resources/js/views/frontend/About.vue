<template>
  <div class="about-page">
    <div class="container py-5">
      <div class="row">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <h1 class="mb-4">About Us</h1>
          <p class="lead mb-4">
            We are dedicated to revolutionizing the restaurant industry through innovative digital solutions.
          </p>
          <p class="mb-4">
            Our platform provides comprehensive restaurant management tools that help businesses streamline their operations,
            increase efficiency, and deliver exceptional customer experiences. From order management to analytics,
            we offer everything you need to succeed in today's competitive market.
          </p>
          <div class="stats row g-4 mb-4">
            <div class="col-6">
              <div class="stat-card p-3 bg-light rounded">
                <h3 class="mb-2">1000+</h3>
                <p class="mb-0">Restaurants Served</p>
              </div>
            </div>
            <div class="col-6">
              <div class="stat-card p-3 bg-light rounded">
                <h3 class="mb-2">98%</h3>
                <p class="mb-0">Customer Satisfaction</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <img src="/images/about-restaurant.jpg" alt="Restaurant Management" class="img-fluid rounded shadow">
        </div>
      </div>

      <div class="row mt-5">
        <div class="col-12">
          <h2 class="text-center mb-5">Why Choose Us</h2>
        </div>
        <div class="col-md-4 mb-4">
          <div class="feature text-center">
            <i class="fas fa-rocket mb-3"></i>
            <h4>Innovation</h4>
            <p>Cutting-edge technology to keep you ahead of the competition</p>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="feature text-center">
            <i class="fas fa-headset mb-3"></i>
            <h4>Support</h4>
            <p>24/7 dedicated support to help you succeed</p>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="feature text-center">
            <i class="fas fa-shield-alt mb-3"></i>
            <h4>Reliability</h4>
            <p>Secure and stable platform you can count on</p>
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
  background-color: #f8f9fa;
}

h1 {
  color: #333;
  font-weight: 600;
}

.lead {
  color: #666;
}

.stat-card {
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.stat-card h3 {
  color: #d4af37;
  font-weight: 600;
}

.feature i {
  font-size: 2.5rem;
  color: #d4af37;
}

.feature h4 {
  color: #333;
  margin-bottom: 1rem;
}

.feature p {
  color: #666;
}
</style> 