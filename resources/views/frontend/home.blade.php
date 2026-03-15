<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Restaurant Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .feature-card {
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('tenant.register') }}">Register Restaurant</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4 mb-4">Manage Your Restaurant with Ease</h1>
            <p class="lead mb-4">Streamline your operations, manage orders, and grow your business with our comprehensive restaurant management system.</p>
            <a href="{{ route('tenant.register') }}" class="btn btn-primary btn-lg">Get Started</a>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Powerful Features</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-utensils fa-3x mb-3 text-primary"></i>
                            <h5 class="card-title">Menu Management</h5>
                            <p class="card-text">Easily create and manage your restaurant menu with categories, prices, and descriptions.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-shopping-cart fa-3x mb-3 text-primary"></i>
                            <h5 class="card-title">Order Management</h5>
                            <p class="card-text">Handle orders efficiently with real-time updates and status tracking.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-3x mb-3 text-primary"></i>
                            <h5 class="card-title">Analytics & Reports</h5>
                            <p class="card-text">Get insights into your business performance with detailed analytics and reports.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Simple Pricing</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Trial</h5>
                            <h2 class="card-text">Free</h2>
                            <p class="text-muted">14 days</p>
                            <ul class="list-unstyled">
                                <li>✓ Basic Features</li>
                                <li>✓ 1 Restaurant</li>
                                <li>✓ Email Support</li>
                            </ul>
                            <a href="{{ route('tenant.register') }}" class="btn btn-outline-primary">Start Trial</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Standard</h5>
                            <h2 class="card-text">$49/mo</h2>
                            <p class="text-muted">Billed monthly</p>
                            <ul class="list-unstyled">
                                <li>✓ All Basic Features</li>
                                <li>✓ 1 Restaurant</li>
                                <li>✓ Priority Support</li>
                                <li>✓ Advanced Analytics</li>
                            </ul>
                            <a href="{{ route('tenant.register') }}" class="btn btn-primary">Get Started</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Enterprise</h5>
                            <h2 class="card-text">$99/mo</h2>
                            <p class="text-muted">Billed monthly</p>
                            <ul class="list-unstyled">
                                <li>✓ All Standard Features</li>
                                <li>✓ Multiple Restaurants</li>
                                <li>✓ 24/7 Support</li>
                                <li>✓ Custom Features</li>
                            </ul>
                            <a href="{{ route('tenant.register') }}" class="btn btn-primary">Contact Sales</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ config('app.name') }}</h5>
                    <p>Your complete restaurant management solution.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
</body>
</html> 