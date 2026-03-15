<template>
    <div class="home ">

        <!-- Hero Section -->
        <section class="header-image-wrapper pt-5 mt-5 pb-5 mb-5" style=" padding-top: 36px;">
            <div class="bg"
                style="background-image: url('\Mox_files/homepage.jpg'); width: 100%; height: 100%; position: absolute; inset: 0; background-size: cover; background-repeat: no-repeat; background-position: center; z-index: 0;">
            </div>
            <div class="mask"
                style="background: #000000b3; width: 100%; height: 100%; position: absolute; overflow: hidden; top: 0; left: 0; z-index: 1;">
            </div>
            <div class="header-image-content home-page d-flex flex-column align-items-center justify-content-center w-100"
                style="min-height: 520px; position: relative; z-index: 2;">
                <h1 class="display-1 mb-4 text-white">{{ $t('home.hero.title') }}</h1>
                <p class="lead mb-5 text-white">{{ $t('home.hero.subtitle') }}</p>
                <div class="d-flex gap-3 justify-content-center mt-4">
                    <div class="cta-buttons">
                        <router-link to="/tenant-register" class="btn btn-danger btn-lg me-3"
                            style="background: #c62828" id="get-started-btn" @click.native="handleGetStartedClick">
                            {{ $t('home.hero.getStarted') }}
                        </router-link>
                        <a href="#features" class="btn btn-outline-light btn-lg">
                            {{ $t('home.hero.learnMore') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Us Section -->
        <section class="about-section" style="background: #fafafa; margin-top: 120px;">
            <div class="px-3">
                <div class="theme-container" style="max-width: 1100px; margin: 0 auto;">
                    <div class="text-center mb-3">
                        <h1
                            style="font-size: 2.8rem; font-weight: bold; margin-bottom: 0.5rem; display: inline-block; position: relative;">
                            {{ $t('home.about.title') }}
                            <span
                                style="display: block; height: 4px; width: 90px; background: #c62828; border-radius: 2px; margin: 0.5rem auto 0 auto;"></span>
                        </h1>
                    </div>
                    <p class="section-desc text-muted mb-4"
                        style="text-align: center; font-size: 1.1rem; color: #666; margin-bottom: 2.5rem;">
                        {{ $t('home.about.subtitle') }}
                    </p>
                    <div class="about-card d-flex flex-wrap bg-white shadow mb-4"
                        style="border-radius: 20px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); max-width: 100%; margin: 0 auto;">
                        <div class="about-img-col col-12 col-md-6 p-0" style="flex: 1 1 50%; max-width: 50%;">
                            <img src="/Mox_files/about-us.jpg" alt="about" class="w-100 h-100 d-block"
                                style="object-fit: cover; height: 100%; min-height: 320px; border-top-left-radius: 20px; border-bottom-left-radius: 20px;">
                        </div>
                        <div class="about-text-col col-12 col-md-6 d-flex flex-column justify-content-center p-4"
                            style="flex: 1 1 50%; max-width: 50%; min-width: 300px;">
                            <h2 class="secondary-font primary-color mb-2"
                                style="color: #c62828; font-size: 2rem; font-weight: 600;">{{ $t('home.about.heading') }}
                            </h2>
                            <p>{{ $t('home.about.description1') }}</p>
                            <p>{{ $t('home.about.description2') }}</p>
                            <div class="d-flex align-items-center justify-content-between pt-3">
                                <img src="/Mox_files/signature.png" alt="signature" style="max-height: 60px;">
                                <a href="/about" class="btn btn-danger text-uppercase px-4 py-2 ms-auto">{{ $t('home.about.readMore') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-5">
            <div class="container">
                <h2 class="text-center mb-5">{{ $t('home.whyChoose') }}</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="feature-card text-center p-4">
                            <i class="fas fa-rocket mb-3 fa-2x text-danger"></i>
                            <h3>{{ $t('home.features.quickSetup.title') }}</h3>
                            <p>{{ $t('home.features.quickSetup.description') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card text-center p-4">
                            <i class="fas fa-mobile-alt mb-3 fa-2x text-danger"></i>
                            <h3>{{ $t('home.features.mobileFriendly.title') }}</h3>
                            <p>{{ $t('home.features.mobileFriendly.description') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-card text-center p-4">
                            <i class="fas fa-chart-line mb-3 fa-2x text-danger"></i>
                            <h3>{{ $t('home.features.easyManagement.title') }}</h3>
                            <p>{{ $t('home.features.easyManagement.description') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recent Registered Restaurants Section -->
        <section v-if="!loading && tenants && tenants.length > 0" class="special-menu-section py-5" >
            <div class="container" style="max-width: 1200px; margin: 0 auto;">
                <div class="text-center mb-3">
                    <h2
                        style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem; display: inline-block; position: relative;">
                        {{ $t('home.recentRestaurants.title') }}
                        <span
                            style="display: block; height: 4px; width: 70px; background: #c62828; border-radius: 2px; margin: 0.5rem auto 0 auto;"></span>
                    </h2>
                </div>
                <p class="text-center text-muted mb-5" style="font-size: 1.1rem;">{{ $t('home.recentRestaurants.subtitle') }}</p>

                <!-- Loading State -->
                <div v-if="loading" class="text-center py-5">
                    <div class="spinner-border text-danger" role="status">
                        <span class="visually-hidden">{{ $t('home.recentRestaurants.loading') }}</span>
                    </div>
                    <p class="mt-3">{{$t('home.recentRestaurants.loadingRestaurants')}}</p>
                </div>

                <!-- Error State -->
                <div v-else-if="error" class="text-center py-5">
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ error }}
                        <button @click="fetchTenants" class="btn btn-outline-danger btn-sm ms-3">{{  $t('home.recentRestaurants.tryAgain') }}</button>
                    </div>
                </div>

                <!-- Restaurants Grid -->
                <div v-else class="row g-4 justify-content-center">
                    <div v-for="tenant in tenants" :key="tenant.id" class="col-12 col-md-6 col-lg-3">
                        <div class="card h-100 shadow-sm border-0">
                            <!-- <img :src="tenant.image || '/Mox_files/default-restaurant.jpg'" class="card-img-top" :alt="tenant.name"
                                style="border-top-left-radius: 1rem; border-top-right-radius: 1rem; object-fit: cover; height: 180px;"> -->
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"
                                    style="font-size: 1.1rem; font-weight: bold;">{{
                                    tenant.name }}</h5>
                                <p class="card-text text-muted mb-2" style="font-size: 0.95rem;">{{ tenant.description || 'Delicious food and great service' }}</p>
                                <ul class="list-unstyled mb-2" style="font-size: 0.95rem;">
                                    <li><span class="material-icons"
                                            style="color: #c62828; font-size: 1.1em; vertical-align: middle;">local_offer</span>
                                        <b>{{$t('home.recentRestaurants.restaurantsGrid.cuisine')}}:</b> {{ tenant.category || 'Various' }}</li>
                                    <li><span class="material-icons"
                                            style="color: #c62828; font-size: 1.1em; vertical-align: middle;">call</span>
                                        <b>{{$t('home.recentRestaurants.restaurantsGrid.phone')}}:</b> {{ tenant.contact || 'Contact not available' }}</li>
                                    <li><span class="material-icons"
                                            style="color: #c62828; font-size: 1.1em; vertical-align: middle;">spa</span>
                                        <b>{{$t('home.recentRestaurants.restaurantsGrid.vegetarianOptions')}}:</b> {{ tenant.vegetarian ? 'Available' : 'Limited' }}</li>
                                    <li><span class="material-icons"
                                            style="color: #c62828; font-size: 1.1em; vertical-align: middle;">check_circle</span>
                                        <b>{{$t('home.recentRestaurants.restaurantsGrid.status')}}:</b> <span :class="tenant.status ? 'text-success' : 'text-danger'">{{ tenant.status ? 'Open' : 'Closed' }}</span></li>
                                </ul>
                                <!-- <div class="mt-auto d-flex align-items-center justify-content-center">
                                    <router-link :to="`/${tenant.id}`" class="btn btn-danger btn-sm w-100">View Restaurant</router-link>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="text-center mt-4">
                    <a href="/restaurants" class="btn btn-danger text-uppercase px-4 py-2">View All Restaurants</a>
                </div> -->
            </div>
        </section>


        <!-- Our Services Section -->
        <section class="our-services-section py-5" >
            <div class="container" style="max-width: 1300px; margin: 0 auto;">
                <div class="text-center mb-4">
                    <h2
                        style="font-size: 2.8rem; font-weight: bold; margin-bottom: 0.5rem; color: #1a2734; display: inline-block; position: relative;">
                        {{ $t('home.services.title') }}
                        <span
                            style="display: block; height: 4px; width: 90px; background: #c62828; border-radius: 2px; margin: 0.5rem auto 0 auto;"></span>
                    </h2>
                    <p class="text-center mb-4" style="font-size: 1.1rem; color: #888;">{{ $t('home.services.subtitle') }}</p>
                </div>
                <div class="row g-4 justify-content-center">
                    <!-- Card 1 -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card h-100 text-center shadow-sm border-0" style="border-radius: 8px;">
                            <div class="card-body py-5">
                                <span class="material-icons mb-3"
                                    style="font-size: 3rem; color: #c62828;">lunch_dining</span>
                                <h5 class="card-title mb-3"
                                    style="font-size: 1.3rem; font-weight: bold;">{{ $t('home.services.websiteBuilder.title') }}</h5>
                                <p class="card-text text-muted" style="font-size: 1rem;">{{ $t('home.services.websiteBuilder.description') }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Card 2 -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card h-100 text-center shadow-sm border-0"
                            style="background: #c62828; color: #fff; border-radius: 8px;">
                            <div class="card-body py-5">
                                <span class="material-icons mb-3"
                                    style="font-size: 3rem; color: #fff;">local_shipping</span>
                                <h5 class="card-title mb-3"
                                    style="font-size: 1.3rem; font-weight: bold;">{{ $t('home.services.onlineOrdering.title') }}</h5>
                                <p class="card-text" style="font-size: 1rem;">{{ $t('home.services.onlineOrdering.description') }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Card 3 -->
                    <div class="col-12 col-md-6 col-lg-3 ">
                        <div class="card  h-100 text-center shadow-sm border-0" style="border-radius: 8px;">
                            <div class="card-body py-5">
                                <span class="material-icons mb-3"
                                    style="font-size: 3rem; color: #c62828;">local_offer</span>
                                <h5 class="card-title mb-3"
                                    style="font-size: 1.3rem; font-weight: bold;">{{ $t('home.services.tableReservations.title') }}</h5>
                                <p class="card-text text-muted" style="font-size: 1rem;">{{ $t('home.services.tableReservations.description') }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Card 4 -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card h-100 text-center shadow-sm border-0"
                            style="background: #c62828; color: #fff; border-radius: 8px;">
                            <div class="card-body py-5">
                                <span class="material-icons mb-3" style="font-size: 3rem; color: #fff;">schedule</span>
                                <h5 class="card-title mb-3"
                                    style="font-size: 1.3rem; font-weight: bold;">{{ $t('home.services.inventoryManagement.title') }}</h5>
                                <p class="card-text" style="font-size: 1rem;">{{ $t('home.services.inventoryManagement.description') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="how-it-works py-5" style="background: #f8f9fa;">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 style="font-size: 2.5rem; font-weight: bold; color: #1a2734;">{{  $t('home.howItWorks.title') }}</h2>
                    <p style="font-size: 1.1rem; color: #666;">{{ $t('home.howItWorks.subtitle') }}</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="text-center p-4">
                            <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                                <span class="text-white fw-bold" style="font-size: 1.5rem;">1</span>
                            </div>
                            <h4 style="font-size: 1.3rem; font-weight: bold; margin-bottom: 1rem;">{{ $t('home.howItWorks.step1.title') }}</h4>
                            <p style="color: #666;">{{ $t('home.howItWorks.step1.description') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-4">
                            <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                                <span class="text-white fw-bold" style="font-size: 1.5rem;">2</span>
                            </div>
                            <h4 style="font-size: 1.3rem; font-weight: bold; margin-bottom: 1rem;">{{ $t('home.howItWorks.step2.title') }}</h4>
                            <p style="color: #666;">{{ $t('home.howItWorks.step2.description') }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-4">
                            <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                                <span class="text-white fw-bold" style="font-size: 1.5rem;">3</span>
                            </div>
                            <h4 style="font-size: 1.3rem; font-weight: bold; margin-bottom: 1rem;">{{ $t('home.howItWorks.step3.title') }}</h4>
                            <p style="color: #666;">{{ $t('home.howItWorks.step3.description') }}</p>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-5">
                    <router-link to="/tenant-register" class="btn btn-danger btn-lg px-5 py-3">{{ $t('home.howItWorks.cta') }}</router-link>
                </div>
            </div>
        </section>

        <!-- Google Integration Section -->
        <section class="google-integration py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h2 style="font-size: 2.5rem; font-weight: bold; color: #1a2734; margin-bottom: 1.5rem;">{{ $t('home.googleIntegration.title') }}</h2>
                        <p style="font-size: 1.1rem; color: #666; margin-bottom: 2rem;">{{ $t('home.googleIntegration.subtitle') }}</p>
                        <ul style="list-style: none; padding: 0;">
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-check-circle text-danger me-3" style="font-size: 1.2rem;"></i>
                                <span>{{ $t('home.googleIntegration.features.1') }}</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-check-circle text-danger me-3" style="font-size: 1.2rem;"></i>
                                <span>{{ $t('home.googleIntegration.features.2') }}</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-check-circle text-danger me-3" style="font-size: 1.2rem;"></i>
                                <span>{{$t('home.googleIntegration.features.3')}}</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-check-circle text-danger me-3" style="font-size: 1.2rem;"></i>
                                <span>{{$t('home.googleIntegration.features.4')}}</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="fas fa-check-circle text-danger me-3" style="font-size: 1.2rem;"></i>
                                <span>{{ $t('home.googleIntegration.features.5') }}</span>
                            </li>
                        </ul>
                        <div class="mt-4">
                            <router-link to="/tenant-register" class="btn btn-danger btn-lg px-4 py-2">Try Google Import</router-link>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-center">
                            <img src="/Mox_files/google-integration.svg" alt="Google Integration" class="img-fluid rounded " style="max-width: 100%;">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Comparison Section -->
        <section class="features-comparison py-5" style="background: #f8f9fa;">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 style="font-size: 2.5rem; font-weight: bold; color: #1a2734;">{{ $t('home.comparison.title') }}</h2>
                    <p style="font-size: 1.1rem; color: #666;">{{ $t('home.comparison.subtitle') }}</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-danger text-white text-center py-3">
                                <h4 class="mb-0">{{ $t('home.comparison.traditional.title') }}</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-3 d-flex align-items-center">
                                        <i class="fas fa-times-circle text-danger me-3"></i>
                                        <span>{{ $t('home.comparison.traditional.features.1') }}</span>
                                    </li>
                                    <li class="mb-3 d-flex align-items-center">
                                        <i class="fas fa-times-circle text-danger me-3"></i>
                                        <span>{{ $t('home.comparison.traditional.features.2') }}</span>
                                    </li>
                                    <li class="mb-3 d-flex align-items-center">
                                        <i class="fas fa-times-circle text-danger me-3"></i>
                                        <span>{{ $t('home.comparison.traditional.features.3') }}</span>
                                    </li>
                                    <li class="mb-3 d-flex align-items-center">
                                        <i class="fas fa-times-circle text-danger me-3"></i>
                                        <span>{{ $t('home.comparison.traditional.features.4') }}</span>
                                    </li>
                                    <li class="mb-3 d-flex align-items-center">
                                        <i class="fas fa-times-circle text-danger me-3"></i>
                                        <span>{{ $t('home.comparison.traditional.features.5') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-header bg-success text-white text-center py-3">
                                <h4 class="mb-0">{{ $t('home.comparison.ourPlatform.title') }}</h4>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled">
                                    <li class="mb-3 d-flex align-items-center">
                                        <i class="fas fa-check-circle text-success me-3"></i>
                                        <span>{{$t('home.comparison.ourPlatform.features.1')}}</span>
                                    </li>
                                    <li class="mb-3 d-flex align-items-center">
                                        <i class="fas fa-check-circle text-success me-3"></i>
                                        <span>{{$t('home.comparison.ourPlatform.features.2')}}</span>
                                    </li>
                                    <li class="mb-3 d-flex align-items-center">
                                        <i class="fas fa-check-circle text-success me-3"></i>
                                        <span>{{$t('home.comparison.ourPlatform.features.3')}}</span>
                                    </li>
                                    <li class="mb-3 d-flex align-items-center">
                                        <i class="fas fa-check-circle text-success me-3"></i>
                                        <span>{{$t('home.comparison.ourPlatform.features.4')}}</span>
                                    </li>
                                    <li class="mb-3 d-flex align-items-center">
                                        <i class="fas fa-check-circle text-success me-3"></i>
                                        <span>{{
                                        $t('home.comparison.ourPlatform.features.5')}}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Customer Success Section -->
        <section class="customer-success py-5">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 style="font-size: 2.5rem; font-weight: bold; color: #1a2734;">{{ $t('home.customerSuccess.title') }}</h2>
                    <p style="font-size: 1.1rem; color: #666;">{{ $t('home.customerSuccess.subtitle') }}</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-utensils text-white"></i>
                                </div>
                                <h5 class="card-title">{{ $t('home.customerSuccess.testimonials.1.name') }}</h5>
                                <p class="card-text text-muted">{{ $t('home.customerSuccess.testimonials.1.text') }}</p>
                                <div class="text-warning">★★★★★</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-coffee text-white"></i>
                                </div>
                                <h5 class="card-title">{{ $t('home.customerSuccess.testimonials.2.name') }}</h5>
                                <p class="card-text text-muted">{{ $t('home.customerSuccess.testimonials.2.text') }}</p>
                                <div class="text-warning">★★★★★</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-hamburger text-white"></i>
                                </div>
                                <h5 class="card-title">{{ $t('home.customerSuccess.testimonials.3.name') }}</h5>
                                <p class="card-text text-muted">{{ $t('home.customerSuccess.testimonials.3.text') }}</p>
                                <div class="text-warning">★★★★★</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section py-5" style="background: #f8f9fa;">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 style="font-size: 2.5rem; font-weight: bold; color: #1a2734;">{{ $t('home.faq.title') }}</h2>
                    <p style="font-size: 1.1rem; color: #666;">{{ $t('home.faq.subtitle') }}    </p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                        {{ $t('home.faq.questions.1.question') }}
                                    </button>
                                </h2>
                                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        {{ $t('home.faq.questions.1.answer') }}
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                        {{ $t('home.faq.questions.2.question') }}
                                    </button>
                                </h2>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        {{ $t('home.faq.questions.2.answer') }}
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                        {{ $t('home.faq.questions.3.question') }}
                                    </button>
                                </h2>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        {{ $t('home.faq.questions.3.answer') }}
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                        {{ $t('home.faq.questions.4.question') }}
                                    </button>
                                </h2>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        {{ $t('home.faq.questions.4.answer') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Platform Benefits Section -->
        <section class="platform-benefits py-5" style="background: #f8f9fa;">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 style="font-size: 2.5rem; font-weight: bold; color: #1a2734;">{{ $t('home.platformBenefits.title') }}</h2>
                    <p style="font-size: 1.1rem; color: #666;">{{ $t('home.platformBenefits.subtitle') }}</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="text-center p-4">
                            <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-globe text-white fa-2x"></i>
                            </div>
                            <h4 style="font-size: 1.2rem; font-weight: bold;">{{  $t('home.platformBenefits.noHostingFees.title') }}</h4>
                            <p style="color: #666;">{{  $t('home.platformBenefits.noHostingFees.description') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="text-center p-4">
                            <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-mobile-alt text-white fa-2x"></i>
                            </div>
                            <h4 style="font-size: 1.2rem; font-weight: bold;">{{ $t('home.platformBenefits.mobileReady.title') }}</h4>
                            <p style="color: #666;">{{ $t('home.platformBenefits.mobileReady.description') }} </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="text-center p-4">
                            <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-credit-card text-white fa-2x"></i>
                            </div>
                            <h4 style="font-size: 1.2rem; font-weight: bold;">{{ $t('home.platformBenefits.onlinePayments.title') }}</h4>
                            <p style="color: #666;">{{ $t('home.platformBenefits.onlinePayments.description') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="text-center p-4">
                            <div class="bg-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-chart-bar text-white fa-2x"></i>
                            </div>
                            <h4 style="font-size: 1.2rem; font-weight: bold;">{{ $t('home.platformBenefits.businessReports.title') }}</h4>
                            <p style="color: #666;">{{ $t('home.platformBenefits.businessReports.description') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Meet our Team Section -->
        <section class="meet-team-section py-5" style="background: #fafafa;display:none">
            <div class="container" style="max-width: 1200px; margin: 0 auto;">
                <div class="text-center mb-4">
                    <h2
                        style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem; color: #1a2734; display: inline-block; position: relative;">
                        Meet our Team
                        <span
                            style="display: block; height: 4px; width: 90px; background: #c62828; border-radius: 2px; margin: 0.5rem auto 0 auto;"></span>
                    </h2>
                    <p class="text-center mb-4" style="font-size: 1.1rem; color: #888;">What can be better than food
                        prepared with love</p>
                </div>
                <div class="row g-4 justify-content-center">
                    <!-- Card 1 -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card h-100 text-center border-0" style="border-radius: 8px;">
                            <img src="/Mox_files/1.jpg" class="card-img-top team-img" alt="Andy Warhol">
                            <div class="card-body pb-2">
                                <h5 class="card-title mb-1"
                                    style="font-size: 1.15rem; font-weight: bold;">Andy
                                    Warhol</h5>
                                <div class="text-muted mb-2" style="font-size: 0.98rem;">Head of Chef</div>
                                <div class="text-warning mb-2" style="font-size: 1.1rem;">★★★★★</div>
                                <div class="team-social mb-2">
                                    <a href="#" class="social-icon  text-danger" aria-label="Facebook">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M22.675 0h-21.35C.595 0 0 .592 0 1.326v21.348C0 23.406.595 24 1.325 24h11.495v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.797.143v3.24l-1.918.001c-1.504 0-1.797.715-1.797 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.406 24 24 23.406 24 22.674V1.326C24 .592 23.406 0 22.675 0" />
                                        </svg>
                                    </a>
                                    <a href="#" class="social-icon  text-danger" aria-label="Twitter">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M24 4.557a9.83 9.83 0 0 1-2.828.775 4.932 4.932 0 0 0 2.165-2.724c-.951.564-2.005.974-3.127 1.195a4.916 4.916 0 0 0-8.38 4.482C7.691 8.095 4.066 6.13 1.64 3.161c-.542.929-.856 2.01-.857 3.17 0 2.188 1.115 4.117 2.823 5.254a4.904 4.904 0 0 1-2.229-.616c-.054 2.281 1.581 4.415 3.949 4.89a4.936 4.936 0 0 1-2.224.084c.627 1.956 2.444 3.377 4.6 3.417A9.867 9.867 0 0 1 0 21.543a13.94 13.94 0 0 0 7.548 2.209c9.058 0 14.009-7.496 14.009-13.986 0-.21 0-.423-.016-.634A9.936 9.936 0 0 0 24 4.557z" />
                                        </svg>
                                    </a>
                                    <a href="#" class="social-icon  text-danger" aria-label="LinkedIn">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M22.23 0H1.77C.792 0 0 .771 0 1.723v20.549C0 23.229.792 24 1.77 24h20.459C23.208 24 24 23.229 24 22.271V1.723C24 .771 23.208 0 22.23 0zM7.12 20.452H3.56V9h3.56v11.452zM5.34 7.633a2.062 2.062 0 1 1 0-4.124 2.062 2.062 0 0 1 0 4.124zM20.452 20.452h-3.555v-5.569c0-1.328-.025-3.037-1.849-3.037-1.851 0-2.134 1.445-2.134 2.939v5.667h-3.555V9h3.414v1.561h.049c.476-.899 1.637-1.849 3.37-1.849 3.602 0 4.267 2.369 4.267 5.455v6.285z" />
                                        </svg>
                                    </a>
                                    <a href="#" class="social-icon  text-danger" aria-label="Instagram">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.334 3.608 1.308.974.974 1.246 2.241 1.308 3.608.058 1.266.069 1.646.069 4.85s-.012 3.584-.07 4.85c-.062 1.366-.334 2.633-1.308 3.608-.974.974-2.241 1.246-3.608 1.308-1.266.058-1.646.069-4.85.069s-3.584-.012-4.85-.07c-1.366-.062-2.633-.334-3.608-1.308-.974-.974-1.246-2.241-1.308-3.608C2.175 15.647 2.163 15.267 2.163 12s.012-3.584.07-4.85c.062-1.366.334-2.633 1.308-3.608C4.515 2.567 5.782 2.295 7.148 2.233 8.414 2.175 8.794 2.163 12 2.163zm0-2.163C8.741 0 8.332.013 7.052.072 5.775.131 4.602.425 3.635 1.392 2.668 2.359 2.374 3.532 2.315 4.809 2.256 6.089 2.243 6.498 2.243 12c0 5.502.013 5.911.072 7.191.059 1.277.353 2.45 1.32 3.417.967.967 2.14 1.261 3.417 1.32 1.28.059 1.689.072 7.191.072s5.911-.013 7.191-.072c1.277-.059 2.45-.353 3.417-1.32.967-.967 1.261-2.14 1.32-3.417.059-1.28.072-1.689.072-7.191s-.013-5.911-.072-7.191c-.059-1.277-.353-2.45-1.32-3.417C19.398.425 18.225.131 16.948.072 15.668.013 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zm0 10.162a3.999 3.999 0 1 1 0-7.998 3.999 3.999 0 0 1 0 7.998zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z" />
                                        </svg>
                                    </a>
                                </div>
                                <a href="#" class="btn btn-danger btn-sm w-100">VIEW PROFILE</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card h-100 text-center border-0" style="border-radius: 8px;">
                            <img src="/Mox_files/2.jpg" class="card-img-top team-img" alt="Lusia Manuel">
                            <div class="card-body pb-2">
                                <h5 class="card-title mb-1"
                                    style="font-size: 1.15rem; font-weight: bold;">Lusia
                                    Manuel</h5>
                                <div class="text-muted mb-2" style="font-size: 0.98rem;">Assistant Chef</div>
                                <div class="text-warning mb-2" style="font-size: 1.1rem;">★★★★★</div>
                                <div class="team-social mb-2">
                                    <a href="#" class="social-icon  text-danger" aria-label="Facebook">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M22.675 0h-21.35C.595 0 0 .592 0 1.326v21.348C0 23.406.595 24 1.325 24h11.495v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.797.143v3.24l-1.918.001c-1.504 0-1.797.715-1.797 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.406 24 24 23.406 24 22.674V1.326C24 .592 23.406 0 22.675 0" />
                                        </svg>
                                    </a>
                                    <a href="#" class="social-icon  text-danger" aria-label="Twitter">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M24 4.557a9.83 9.83 0 0 1-2.828.775 4.932 4.932 0 0 0 2.165-2.724c-.951.564-2.005.974-3.127 1.195a4.916 4.916 0 0 0-8.38 4.482C7.691 8.095 4.066 6.13 1.64 3.161c-.542.929-.856 2.01-.857 3.17 0 2.188 1.115 4.117 2.823 5.254a4.904 4.904 0 0 1-2.229-.616c-.054 2.281 1.581 4.415 3.949 4.89a4.936 4.936 0 0 1-2.224.084c.627 1.956 2.444 3.377 4.6 3.417A9.867 9.867 0 0 1 0 21.543a13.94 13.94 0 0 0 7.548 2.209c9.058 0 14.009-7.496 14.009-13.986 0-.21 0-.423-.016-.634A9.936 9.936 0 0 0 24 4.557z" />
                                        </svg>
                                    </a>
                                    <a href="#" class="social-icon  text-danger" aria-label="LinkedIn">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M22.23 0H1.77C.792 0 0 .771 0 1.723v20.549C0 23.229.792 24 1.77 24h20.459C23.208 24 24 23.229 24 22.271V1.723C24 .771 23.208 0 22.23 0zM7.12 20.452H3.56V9h3.56v11.452zM5.34 7.633a2.062 2.062 0 1 1 0-4.124 2.062 2.062 0 0 1 0 4.124zM20.452 20.452h-3.555v-5.569c0-1.328-.025-3.037-1.849-3.037-1.851 0-2.134 1.445-2.134 2.939v5.667h-3.555V9h3.414v1.561h.049c.476-.899 1.637-1.849 3.37-1.849 3.602 0 4.267 2.369 4.267 5.455v6.285z" />
                                        </svg>
                                    </a>
                                    <a href="#" class="social-icon  text-danger" aria-label="Instagram">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.334 3.608 1.308.974.974 1.246 2.241 1.308 3.608.058 1.266.069 1.646.069 4.85s-.012 3.584-.07 4.85c-.062 1.366-.334 2.633-1.308 3.608-.974.974-2.241 1.246-3.608 1.308-1.266.058-1.646.069-4.85.069s-3.584-.012-4.85-.07c-1.366-.062-2.633-.334-3.608-1.308-.974-.974-1.246-2.241-1.308-3.608C2.175 15.647 2.163 15.267 2.163 12s.012-3.584.07-4.85c.062-1.366.334-2.633 1.308-3.608C4.515 2.567 5.782 2.295 7.148 2.233 8.414 2.175 8.794 2.163 12 2.163zm0-2.163C8.741 0 8.332.013 7.052.072 5.775.131 4.602.425 3.635 1.392 2.668 2.359 2.374 3.532 2.315 4.809 2.256 6.089 2.243 6.498 2.243 12c0 5.502.013 5.911.072 7.191.059 1.277.353 2.45 1.32 3.417.967.967 2.14 1.261 3.417 1.32 1.28.059 1.689.072 7.191.072s5.911-.013 7.191-.072c1.277-.059 2.45-.353 3.417-1.32.967-.967 1.261-2.14 1.32-3.417.059-1.28.072-1.689.072-7.191s-.013-5.911-.072-7.191c-.059-1.277-.353-2.45-1.32-3.417C19.398.425 18.225.131 16.948.072 15.668.013 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zm0 10.162a3.999 3.999 0 1 1 0-7.998 3.999 3.999 0 0 1 0 7.998zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z" />
                                        </svg>
                                    </a>
                                </div>
                                <a href="#" class="btn btn-danger btn-sm w-100 px-3">VIEW PROFILE</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card h-100 text-center border-0" style="border-radius: 8px;">
                            <img src="/Mox_files/3.jpg" class="card-img-top team-img" alt="Michael Blair">
                            <div class="card-body pb-2">
                                <h5 class="card-title mb-1"
                                    style="font-size: 1.15rem; font-weight: bold;">Michael
                                    Blair</h5>
                                <div class="text-muted mb-2" style="font-size: 0.98rem;">Intern Chef</div>
                                <div class="text-warning mb-2" style="font-size: 1.1rem;">★★★★★</div>
                                <div class="team-social mb-2">
                                    <a href="#" class="social-icon  text-danger" aria-label="Facebook">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M22.675 0h-21.35C.595 0 0 .592 0 1.326v21.348C0 23.406.595 24 1.325 24h11.495v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.797.143v3.24l-1.918.001c-1.504 0-1.797.715-1.797 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.406 24 24 23.406 24 22.674V1.326C24 .592 23.406 0 22.675 0" />
                                        </svg>
                                    </a>
                                    <a href="#" class="social-icon  text-danger" aria-label="Twitter">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M24 4.557a9.83 9.83 0 0 1-2.828.775 4.932 4.932 0 0 0 2.165-2.724c-.951.564-2.005.974-3.127 1.195a4.916 4.916 0 0 0-8.38 4.482C7.691 8.095 4.066 6.13 1.64 3.161c-.542.929-.856 2.01-.857 3.17 0 2.188 1.115 4.117 2.823 5.254a4.904 4.904 0 0 1-2.229-.616c-.054 2.281 1.581 4.415 3.949 4.89a4.936 4.936 0 0 1-2.224.084c.627 1.956 2.444 3.377 4.6 3.417A9.867 9.867 0 0 1 0 21.543a13.94 13.94 0 0 0 7.548 2.209c9.058 0 14.009-7.496 14.009-13.986 0-.21 0-.423-.016-.634A9.936 9.936 0 0 0 24 4.557z" />
                                        </svg>
                                    </a>
                                    <a href="#" class="social-icon  text-danger" aria-label="LinkedIn">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M22.23 0H1.77C.792 0 0 .771 0 1.723v20.549C0 23.229.792 24 1.77 24h20.459C23.208 24 24 23.229 24 22.271V1.723C24 .771 23.208 0 22.23 0zM7.12 20.452H3.56V9h3.56v11.452zM5.34 7.633a2.062 2.062 0 1 1 0-4.124 2.062 2.062 0 0 1 0 4.124zM20.452 20.452h-3.555v-5.569c0-1.328-.025-3.037-1.849-3.037-1.851 0-2.134 1.445-2.134 2.939v5.667h-3.555V9h3.414v1.561h.049c.476-.899 1.637-1.849 3.37-1.849 3.602 0 4.267 2.369 4.267 5.455v6.285z" />
                                        </svg>
                                    </a>
                                    <a href="#" class="social-icon  text-danger" aria-label="Instagram">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.334 3.608 1.308.974.974 1.246 2.241 1.308 3.608.058 1.266.069 1.646.069 4.85s-.012 3.584-.07 4.85c-.062 1.366-.334 2.633-1.308 3.608-.974.974-2.241 1.246-3.608 1.308-1.266.058-1.646.069-4.85.069s-3.584-.012-4.85-.07c-1.366-.062-2.633-.334-3.608-1.308-.974-.974-1.246-2.241-1.308-3.608C2.175 15.647 2.163 15.267 2.163 12s.012-3.584.07-4.85c.062-1.366.334-2.633 1.308-3.608C4.515 2.567 5.782 2.295 7.148 2.233 8.414 2.175 8.794 2.163 12 2.163zm0-2.163C8.741 0 8.332.013 7.052.072 5.775.131 4.602.425 3.635 1.392 2.668 2.359 2.374 3.532 2.315 4.809 2.256 6.089 2.243 6.498 2.243 12c0 5.502.013 5.911.072 7.191.059 1.277.353 2.45 1.32 3.417.967.967 2.14 1.261 3.417 1.32 1.28.059 1.689.072 7.191.072s5.911-.013 7.191-.072c1.277-.059 2.45-.353 3.417-1.32.967-.967 1.261-2.14 1.32-3.417.059-1.28.072-1.689.072-7.191s-.013-5.911-.072-7.191c-.059-1.277-.353-2.45-1.32-3.417C19.398.425 18.225.131 16.948.072 15.668.013 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zm0 10.162a3.999 3.999 0 1 1 0-7.998 3.999 3.999 0 0 1 0 7.998zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z" />
                                        </svg>
                                    </a>
                                </div>
                                <a href="#" class="btn btn-danger btn-sm w-100 px-3">VIEW PROFILE</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card h-100 text-center border-0" style="border-radius: 8px;">
                            <img src="/Mox_files/4.jpg" class="card-img-top team-img" alt="Tereza Stiles">
                            <div class="card-body pb-2">
                                <h5 class="card-title mb-1"
                                    style="font-size: 1.15rem; font-weight: bold;">Tereza
                                    Stiles</h5>
                                <div class="text-muted mb-2" style="font-size: 0.98rem;">Assistant Chef</div>
                                <div class="text-warning mb-2" style="font-size: 1.1rem;">★★★★★</div>
                                <div class="team-social mb-2">
                                    <a href="#" class="social-icon  text-danger" aria-label="Facebook">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M22.675 0h-21.35C.595 0 0 .592 0 1.326v21.348C0 23.406.595 24 1.325 24h11.495v-9.294H9.692v-3.622h3.128V8.413c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.797.143v3.24l-1.918.001c-1.504 0-1.797.715-1.797 1.763v2.313h3.587l-.467 3.622h-3.12V24h6.116C23.406 24 24 23.406 24 22.674V1.326C24 .592 23.406 0 22.675 0" />
                                        </svg>
                                    </a>
                                    <a href="#" class="social-icon  text-danger" aria-label="Twitter">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M24 4.557a9.83 9.83 0 0 1-2.828.775 4.932 4.932 0 0 0 2.165-2.724c-.951.564-2.005.974-3.127 1.195a4.916 4.916 0 0 0-8.38 4.482C7.691 8.095 4.066 6.13 1.64 3.161c-.542.929-.856 2.01-.857 3.17 0 2.188 1.115 4.117 2.823 5.254a4.904 4.904 0 0 1-2.229-.616c-.054 2.281 1.581 4.415 3.949 4.89a4.936 4.936 0 0 1-2.224.084c.627 1.956 2.444 3.377 4.6 3.417A9.867 9.867 0 0 1 0 21.543a13.94 13.94 0 0 0 7.548 2.209c9.058 0 14.009-7.496 14.009-13.986 0-.21 0-.423-.016-.634A9.936 9.936 0 0 0 24 4.557z" />
                                        </svg>
                                    </a>
                                    <a href="#" class="social-icon  text-danger" aria-label="LinkedIn">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M22.23 0H1.77C.792 0 0 .771 0 1.723v20.549C0 23.229.792 24 1.77 24h20.459C23.208 24 24 23.229 24 22.271V1.723C24 .771 23.208 0 22.23 0zM7.12 20.452H3.56V9h3.56v11.452zM5.34 7.633a2.062 2.062 0 1 1 0-4.124 2.062 2.062 0 0 1 0 4.124zM20.452 20.452h-3.555v-5.569c0-1.328-.025-3.037-1.849-3.037-1.851 0-2.134 1.445-2.134 2.939v5.667h-3.555V9h3.414v1.561h.049c.476-.899 1.637-1.849 3.37-1.849 3.602 0 4.267 2.369 4.267 5.455v6.285z" />
                                        </svg>
                                    </a>
                                    <a href="#" class="social-icon  text-danger" aria-label="Instagram">
                                        <svg width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.334 3.608 1.308.974.974 1.246 2.241 1.308 3.608.058 1.266.069 1.646.069 4.85s-.012 3.584-.07 4.85c-.062 1.366-.334 2.633-1.308 3.608-.974.974-2.241 1.246-3.608 1.308-1.266.058-1.646.069-4.85.069s-3.584-.012-4.85-.07c-1.366-.062-2.633-.334-3.608-1.308-.974-.974-1.246-2.241-1.308-3.608C2.175 15.647 2.163 15.267 2.163 12s.012-3.584.07-4.85c.062-1.366.334-2.633 1.308-3.608C4.515 2.567 5.782 2.295 7.148 2.233 8.414 2.175 8.794 2.163 12 2.163zm0-2.163C8.741 0 8.332.013 7.052.072 5.775.131 4.602.425 3.635 1.392 2.668 2.359 2.374 3.532 2.315 4.809 2.256 6.089 2.243 6.498 2.243 12c0 5.502.013 5.911.072 7.191.059 1.277.353 2.45 1.32 3.417.967.967 2.14 1.261 3.417 1.32 1.28.059 1.689.072 7.191.072s5.911-.013 7.191-.072c1.277-.059 2.45-.353 3.417-1.32.967-.967 1.261-2.14 1.32-3.417.059-1.28.072-1.689.072-7.191s-.013-5.911-.072-7.191c-.059-1.277-.353-2.45-1.32-3.417C19.398.425 18.225.131 16.948.072 15.668.013 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zm0 10.162a3.999 3.999 0 1 1 0-7.998 3.999 3.999 0 0 1 0 7.998zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z" />
                                        </svg>
                                    </a>
                                </div>
                                <a href="#" class="btn btn-danger btn-sm w-100 px-3">VIEW PROFILE</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="testimonials py-5" style="display:none">
            <div class="container">
                <div class="section-header text-center mb-5">
                    <h2>{{ $t('home.testimonials.title') }}</h2>
                    <p>{{ $t('home.testimonials.subtitle') }}</p>
                </div>
                <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div v-for="(testimonial, idx) in testimonials" :key="testimonial.id"
                            :class="['carousel-item', { active: idx === 0 }]">
                            <div class="testimonial-card mx-auto" style="max-width: 500px;">
                                <div class="testimonial-content">
                                    <p class="quote">"{{ testimonial.content }}"</p>
                                </div>
                                <div class="testimonial-author d-flex align-items-center">
                                    <img :src="testimonial.avatar" :alt="testimonial.name" class="avatar">
                                    <div class="author-info">
                                        <h4 class="mb-0">{{ testimonial.name }}</h4>
                                        <p class="position mb-0">{{ testimonial.position }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#testimonialCarousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#testimonialCarousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    <div class="carousel-indicators mt-4">
                        <button v-for="(testimonial, idx) in testimonials" :key="testimonial.id + '-indicator'"
                            type="button" data-bs-target="#testimonialCarousel" :data-bs-slide-to="idx"
                            :class="{ active: idx === 0 }" :aria-current="idx === 0 ? 'true' : undefined"
                            :aria-label="'Slide ' + (idx + 1)"></button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Reservation Section -->
        <section class="reservation-section py-5 bg-dark text-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h2>{{ $t('home.reservation.title') }}</h2>
                        <p>{{ $t('home.reservation.subtitle') }}</p>
                        <ul class="reservation-info">
                            <li><i class="fas fa-phone"></i> {{ $t('home.reservation.contact.call') }}</li>
                        </ul>
                    </div>
                    <div class="col-lg-6">
                        <form class="reservation-form">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <input type="text" class="form-control"
                                        :placeholder="$t('home.reservation.form.name')" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="email" class="form-control"
                                        :placeholder="$t('home.reservation.form.email')" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="date" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="time" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <input type="number" class="form-control"
                                        :placeholder="$t('home.reservation.form.guests')" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <button type="submit" class="btn btn-primary w-100">{{
                                        $t('home.reservation.form.bookNow') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>


        <!-- Looking for fresh produce Section -->
        <section class="get-in-touch-section py-3" style="background: #cf2e2e; color: #fff;">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <img src="/Mox_files/operator.png" alt="Chef" class="img-fluid" style="max-height: 120px;">
                    </div>
                    <div class="col-md-5">
                        <div class="fw-bold" style="font-size: 1.5rem; ">{{$t('home.cta.title')}}</div>
                        <div style="font-size: 0.98rem;">{{$t('home.cta.subtitle')}}</div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center justify-content-md-center mt-3 mt-md-0">
                        <span class="material-icons me-2" style="font-size: 2rem; vertical-align: middle;">{{$t('home.cta.call')}}</span>
                        <div>
                            <div style="font-size: 0.85rem;">{{ $t('home.cta.startFreeTrial') }}</div>
                            <div class="fw-bold" style="font-size: 1.15rem;">{{$t('home.cta.noCreditCard') }}</div>
                        </div>
                    </div>
                    <div class="col-md-2 text-center mt-3 mt-md-0">
                        <a href="/tenant-register" class="btn btn-light text-dark px-4 py-2"
                            style="font-size: 0.95rem; font-weight: 500; border-radius: 4px;">{{ $t('home.cta.getStarted') }}</a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import { driver } from 'driver.js';
import 'driver.js/dist/driver.css';

export default {
    name: 'Home',
    data() {
        return {
            tenants: [],
            loading: false,
            error: null,
            testimonials: [
                {
                    id: 1,
                    name: 'John Smith',
                    position: 'Food Critic',
                    content: 'The best dining experience I\'ve had in years. The food was exceptional and the service was outstanding.',
                    avatar: '/images/testimonials/john.jpg'
                },
                {
                    id: 2,
                    name: 'Sarah Johnson',
                    position: 'Regular Customer',
                    content: 'I love coming here with my family. The atmosphere is perfect and the food is always delicious.',
                    avatar: '/images/testimonials/sarah.jpg'
                },
                {
                    id: 3,
                    name: 'Michael Brown',
                    position: 'Food Blogger',
                    content: 'A hidden gem in the city. Their special dishes are truly unique and worth trying.',
                    avatar: '/images/testimonials/michael.jpg'
                }
            ]
        }
    },
    mounted() {
        this.fetchTenants();

        // Only show the tour if the user hasn't clicked Get Started before
        if (!localStorage.getItem('restro_driver_tour_shown')) {
            setTimeout(() => {
                const driverObj = driver({
                    showProgress: true,
                    steps: [
                        {
                            element: '#get-started-btn',
                            popover: {
                                title: 'Start Here',
                                description: 'Click this button to begin setting up your restaurant business.',
                                position: 'bottom'
                            }
                        }
                    ]
                });
                driverObj.drive();
            }, 1000);
        }
    },
    methods: {
        async fetchTenants() {
            this.loading = true;
            this.error = null;

            try {
                const response = await axios.get('/tenants/list');

                if (response.data.success) {
                    this.tenants = response.data.data;
                } else {
                    this.error = 'Failed to fetch restaurants';
                }
            } catch (error) {
                console.error('Error fetching tenants:', error);
                this.error = 'Failed to load restaurants. Please try again later.';
            } finally {
                this.loading = false;
            }
        },

        handleGetStartedClick() {
            localStorage.setItem('restro_driver_tour_shown', '1');
        }
    }
}
</script>

<style scoped>
.home {
    overflow-x: hidden;
}

/* Hero Section */
.header-image-wrapper {
    position: relative;
    min-height: 520px;
    margin-top: 96px;
}

.header-image-content h1 {
    font-size: 3.5rem;
    line-height: 1.2;
}

.header-image-content p {
    font-size: 1.25rem;
    max-width: 800px;
    margin: 0 auto;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
}

/* About Section */
.about-section {
    padding: 4rem 1rem;
}

.about-card {
    margin: 2rem 0;
    flex-wrap: wrap;
}

.about-img-col img {
    min-height: 320px;
    object-fit: cover;
    width: 100%;
}

.about-text-col {
    padding: 2rem;
}

/* Features Section */
.feature-card {
    height: 100%;
    padding: 2rem;
    margin-bottom: 1.5rem;
    transition: transform 0.3s ease;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
    background: #fff;
}

.feature-card:hover {
    transform: translateY(-5px);
}

/* Special Menu Section */
.special-menu-section {
    padding: 4rem 1rem;
}

.card {
    height: 100%;
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.card-img-top {
    height: 180px;
    object-fit: cover;
}

/* Team Section */
.team-img {
    height: 220px;
    object-fit: cover;
    width: 100%;
}

.team-social {
    gap: 0.5rem;
    display: flex;
    justify-content: center;
}

/* Testimonials Section */
.testimonial-card {
    height: 100%;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
}

.quote {
    font-style: italic;
    margin-bottom: 1.5rem;
}

.testimonial-author {
    display: flex;
    align-items: center;
}

.avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 1rem;
}

.author-info h4 {
    margin: 0;
    font-size: 1.1rem;
}

.position {
    color: #6c757d;
    margin: 0;
}

/* Reservation Section */
.reservation-section {
    padding: 4rem 1rem;
    background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url('/images/reservation-bg.jpg');
    background-size: cover;
    background-position: center;
}

.reservation-info {
    list-style: none;
    padding: 0;
    margin: 2rem 0;
}

.reservation-info li {
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.reservation-info i {
    margin-right: 1rem;
    color: c62828;
}

.reservation-form .form-control {
    margin-bottom: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
}

.reservation-form .form-control::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

/* Get in Touch Section */
.get-in-touch-section {
    padding: 2rem 1rem;
}

/* Responsive Styles */
@media (max-width: 991.98px) {
    .header-image-content h1 {
        font-size: 2.2rem;
    }

    .about-card {
        flex-direction: column !important;
    }

    .about-img-col,
    .about-text-col {
        max-width: 100% !important;
        flex-basis: 100% !important;
    }

    .about-img-col img {
        border-radius: 20px 20px 0 0 !important;
    }

    .about-text-col {
        padding: 1.5rem;
    }

    .team-img {
        height: 180px;
    }
}

@media (max-width: 768px) {
    .header-image-wrapper {
        margin-top: 120px;
    }

    .header-image-content {
        padding: 3rem 1rem;
    }

    .cta-buttons {
        flex-direction: column;
        gap: 1rem;
        width: 100%;
        align-items: stretch;
    }

    .cta-buttons .btn {
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
    }

    .section-header h2 {
        font-size: 2rem;
    }

    .feature-card,
    .testimonial-card {
        padding: 1.5rem;
    }

    .reservation-section {
        text-align: center;
    }

    .reservation-info {
        justify-content: center;
    }

    .get-in-touch-section .row {
        text-align: center;
    }

    .get-in-touch-section .btn {
        width: 100%;
        max-width: 300px;
        margin: 1rem auto 0;
    }
}

@media (max-width: 576px) {
    .header-image-wrapper {
        margin-top: 110px;
    }

    .header-image-content h1 {
        font-size: 1.5rem;
    }

    .header-image-content p {
        font-size: 1rem;
    }

    .section-header h2 {
        font-size: 1.2rem;
    }

    .about-text-col {
        padding: 1rem;
    }

    .card-img-top {
        height: 120px;
    }

    .team-img {
        height: 120px;
    }

    .testimonial-card {
        padding: 1rem;
    }

    .reservation-form .form-control {
        font-size: 0.9rem;
    }
}
</style>
