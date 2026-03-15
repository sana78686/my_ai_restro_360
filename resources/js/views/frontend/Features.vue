<template>
  <div class="features-page">
    <div class="container">
      <h1 class="text-center mb-5">Our Features</h1>
      
      <div class="row g-4">
        <div class="col-md-4" v-for="(feature, index) in features" :key="index">
          <div class="feature-card">
            <div class="icon-wrapper">
              <i :class="feature.icon"></i>
            </div>
            <h3>{{ feature.title }}</h3>
            <p>{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const features = [
  {
    icon: 'fas fa-utensils',
    title: 'Menu Management',
    description: 'Easily manage your restaurant menu with our intuitive interface. Add, edit, and organize items with just a few clicks.'
  },
  {
    icon: 'fas fa-mobile-alt',
    title: 'Mobile Ordering',
    description: 'Allow customers to order directly from their mobile devices. Increase efficiency and reduce wait times.'
  },
  {
    icon: 'fas fa-chart-line',
    title: 'Analytics',
    description: 'Get detailed insights into your business performance with comprehensive analytics and reporting tools.'
  },
  {
    icon: 'fas fa-users',
    title: 'Customer Management',
    description: 'Keep track of your customers, their preferences, and order history to provide personalized service.'
  },
  {
    icon: 'fas fa-clock',
    title: 'Reservation System',
    description: 'Streamline your reservation process with our automated booking system. Manage tables efficiently.'
  },
  {
    icon: 'fas fa-truck',
    title: 'Delivery Integration',
    description: 'Integrate with popular delivery services or manage your own delivery fleet with our delivery management system.'
  }
]
</script>

<style scoped>
.features-page {
  padding: 4rem 0;
  background-color: #f8f9fa;
}

.feature-card {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
  text-align: center;
  transition: transform 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-5px);
}

.icon-wrapper {
  width: 70px;
  height: 70px;
  margin: 0 auto 1.5rem;
  background: #d4af37;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-wrapper i {
  font-size: 30px;
  color: white;
}

h3 {
  margin-bottom: 1rem;
  color: #333;
  font-size: 1.5rem;
}

p {
  color: #666;
  margin-bottom: 0;
}
</style> 