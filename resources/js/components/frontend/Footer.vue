<template>
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <!-- About Section -->
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">{{ $t('footer.about.title') }}</h5>
                    <p>{{ $t('footer.about.description') }}</p>
                </div>

                <!-- Quick Links -->
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">{{ $t('footer.quickLinks.title') }}</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <router-link class="text-light text-decoration-none" :to="{ name: 'features' }">
                                {{ $t('footer.quickLinks.features') }}
                            </router-link>
                        </li>
                        <li class="mb-2">
                            <router-link class="text-light text-decoration-none" :to="{ name: 'pricing' }">
                                {{ $t('footer.quickLinks.pricing') }}
                            </router-link>
                        </li>
                        <li class="mb-2">
                            <router-link class="text-light text-decoration-none" :to="{ name: 'contact' }">
                                {{ $t('footer.quickLinks.contact') }}
                            </router-link>
                        </li>
                        <li class="mb-2">
                            <router-link class="text-light text-decoration-none" :to="{ name: 'privacy' }">
                                {{ $t('footer.quickLinks.privacy') }}
                            </router-link>
                        </li>
                        <li class="mb-2">
                            <router-link class="text-light text-decoration-none" :to="{ name: 'terms' }">
                                {{ $t('footer.quickLinks.terms') }}
                            </router-link>
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="col-md-4 mb-4">
                    <h5 class="mb-3">{{ $t('footer.contact.title') }}</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i> {{ $t('footer.contact.address') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone me-2"></i> {{ $t('footer.contact.phone') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2"></i> {{ $t('footer.contact.email') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock me-2"></i> {{ $t('footer.contact.hours') }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Copyright -->
            <hr class="my-4 bg-light">
            <div class="text-center">
                <p class="mb-0">{{ $t('footer.copyright') }}</p>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()
const currentLocale = ref(locale.value)

const changeLocale = () => {
    locale.value = currentLocale.value
    localStorage.setItem('language', currentLocale.value)
}
</script>

<style scoped>
.footer {
    background-color: #2c3e50;
}

.list-unstyled a {
    color: #fff;
    opacity: 0.8;
    transition: opacity 0.3s ease;
}

.list-unstyled a:hover {
    opacity: 1;
    text-decoration: none;
}

hr {
    opacity: 0.2;
}
</style> 