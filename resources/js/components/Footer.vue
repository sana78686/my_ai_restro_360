<template>
  <footer class="footer">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <h5>{{ $t('footer.about.title') }}</h5>
          <p>{{ $t('footer.about.description') }}</p>
        </div>
        <div class="col-md-4">
          <h5>{{ $t('footer.contact.title') }}</h5>
          <ul class="contact-info">
            <li><i class="fas fa-map-marker-alt"></i> {{ $t('footer.contact.address') }}</li>
            <li><i class="fas fa-phone"></i> {{ $t('footer.contact.phone') }}</li>
            <li><i class="fas fa-envelope"></i> {{ $t('footer.contact.email') }}</li>
          </ul>
        </div>
        <div class="col-md-4">
          <h5>{{ $t('footer.quickLinks.quickLinks') }}</h5>
          <ul class="quick-links">
            <li><a href="#">{{ $t('footer.quickLinks.features') }}</a></li>
            <li><a href="#">{{ $t('footer.quickLinks.pricing') }}</a></li>
            <li><a href="#">{{ $t('footer.quickLinks.contact') }}</a></li>
            <li><a href="#">{{ $t('footer.quickLinks.privacy') }}</a></li>
            <li><a href="#">{{ $t('footer.quickLinks.terms') }}</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p>{{ $t('footer.copyright') }}</p>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'Footer'
}
</script>

<style scoped>
.footer {
  background-color: #1a1a1a;
  color: white;
  padding: 4rem 0 2rem;
  margin-top: auto;
}

.footer h5 {
  color: #d4af37;
  margin-bottom: 1.5rem;
}

.contact-info,
.quick-links {
  list-style: none;
  padding: 0;
}

.contact-info li,
.quick-links li {
  margin-bottom: 0.8rem;
  display: flex;
  align-items: center;
}

.contact-info i {
  margin-right: 0.8rem;
  color: #d4af37;
  width: 20px;
}

.quick-links a {
  color: white;
  text-decoration: none;
  transition: color 0.3s ease;
}

.quick-links a:hover {
  color: #d4af37;
}

.footer-bottom {
  margin-top: 3rem;
  padding-top: 2rem;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  text-align: center;
}

@media (max-width: 768px) {
  .footer {
    padding: 3rem 0 1.5rem;
  }
  
  .col-md-4 {
    margin-bottom: 2rem;
  }
}
</style> 