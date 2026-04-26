<template>
  <div class="tenant-dashboard-layout">
    <!-- Top Bar -->
    <header class="td-header d-flex align-items-center justify-content-between px-3 px-md-4 py-2 bg-white border-bottom fixed-top">
      <div class="td-header__left d-flex align-items-center gap-2 gap-md-3 min-w-0">
        <button
          class="btn btn-link text-dark d-lg-none p-2 sidebar-toggle-btn flex-shrink-0"
          @click="toggleSidebar"
          aria-label="Toggle sidebar"
          id="sidebar-toggle-btn">
          <i class="fas" :class="sidebarOpen ? 'fa-times' : 'fa-bars'" style="font-size: 1.25rem;"></i>
        </button>

        <!-- Eat Desk–style brand + current page -->
        <div class="td-brand d-flex align-items-center gap-2 gap-md-3 min-w-0 flex-grow-1 flex-lg-grow-0">
          <div class="td-brand__mark flex-shrink-0">
            <div class="td-brand__logo-wrap">
              <img
                :src="brandLogoSrc"
                :alt="businessName + ' logo'"
                class="td-brand__logo-img"
              />
              <span class="td-brand__online" aria-hidden="true"></span>
            </div>
          </div>
          <div class="td-brand__text min-w-0 d-none d-sm-block">
            <div class="td-brand__name text-truncate">{{ businessName }}</div>
            <div class="td-brand__os">Restaurant OS</div>
          </div>
          <div class="td-brand__rule d-none d-md-block flex-shrink-0" aria-hidden="true"></div>
          <div class="td-brand__page min-w-0 d-none d-md-block">
            <div class="td-brand__page-title text-truncate">{{ headerPageTitle }}</div>
            <div class="td-brand__page-sub">Manage your restaurant operations.</div>
          </div>
        </div>
      </div>

      <!-- Professional Navigation Bar -->
      <nav class="navbar navbar-expand-md navbar-light td-header__nav justify-content-center">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item">
            <router-link to="/dashboard/stock-check-requests" class="nav-link px-3 text-xs leading-tight" active-class="active">{{ $t('stockCheckRequests') }}</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/dashboard/orders" class="nav-link px-3 text-xs leading-tight" active-class="active">{{ $t('orders') }}</router-link>
          </li>
          <li class="nav-item dropdown position-static mega-menu-parent">
            <a class="nav-link dropdown-toggle px-3 text-xs leading-tight" href="#" id="megaMenuDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Setting
            </a>
            <div class="dropdown-menu mega-menu-dropdown shadow-lg p-4 mt-2" aria-labelledby="megaMenuDropdown">
              <div class="row" style="min-width: 480px;">
                <div class="col-6">
                  <div class="mb-2 text-uppercase text-muted text-xs leading-tight" style="letter-spacing: 0.5px;">{{ $t('restaurantSettings') }}</div>
                  <router-link to="/dashboard/users" class="dropdown-item text-xs leading-tight"><i class="fas fa-user"></i> {{ $t('users') }}</router-link>
                  <router-link to="/dashboard/roles" class="dropdown-item text-xs leading-tight"><i class="fas fa-user-tag"></i> {{ $t('roles') }}</router-link>
                </div>
                <div class="col-6">
                  <div class="mb-2 text-uppercase text-muted text-xs leading-tight" style="letter-spacing: 0.5px;">Theme Settings</div>
                  <router-link to="/dashboard/settings/general" class="dropdown-item text-xs leading-tight"><i class="fas fa-store"></i> Business settings</router-link>
                  <router-link to="/dashboard/website-settings" class="dropdown-item text-xs leading-tight"><i class="fas fa-globe"></i> Website settings</router-link>
                  <router-link to="/dashboard/themes" class="dropdown-item text-xs leading-tight"><i class="fas fa-paint-brush"></i> Themes</router-link>
                  <router-link to="/dashboard/colors" class="dropdown-item text-xs leading-tight"><i class="fas fa-palette"></i> Colors</router-link>
                  <router-link to="/dashboard/layouts" class="dropdown-item text-xs leading-tight"><i class="fas fa-columns"></i> Layouts</router-link>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </nav>

      <div class="td-header__right d-flex align-items-center gap-1 flex-shrink-0">
        <div class="position-relative notification-wrapper" id="notification-wrapper">
          <button class="btn btn-link td-notify-btn position-relative" data-bs-toggle="tooltip" data-bs-placement="bottom" :title="$t('notifications')" @click="toggleNotificationDropdown">
            <i class="fas fa-bell"></i>
            <span v-if="unreadCount > 0" class="notification-badge">{{ unreadCount }}</span>
          </button>
          <div v-if="showNotificationDropdown" class="notification-dropdown card shadow position-absolute end-0 mt-2" style="min-width:320px; max-width:95vw; z-index:2000; right:0;">
            <div class="card-header d-flex justify-content-between align-items-center py-2 pl-3 pr-2 bg-white border-bottom">
              <span class="fw-bold text-xs leading-tight">Notifications</span>
              <button type="button" class="btn-close btn-sm" @click="closeNotificationDropdown" style="font-size:0.75rem;"></button>
            </div>
            <div class="card-body " style="max-height:350px; overflow:auto;">
              <div v-if="loadingNotifications" class="d-flex justify-content-center align-items-center py-4">
                <div class="spinner-border spinner-border-sm td-spinner" role="status"></div>
              </div>
              <div v-else-if="notifications.length === 0" class="text-center text-muted py-4">
                <i class="fas fa-bell-slash fa-lg mb-2"></i><br>
                <span class="text-xs leading-tight">No notifications</span>
              </div>
              <div v-else>
                <div v-for="n in notifications" :key="n.id" class="border-bottom pl-3 pr-2 py-2 d-flex justify-content-between align-items-start notification-item" :class="{ 'text-muted bg-light': n.is_read }">
                  <div style="min-width:0; flex:1;">
                    <div class="fw-semibold text-truncate text-xs leading-tight mb-1">{{ n.title }}</div>
                    <div class="text-truncate text-xs leading-tight mb-1">{{ n.message }}</div>
                    <div class="text-muted text-xs leading-tight">{{ new Date(n.created_at).toLocaleString() }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <button
          class="btn btn-link td-help-btn px-2"
          data-bs-toggle="tooltip"
          data-bs-placement="bottom"
          :title="$t('showTour')"
          @click="restartTour"
        >
          <i class="fas fa-question-circle"></i>
        </button>

        <LanguageSwitcher class="td-header-lang" flag-only />

        <div class="dropdown td-profile-wrap" id="profile-dropdown-container">
          <button
            class="btn btn-link text-dark dropdown-toggle td-profile-trigger p-1 d-flex align-items-center"
            type="button"
            id="profileDropdown"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            :title="user ? user.name + ' (' + (user.role_name || $t('role')) + ')' : $t('profile')"
            data-bs-placement="bottom"
          >
            <span v-if="user && user.avatar" class="rounded-circle overflow-hidden flex-shrink-0 td-profile-av">
              <img :src="user.avatar" :alt="user.name" class="td-profile-av-img" />
            </span>
            <span v-else class="rounded-circle td-profile-avatar text-white d-flex align-items-center justify-content-center flex-shrink-0 td-profile-av">
              {{ user && user.name ? user.name.charAt(0).toUpperCase() : 'U' }}
            </span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown" style="min-width: 220px;">
            <li>
              <router-link class="dropdown-item d-flex align-items-center text-xs leading-tight" to="/dashboard/pricing">
                <i class="fas fa-rocket td-accent-icon me-2"></i>
                <span>{{ $t('Manage Subscribtions') }}</span>
              </router-link>
            </li>
            <li>
              <router-link class="dropdown-item d-flex align-items-center text-xs leading-tight" to="/dashboard/reset-pass">
                <i class="fas fa-key td-accent-icon me-2"></i>
                <span>{{ $t('passwordChange') }}</span>
              </router-link>
            </li>
            <li>
              <router-link class="dropdown-item d-flex align-items-center text-xs leading-tight" id="dashboard-settings-btn" to="/dashboard/settings/general">
                <i class="fas fa-cogs td-accent-icon me-2"></i>
                <span>{{ $t('settings') }}</span>
              </router-link>
            </li>
            <li>
              <router-link class="dropdown-item d-flex align-items-center text-xs leading-tight" id="dashboard-settings-btn" to="/dashboard/personal-settings">
                <i class="fas fa-cogs td-accent-icon me-2"></i>
                <span>{{ $t('personal_settings') }}</span>
              </router-link>
            </li>
            <li>
              <router-link class="dropdown-item d-flex align-items-center text-xs leading-tight" id="dashboard-settings-btn" to="/dashboard/email-settings">
                <i class="fas fa-envelope td-accent-icon me-2"></i>
                <span>{{ $t('emailSetting') }}</span>
              </router-link>
            </li>
            <li>
              <router-link class="dropdown-item d-flex align-items-center text-xs leading-tight" id="dashboard-settings-btn" to="/dashboard/payment-gateways">
                <i class="fas fa-credit-card td-accent-icon me-2"></i>
                <span>{{ $t('paymentGateways') }}</span>
              </router-link>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item d-flex align-items-center text-xs leading-tight" href="#" @click.prevent="logout">
                <i class="fas fa-sign-out td-accent-icon me-2"></i>
                <span>{{ $t('logout') }}</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <!-- Notification Modal -->
    <div class="modal fade" :class="{ show: showNotificationModal }" tabindex="-1" style="display: block;" v-if="showNotificationModal">
      <div class="modal-dialog modal-dialog-scrollable modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Notifications</h5>
            <button type="button" class="btn-close" @click="closeNotificationModal"></button>
          </div>
          <div class="modal-body p-0">
            <div v-if="notifications.length === 0" class="text-center text-muted py-4">No notifications</div>
            <div v-for="n in notifications" :key="n.id" class="border-bottom p-3 d-flex justify-content-between align-items-center" :class="{ 'text-muted': n.is_read }">
              <div>
                <div class="fw-bold">{{ n.title }}</div>
                <div style="font-size:0.95em;">{{ n.message }}</div>
                <div class="text-muted" style="font-size:0.85em;">{{ new Date(n.created_at).toLocaleString() }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-backdrop fade show" @click="closeNotificationModal"></div>
    </div>

    <!-- Sidebar Overlay (Mobile/Tablet) -->
    <div
      v-if="sidebarOpen"
      class="sidebar-overlay d-lg-none"
      @click="closeSidebar"
      id="sidebar-overlay">
    </div>

    <div class="td-main d-flex justify-content-center align-items-stretch">
      <!-- Sidebar -->
      <aside
        class="td-sidebar td-sidebar--rail"
        id="main-sidebar"
        :class="{
          'sidebar-open': sidebarOpen,
          'td-sidebar--collapsed': sidebarCollapsed
        }"
      >
        <div class="d-lg-none d-flex justify-content-end p-2 border-bottom border-light">
          <button
            class="btn btn-link text-dark p-1 sidebar-close-btn"
            @click="closeSidebar"
            aria-label="Close sidebar">
            <i class="fas fa-times" style="font-size: 1.25rem;"></i>
          </button>
        </div>
        <div class="td-sidebar-toolbar d-none d-lg-flex">
          <button
            type="button"
            class="td-sidebar-collapse-btn"
            :title="sidebarCollapsed ? 'Expand sidebar' : 'Minimize sidebar'"
            :aria-expanded="!sidebarCollapsed"
            aria-controls="main-navigation"
            @click="toggleSidebarCollapsed"
          >
            <i class="fas" :class="sidebarCollapsed ? 'fa-angle-double-right' : 'fa-angle-double-left'" aria-hidden="true"></i>
          </button>
        </div>
        <div class="td-sidebar-scroll">
          <nav class="td-nav flex-column" id="main-navigation">
            <router-link to="/dashboard/home" class="td-nav-link text-xs leading-tight" active-class="active" @click="closeSidebar">
              <i class="fas fa-th-large"></i>
              <span>{{ $t('dashboard') }}</span>
            </router-link>
            <div class="td-nav-section">Orders &amp; service</div>
            <router-link to="/dashboard/orders" class="td-nav-link text-xs leading-tight" active-class="active" @click="closeSidebar">
              <i class="fas fa-shopping-cart"></i>
              <span class="flex-grow-1">{{ $t('orders') }}</span>
              <span v-if="counts.orders > 0" class="badge bg-light text-dark text-xs leading-tight">{{ counts.orders }}</span>
            </router-link>
            <router-link to="/dashboard/stock-check-requests" class="td-nav-link text-xs leading-tight" active-class="active" @click="closeSidebar">
              <i class="fas fa-boxes"></i>
              <span>{{ $t('stockCheckRequests') }}</span>
            </router-link>
            <router-link to="/dashboard/reservations" class="td-nav-link text-xs leading-tight" active-class="active" @click="closeSidebar">
              <i class="fas fa-calendar-alt"></i>
              <span>{{ $t('reservations') }}</span>
            </router-link>
            <div class="td-nav-section">Engagement</div>
            <router-link to="/dashboard/contact-reqs" class="td-nav-link text-xs leading-tight" active-class="active" @click="closeSidebar">
              <i class="fas fa-calendar-alt"></i>
              <span>{{ $t('contactReqs') }}</span>
            </router-link>
            <router-link to="/dashboard/subscribers" class="td-nav-link text-xs leading-tight" active-class="active" @click="closeSidebar">
              <i class="fas fa-calendar-alt"></i>
              <span>{{ $t('subscribers') }}</span>
            </router-link>
            <router-link to="/dashboard/customers" class="td-nav-link text-xs leading-tight" active-class="active" @click="closeSidebar">
              <i class="fas fa-users"></i>
              <span>{{ $t('customers') }}</span>
            </router-link>
            <div class="td-nav-section">Menu</div>
            <router-link to="/dashboard/bulletin" class="td-nav-link text-xs leading-tight" active-class="active" @click="closeSidebar">
              <i class="fas fa-bullhorn"></i>
              <span>{{ $t('bulletin') }}</span>
            </router-link>
            <router-link to="/dashboard/categories" class="td-nav-link text-xs leading-tight" active-class="active" @click="closeSidebar">
              <i class="fas fa-list"></i>
              <span>{{ $t('categories') }}</span>
            </router-link>
            <router-link to="/dashboard/products" class="td-nav-link text-xs leading-tight" active-class="active" @click="closeSidebar">
              <i class="fas fa-box"></i>
              <span>{{ $t('products') }}</span>
            </router-link>
            <router-link to="/dashboard/cms" class="td-nav-link text-xs leading-tight" active-class="active" @click="closeSidebar">
              <i class="fas fa-file-alt"></i>
              <span>{{ $t('content_system') }}</span>
            </router-link>
            <router-link
              to="/dashboard/settings/general"
              class="td-nav-link text-xs leading-tight"
              :class="{ active: $route.path.startsWith('/dashboard/settings') }"
              @click="closeSidebar"
            >
              <i class="fas fa-cogs"></i>
              <span>Business settings</span>
            </router-link>
            <router-link
              to="/dashboard/website-settings"
              class="td-nav-link text-xs leading-tight"
              :class="{ active: $route.path.startsWith('/dashboard/website-settings') }"
              @click="closeSidebar"
            >
              <i class="fas fa-globe"></i>
              <span>Website settings</span>
            </router-link>
            <div class="td-nav-section">Administration</div>
            <router-link to="/dashboard/notifications" class="td-nav-link text-xs leading-tight" active-class="active" @click="closeSidebar">
              <i class="fas fa-bell"></i>
              <span>{{ $t('notifications') }}</span>
            </router-link>
            <router-link to="/dashboard/roles" class="td-nav-link text-xs leading-tight" active-class="active" @click="closeSidebar">
              <i class="fas fa-user-tag"></i>
              <span>{{ $t('roles') }}</span>
            </router-link>
            <router-link to="/dashboard/users" class="td-nav-link text-xs leading-tight" active-class="active" @click="closeSidebar">
              <i class="fas fa-user"></i>
              <span>{{ $t('users') }}</span>
            </router-link>
            <!-- <router-link to="/dashboard/maillogs" class="td-nav-link text-xs leading-tight" active-class="active" @click="closeSidebar"><i class="fas fa-envelope"></i> <span>{{ $t('mail_logs') }}</span></router-link> -->
            <a href="javascript:void(0)" @click="logout" class="td-nav-link text-xs leading-tight">
              <i class="fas fa-sign-out-alt"></i>
              <span>{{ $t('logout') }}</span>
            </a>
          </nav>
        </div>
      </aside>

      <!-- Main Content — full-bleed white workspace (no outer card) -->
      <main class="td-content flex-grow-1" id="main-content">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script>
import { driver } from 'driver.js';
import 'driver.js/dist/driver.css';
import { Tooltip } from 'bootstrap';
import LanguageSwitcher from '../components/LanguageSwitcher.vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { ref, computed, onMounted } from 'vue'; // ✅ for reactivity

export default {
  name: 'TenantDashboardLayout',
  components: {
    LanguageSwitcher
  },
  setup() {
    // ✅ Subscription state
    const subscription = ref(null);

    // Fetch subscription info
    const axiosTenant = axios.create({
      baseURL: '/tenant', // adjust if your tenant API prefix differs
      withCredentials: true,
    });

    onMounted(async () => {
  try {
    const { data } = await axios.get('/tenant/my-subscription');

    subscription.value = data.data || data || null;
    console.log('Subscription loaded:', subscription.value);
  } catch (error) {
    console.error('Failed to load subscription:', error);
    subscription.value = null; // Ensure it's set to null on error
  }
});

    // ✅ Compute remaining trial days
    const trialDaysRemaining = computed(() => {
      if (!subscription.value || !subscription.value.ends_at) return 0;
      const end = new Date(subscription.value.ends_at);
      const today = new Date();
      const diff = Math.ceil((end - today) / (1000 * 60 * 60 * 24));
      return diff > 0 ? diff : 0;
    });

    // ✅ Renew plan
    async function renewPlan() {
      try {
        const { data } = await axiosTenant.post('/subscription/renew');
        if (data.checkout_url) {
          window.location.href = data.checkout_url;
        } else {
          Swal.fire('Success', data.message || 'Subscription renewed successfully', 'success');
          location.reload();
        }
      } catch (error) {
        Swal.fire('Error', 'Renewal failed.', 'error');
      }
    }

    // ✅ Cancel trial
    async function cancelTrial() {
      if (confirm('Are you sure you want to cancel your trial?')) {
        try {
          await axiosTenant.post('/subscription/cancel');
          Swal.fire('Canceled', 'Trial canceled successfully.', 'success');
          location.reload();
        } catch (error) {
          Swal.fire('Error', 'Failed to cancel trial.', 'error');
        }
      }
    }

    // ✅ Choose plan
    function choosePlan() {
      window.location.href = '/dashboard/pricing';
    }

    // ✅ Date formatting
    function formatDate(date) {
      return new Date(date).toLocaleDateString();
    }

    // expose to template
    return {
      subscription,
      trialDaysRemaining,
      renewPlan,
      cancelTrial,
      choosePlan,
      formatDate
    };
  },

  data() {
    let user = null;
    try {
      user = JSON.parse(localStorage.getItem('user')) || null;
    } catch (e) { user = null; }

    return {
      user,
      setting: {},
      languages: [
        { code: 'en', name: 'English' },
        { code: 'de', name: 'Deutsch' }
      ],
      selectedLang: { code: 'en', name: 'English' },
      notifications: [],
      showNotificationDropdown: false,
      showNotificationModal: false,
      loadingNotifications: false,
      notificationPollInterval: null,
      counts: {
        orders: 0,
        stockCheckReqs: 0,
      },
      dashboardTour: null,
      sidebarOpen: false,
      sidebarCollapsed: false
    };
  },

  computed: {
    headerPageTitle() {
      const withTitle = this.$route.matched.filter(r => r.meta && r.meta.title);
      const leaf = withTitle[withTitle.length - 1];
      return leaf?.meta?.title || 'Dashboard';
    },
    businessName() {
      return this.setting?.business_name || 'Restaurant';
    },
    brandLogoSrc() {
      return this.setting?.logo_url || this.setting?.logo || '/assets/logo/airestro360.png';
    },
    memberSince() {
      if (!this.user || !this.user.created_at) return '';
      const date = new Date(this.user.created_at);
      return date.toLocaleString('default', { month: 'short', year: 'numeric' });
    },
    unreadCount() {
      return this.notifications.filter(n => !n.is_read).length;
    }
  },

  mounted() {
    try {
      this.sidebarCollapsed = localStorage.getItem('tenant_sidebar_collapsed') === '1';
    } catch (e) { /* ignore */ }
    this.fetchUser();
    this.initTooltips();
    this.fetchNotifications();
    this.fetchCounts();
    this.fetchSettings();

    // Poll notifications every 20 seconds
    this.notificationPollInterval = setInterval(() => {
      this.fetchNotifications();
    }, 20000);

    this.$nextTick(() => {
      document.querySelectorAll('.dropdown-toggle').forEach(el => {
        el.addEventListener('click', function(e) {
          e.stopPropagation();
        });
      });
    });

    this.$watch(
      () => this.$route.path,
      () => {
        window.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
        // Close sidebar when route changes
        this.closeSidebar();
      },
      { immediate: true }
    );

    document.addEventListener('click', this.handleClickOutsideDropdown);

    // Close sidebar when window is resized to desktop size
    window.addEventListener('resize', this.handleResize);
    this.handleResize(); // Check on mount
  },

  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutsideDropdown);
    window.removeEventListener('resize', this.handleResize);
    if (this.notificationPollInterval) clearInterval(this.notificationPollInterval);
  },

  methods: {
    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen;
      // Prevent body scroll when sidebar is open on mobile
      if (this.sidebarOpen) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    },

    closeSidebar() {
      this.sidebarOpen = false;
      document.body.style.overflow = '';
    },

    toggleSidebarCollapsed() {
      this.sidebarCollapsed = !this.sidebarCollapsed;
      try {
        localStorage.setItem('tenant_sidebar_collapsed', this.sidebarCollapsed ? '1' : '0');
      } catch (e) { /* ignore */ }
    },

    handleResize() {
      // Close sidebar if window is resized to desktop size (992px and above)
      if (window.innerWidth >= 992 && this.sidebarOpen) {
        this.closeSidebar();
      }
    },

    async fetchUser() {
      try {
        const response = await axios.get('/tenant/auth-check', { withCredentials: true });
        this.user = response.data.user;
        this.user.role_name = response.data.role_name;
        localStorage.setItem('user', JSON.stringify(response.data.user));
        this.$nextTick(() => this.initDashboardTour());
      } catch (error) {
        console.error('Error fetching user:', error);
      }
    },

    initDashboardTour() {
      const hasSeenTour = localStorage.getItem('tenant_dashboard_tour_shown');
      if (!hasSeenTour && this.user) {
        setTimeout(() => {
          this.startDashboardTour();
        }, 1500);
      }
    },

    startDashboardTour() {
      this.dashboardTour = driver({
        showProgress: true,
        animate: true,
        allowClose: true,
        overlayOpacity: 0.4,
        smoothScroll: false,
        stagePadding: 8,
        stageRadius: 8,
        popoverClass: 'custom-driver-popover',
        steps: [
          {
            element: '#main-navigation',
            popover: {
              title: '📊 Main Navigation',
              description: 'Access Orders, Products, Customers, and other sections here.',
              position: 'right-start'
            }
          },
          {
            element: '#profile-dropdown-container',
            popover: {
              title: '👤 Your Profile & Settings',
              description: 'Click your profile icon for settings and logout.',
              position: 'bottom-start',
              onNextClick: () => {
                const dropdownBtn = document.getElementById('profileDropdown');
                if (dropdownBtn) dropdownBtn.click();
                setTimeout(() => this.dashboardTour.moveNext(), 500);
              }
            }
          },
          {
            element: '#dashboard-settings-btn',
            popover: {
              title: '⚙️ System Settings',
              description: 'Manage your restaurant preferences here.',
              position: 'left',
              onNextClick: () => {
                const dropdownBtn = document.getElementById('profileDropdown');
                if (dropdownBtn?.classList.contains('show')) dropdownBtn.click();
                this.dashboardTour.moveNext();
              }
            }
          },
          {
            element: '#notification-wrapper',
            popover: {
              title: '🔔 Notifications Center',
              description: 'Get updates about orders, stock requests, etc.',
              position: 'bottom'
            }
          },
          {
            element: '#main-content',
            popover: {
              title: '🎯 Main Workspace',
              description: 'Manage orders, view analytics, and daily operations here.',
              position: 'left',
              onPopoverClick: () => {
                this.markTourCompleted();
                this.dashboardTour.destroy();
              }
            }
          }
        ],
        onDestroyed: () => {
          this.markTourCompleted();
          this.closeAllDropdowns();
        },
        onCloseClick: () => {
          this.markTourCompleted();
          this.dashboardTour.destroy();
        }
      });

      this.dashboardTour.drive();
    },

    closeAllDropdowns() {
      document.querySelectorAll('.dropdown-toggle').forEach(dropdown => {
        if (dropdown.classList.contains('show')) dropdown.click();
      });
    },

    markTourCompleted() {
      localStorage.setItem('tenant_dashboard_tour_shown', 'true');
    },

    restartTour() {
      localStorage.removeItem('tenant_dashboard_tour_shown');
      this.startDashboardTour();
    },

    setLang(lang) {
      this.selectedLang = lang;
    },

    initTooltips() {
      this.$nextTick(() => {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
          new Tooltip(el);
        });
      });
    },

    async fetchNotifications() {
      this.loadingNotifications = true;
      try {
        const res = await axios.get('/tenant/notifications');
        if (res.data.success) this.notifications = res.data.data;
      } catch (e) {}
      this.loadingNotifications = false;
    },

    async markNotificationRead(id) {
      await axios.post(`/tenant/notifications/${id}/read`);
      this.notifications = this.notifications.map(n => n.id === id ? { ...n, is_read: true } : n);
    },

    async markAllNotificationsRead() {
      try {
        await axios.post('/tenant/notifications/mark-all-read');
        this.notifications = this.notifications.map(n => ({ ...n, is_read: true }));
      } catch (e) {}
    },

    toggleNotificationDropdown() {
      this.showNotificationDropdown = !this.showNotificationDropdown;
    },

    closeNotificationDropdown() {
      this.showNotificationDropdown = false;
    },

    closeNotificationModal() {
      this.showNotificationModal = false;
    },

    handleClickOutsideDropdown(e) {
      const dropdown = document.querySelector('.notification-dropdown');
      const bell = document.querySelector('.fa-bell');
      if (dropdown && !dropdown.contains(e.target) && !bell?.contains(e.target)) {
        this.closeNotificationDropdown();
      }
    },

    async logout() {
      try {
        const response = await axios.post('/tenant/logout', {}, {
          headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        if (response.status === 200) {
          this.$router.push('/login');
          localStorage.removeItem('user');
          localStorage.removeItem('token');
          Swal.fire('Logout Success', 'You have been logged out', 'success');
        }
      } catch (error) {
        Swal.fire('Logout Failed', error.response?.data?.message || 'Something went wrong', 'error');
      }
    },

    async seeOrder(notification) {
      if (notification.order_id) {
        await this.markNotificationRead(notification.id);
        this.closeNotificationDropdown();
        this.$router.push(`/dashboard/orders?order_id=${notification.order_id}`);
      }
    },

    async fetchSettings() {
      try {
        const response = await axios.get("/tenant/settings");
        if (response.data.success && response.data.data) {
          this.setting = response.data.data;
        } else {
          Swal.fire({ icon: "error", title: "Error", text: "Invalid settings response" });
        }
      } catch (error) {
        Swal.fire({ icon: "error", title: "Error", text: "Unable to fetch settings" });
      }
    },

    async fetchCounts() {
      try {
        const ordersRes = await axios.get('/tenant/orders', { params: { per_page: 100 } });
        const orders = ordersRes.data.data.data || [];
        this.counts.orders = orders.filter(o => o.status !== 'delivered').length;
      } catch { this.counts.orders = 0; }

      try {
        const stockRes = await axios.get('/tenant/stock-check-reqs', { params: { per_page: 1 } });
        this.counts.stockCheckReqs = stockRes.data.data.total || 0;
      } catch { this.counts.stockCheckReqs = 0; }
    }
  }
};
</script>


<style scoped>
.tenant-dashboard-layout {
  --td-accent: #00844d;
  --td-accent-hover: #006b3f;
  --td-accent-soft: color-mix(in srgb, #00844d 10%, #fff);
  --td-ink: #0f172a;
  --td-muted: #64748b;
  --td-sidebar-w: 200px;
  --td-sidebar-collapsed-w: 62px;
  --td-surface-grey: #f1f5f9;
  min-height: 100vh;
  background: #fff;
  display: flex;
  flex-direction: column;
}

.td-help-btn,
.td-notify-btn {
  color: var(--td-accent) !important;
}
.td-help-btn:hover,
.td-notify-btn:hover {
  color: var(--td-accent-hover) !important;
}
.td-accent-icon {
  color: var(--td-accent);
}
.td-spinner {
  color: var(--td-accent);
}
.td-profile-avatar {
  background: var(--td-accent) !important;
}
/* Global text-xs and leading-tight utilities */
.text-xs {
  font-size: 0.875rem !important;
}
.leading-tight {
  line-height: 1.25 !important;
}

.td-header {
  background: #fff;
  color: var(--td-ink);
  min-height: 60px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
  box-shadow: 0 1px 0 rgba(15, 23, 42, 0.06);
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1050;
  border-bottom: 1px solid #e2e8f0;
  flex-wrap: nowrap;
  padding-top: 0.45rem;
  padding-bottom: 0.45rem;
}
.td-header__left {
  flex: 1 1 auto;
  min-width: 0;
  max-width: min(520px, 46vw);
}
@media (min-width: 1200px) {
  .td-header__left {
    max-width: min(560px, 40vw);
  }
}
.td-brand__logo-wrap {
  position: relative;
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: #fff;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
  border: 1px solid #e2e8f0;
  flex-shrink: 0;
}
.td-brand__logo-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 9px;
  display: block;
}
.td-brand__online {
  position: absolute;
  right: 2px;
  bottom: 2px;
  width: 9px;
  height: 9px;
  background: #22c55e;
  border-radius: 50%;
  border: 2px solid #fff;
  box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.35);
}
.td-brand__name {
  font-size: 0.95rem;
  font-weight: 800;
  color: var(--td-ink);
  letter-spacing: -0.02em;
  line-height: 1.2;
}
.td-brand__os {
  font-size: 0.7rem;
  color: var(--td-muted);
  line-height: 1.2;
  margin-top: 0.12rem;
}
.td-brand__rule {
  width: 1px;
  align-self: stretch;
  min-height: 36px;
  background: #e2e8f0;
}
.td-brand__page-title {
  font-size: 0.95rem;
  font-weight: 800;
  color: var(--td-ink);
  letter-spacing: -0.02em;
  line-height: 1.2;
}
.td-brand__page-sub {
  font-size: 0.7rem;
  color: var(--td-muted);
  margin-top: 0.12rem;
  line-height: 1.2;
}
.td-header__nav {
  flex: 1 1 auto;
  min-width: 0;
}
.td-header__right {
  margin-left: 0.25rem;
}
.td-profile-trigger.dropdown-toggle::after {
  margin-left: 0.1rem;
}
.td-profile-av {
  width: 36px;
  height: 36px;
}
.td-profile-av-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.td-header-lang {
  display: flex;
  align-items: center;
}
.td-header .btn-link {
  color: #222;
  font-size: 1.3rem;
}
.td-main {
  flex: 1 1 0%;
  min-height: 0;
  padding: 0;
  margin-top: 60px;
  height: calc(100vh - 60px);
  overflow: hidden;
  display: flex;
  align-items: stretch;
  background: #fff;
}
.td-sidebar {
  width: var(--td-sidebar-w);
  background: #fff;
  border-radius: 0;
  box-shadow: none;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  height: calc(100vh - 60px);
  flex-shrink: 0;
  position: sticky;
  top: 60px;
  align-self: flex-start;
  transition: width 0.2s ease, transform 0.25s ease;
  z-index: 1000;
  contain: layout style;
}
.td-sidebar--rail {
  border-right: 1px solid #e2e8f0;
}
.td-sidebar--collapsed {
  width: var(--td-sidebar-collapsed-w);
}
.td-sidebar-toolbar {
  align-items: center;
  justify-content: flex-end;
  padding: 0.35rem 0.4rem 0.15rem;
  border-bottom: 1px solid #f1f5f9;
  flex-shrink: 0;
}
.td-sidebar-collapse-btn {
  border: none;
  background: transparent;
  color: var(--td-muted);
  width: 2rem;
  height: 2rem;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}
.td-sidebar-collapse-btn:hover {
  background: var(--td-surface-grey);
  color: var(--td-accent);
}
.td-sidebar--collapsed .td-sidebar-toolbar {
  justify-content: center;
  padding-left: 0.25rem;
  padding-right: 0.25rem;
}
.td-sidebar--collapsed .td-nav-section {
  display: none;
}
.td-sidebar--collapsed .td-nav-link span:not(.badge) {
  display: none;
}
.td-sidebar--collapsed .td-nav {
  padding-left: 0.25rem;
  padding-right: 0.25rem;
}
.td-sidebar--collapsed .td-nav-link {
  justify-content: center;
  padding-left: 0.4rem;
  padding-right: 0.4rem;
  position: relative;
}
.td-sidebar--collapsed .td-nav-link .badge {
  position: absolute;
  top: 2px;
  right: 2px;
  font-size: 0.6rem;
  padding: 0.1em 0.35em;
}
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 999;
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
.sidebar-toggle-btn {
  border: none;
  background: transparent;
  cursor: pointer;
  transition: all 0.2s ease;
}
.sidebar-toggle-btn:hover {
  opacity: 0.7;
}
.sidebar-toggle-btn:focus {
  outline: none;
  box-shadow: none;
}
.sidebar-close-btn {
  border: none;
  background: transparent;
  cursor: pointer;
  transition: all 0.2s ease;
}
.sidebar-close-btn:hover {
  opacity: 0.7;
}
.sidebar-close-btn:focus {
  outline: none;
  box-shadow: none;
}
.td-sidebar-scroll {
  overflow-y: auto;
  overflow-x: hidden;
  height: 100%;
  padding-bottom: 1rem;
  -webkit-overflow-scrolling: touch;
}
.td-sidebar-scroll::-webkit-scrollbar {
  width: 6px;
}
.td-sidebar-scroll::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}
.td-sidebar-scroll::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 10px;
}
.td-sidebar-scroll::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
.td-user-info {
  background: #fafafa;
  border-radius: 12px 12px 0 0;
}
.td-avatar {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: #f0f0f0;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 0.5rem;
}
.td-user-name {
  font-size: 1.1rem;
  color: #444;
}
.td-nav {
  display: flex;
  flex-direction: column;
  gap: 0.12rem;
  padding: 0.35rem 0.35rem 0.75rem;
}
.td-nav-link {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  color: #334155;
  padding: 0.45rem 0.65rem;
  border-radius: 10px;
  font-size: 0.78rem;
  font-weight: 500;
  text-decoration: none;
  transition: background 0.15s ease, color 0.15s ease;
  white-space: nowrap;
  min-height: 34px;
  line-height: 1.25;
}
.td-nav-link i {
  font-size: 0.9rem;
  color: var(--td-accent);
  width: 1.1rem;
  text-align: center;
  flex-shrink: 0;
}
.td-nav-link span {
  flex: 1;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.35;
}
.td-nav-link .badge {
  flex-shrink: 0;
  font-size: 0.68rem;
  padding: 0.2rem 0.45rem;
  border-radius: 999px;
  font-weight: 600;
  background: var(--td-surface-grey) !important;
  color: #475569 !important;
}
.td-nav-link:hover:not(.active) {
  background: var(--td-surface-grey);
  color: var(--td-ink);
}
.td-nav-link:hover:not(.active) i {
  color: var(--td-accent);
}
.td-nav-link.active {
  background: color-mix(in srgb, var(--td-accent) 14%, #fff);
  color: var(--td-accent);
  font-weight: 700;
  box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--td-accent) 35%, #fff);
}
.td-nav-link.active i {
  color: var(--td-accent);
}
.td-content {
  min-width: 0;
  min-height: 0;
  background: #fff;
  border-radius: 0;
  box-shadow: none;
  margin-left: 0;
  flex: 1 1 0%;
  height: calc(100vh - 60px);
  overflow-y: auto;
  overflow-x: hidden;
  -webkit-overflow-scrolling: touch;
  padding: 1.1rem 1.25rem 2rem;
}
.td-content::-webkit-scrollbar {
  width: 8px;
}
.td-content::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}
.td-content::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 10px;
}
.td-content::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Desktop - Sidebar always visible */
@media (min-width: 992px) {
  .td-sidebar {
    transform: translateX(0) !important;
    position: sticky !important;
    top: 60px;
    height: calc(100vh - 60px);
  }
  .sidebar-overlay {
    display: none !important;
  }
}

/* Large Tablets (1024px - 1199px) */
@media (min-width: 992px) and (max-width: 1199.98px) {
  .td-sidebar {
    width: var(--td-sidebar-w);
  }
  .td-main {
    padding-left: 0;
    padding-right: 0;
  }
  .td-content {
    padding: 1.25rem 1.25rem 1.5rem;
  }
  .td-header {
    padding-left: 1rem;
    padding-right: 1rem;
  }
  .td-header-logo {
    height: 40px;
  }
  .td-header .d-none.d-md-block {
    display: none !important;
  }
  .navbar-nav .nav-link {
    font-size: 0.95rem;
    padding-left: 0.75rem;
    padding-right: 0.75rem;
  }
  .mega-menu-dropdown {
    min-width: 450px !important;
  }
}

/* Tablets (768px - 1023px) */
@media (min-width: 768px) and (max-width: 991.98px) {
  .td-main {
    flex-direction: row;
    align-items: flex-start;
    padding: 1rem 0.75rem;
    padding-top: 1.5rem;
    margin-top: 60px;
    height: calc(100vh - 60px);
    overflow: hidden;
  }
  .td-sidebar {
    width: var(--td-sidebar-w);
    margin-right: 0;
    margin-bottom: 0;
    position: fixed;
    top: 60px;
    left: 0;
    height: calc(100vh - 60px);
    flex-shrink: 0;
    transform: translateX(-100%);
    border-radius: 0;
    z-index: 1000;
    box-shadow: 2px 0 8px rgba(0,0,0,0.1);
  }
  .td-sidebar.sidebar-open {
    transform: translateX(0);
  }
  .td-sidebar-scroll {
    height: 100%;
    max-height: 100%;
  }
  .td-content {
    margin-left: 0;
    padding: 1.25rem;
    height: calc(100vh - 60px);
    overflow-y: auto;
    overflow-x: hidden;
    flex: 1 1 0%;
  }
  .td-nav {
    padding: 0.5rem;
    gap: 0.2rem;
  }
  .td-nav-link {
    padding: 0.625rem 0.75rem;
    font-size: 0.9rem;
    gap: 0.625rem;
    min-height: 40px;
  }
  .td-nav-link i {
    font-size: 1rem;
    width: 18px;
  }
  .td-nav-link span {
    font-size: 0.9rem;
  }
  .td-header {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
    height: auto;
    min-height: 56px;
  }
  .td-header-logo {
    height: 38px;
  }
  .td-header .d-none.d-md-block {
    display: none !important;
  }
  .navbar {
    flex-wrap: wrap;
  }
  .navbar-nav {
    flex-wrap: wrap;
    justify-content: center;
  }
  .navbar-nav .nav-link {
    font-size: 0.9rem;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
  }
  .mega-menu-dropdown {
    min-width: 400px !important;
    max-width: 90vw;
  }
  .notification-dropdown {
    min-width: 300px !important;
    max-width: 90vw;
  }
}

/* Small Tablets and Large Phones (576px - 767px) */
@media (min-width: 576px) and (max-width: 767.98px) {
  .td-main {
    flex-direction: column;
    align-items: stretch;
    padding: 1rem 0.5rem;
    height: auto;
    overflow: visible;
  }
  .td-sidebar {
    width: 280px;
    margin-bottom: 0;
    margin-right: 0;
    position: fixed;
    top: 60px;
    left: 0;
    height: calc(100vh - 60px);
    max-height: none;
    transform: translateX(-100%);
    border-radius: 0;
    z-index: 1000;
    box-shadow: 2px 0 8px rgba(0,0,0,0.1);
  }
  .td-sidebar.sidebar-open {
    transform: translateX(0);
  }
  .td-sidebar-scroll {
    max-height: 350px;
  }
  .td-content {
    margin-left: 0;
    padding: 1rem;
    height: auto;
    overflow: visible;
    border-radius: 0;
  }
  .td-nav {
    padding: 0.5rem;
    gap: 0.15rem;
  }
  .td-nav-link {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
    gap: 0.625rem;
    min-height: 40px;
  }
  .td-nav-link i {
    font-size: 1rem;
    width: 16px;
  }
  .td-nav-link span {
    white-space: normal;
    word-break: break-word;
    font-size: 0.9rem;
  }
  .td-nav-link .badge {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
  }
  .td-header {
    padding-left: 0.75rem;
    padding-right: 0.75rem;
    flex-wrap: wrap;
  }
  .td-header .d-none.d-md-block {
    display: none !important;
  }
  .navbar-nav {
    order: 3;
    width: 100%;
    margin-top: 0.5rem;
    flex-wrap: wrap;
  }
  .navbar-nav .nav-link {
    font-size: 0.85rem;
    padding: 0.25rem 0.5rem;
  }
  .mega-menu-dropdown {
    min-width: 100% !important;
    left: 0 !important;
    right: 0 !important;
  }
}

/* Mobile Phones (< 576px) */
@media (max-width: 575.98px) {
  .td-main {
    flex-direction: column;
    align-items: stretch;
    padding: 0.75rem 0.5rem;
    height: auto;
    overflow: visible;
  }
  .td-sidebar {
    width: 260px;
    margin-bottom: 0;
    margin-right: 0;
    position: fixed;
    top: 60px;
    left: 0;
    height: calc(100vh - 60px);
    max-height: none;
    transform: translateX(-100%);
    border-radius: 0;
    z-index: 1000;
    box-shadow: 2px 0 8px rgba(0,0,0,0.1);
  }
  .td-sidebar.sidebar-open {
    transform: translateX(0);
  }
  .td-sidebar-scroll {
    max-height: 300px;
  }
  .td-content {
    margin-left: 0;
    padding: 0.75rem;
    height: auto;
    overflow: visible;
    border-radius: 0;
  }
  .td-nav {
    padding: 0.375rem;
    gap: 0.1rem;
  }
  .td-nav-link {
    padding: 0.5rem 0.625rem;
    font-size: 0.85rem;
    gap: 0.5rem;
    min-height: 38px;
  }
  .td-nav-link i {
    font-size: 0.95rem;
    width: 15px;
  }
  .td-nav-link span {
    white-space: normal;
    word-break: break-word;
    font-size: 0.85rem;
  }
  .td-nav-link .badge {
    font-size: 0.65rem;
    padding: 0.15rem 0.35rem;
  }
  .td-header {
    padding-left: 0.5rem;
    padding-right: 0.5rem;
    height: auto;
    min-height: 56px;
    flex-wrap: wrap;
  }
  .td-header-logo {
    height: 36px !important;
  }
  .td-header .d-none.d-md-block {
    display: none !important;
  }
  .navbar-nav {
    order: 3;
    width: 100%;
    margin-top: 0.5rem;
    flex-wrap: wrap;
    justify-content: center;
  }
  .navbar-nav .nav-link {
    font-size: 0.8rem;
    padding: 0.25rem 0.4rem;
  }
  .mega-menu-dropdown {
    min-width: 100% !important;
    left: 0 !important;
    right: 0 !important;
    padding: 1rem !important;
  }
  .mega-menu-dropdown .row {
    min-width: 100% !important;
  }
  .notification-dropdown {
    min-width: 90vw !important;
    max-width: 90vw !important;
  }
}
.td-nav-section {
  font-size: 0.65rem;
  color: var(--td-muted);
  font-weight: 700;
  margin: 1rem 0.875rem 0.35rem;
  letter-spacing: 0.06em;
  text-transform: uppercase;
}
.td-nav-section:first-of-type {
  margin-top: 0.5rem;
}
.navbar-nav .nav-link {
  color: #222;
  font-weight: 500;
  font-size: 0.75rem;
  line-height: 1.25;
  transition: color 0.2s;
  padding: 0.375rem 0.75rem;
}
.navbar-nav .nav-link.active,
.navbar-nav .nav-link.router-link-active,
.navbar-nav .nav-link:hover {
  color: var(--td-accent);
}
.navbar-nav .dropdown-menu {
  min-width: 160px;
  font-size: 0.75rem;
  line-height: 1.25;
}
.lang-switcher-group {
  position: relative;
}
.lang-switcher-btn {
  font-weight: 500;
  color: #222;
  background: #fff;
  border: 1.5px solid #e0e0e0;
  border-radius: 7px;
  padding: 5px 18px 5px 12px;
  min-width: 120px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  transition: border 0.15s, box-shadow 0.15s;
  font-size: 1.08rem;
  outline: none;
}
.lang-switcher-btn:focus, .lang-switcher-btn:hover {
  border: 1.5px solid var(--td-accent);
  box-shadow: 0 4px 16px rgba(0, 132, 77, 0.12);
  color: var(--td-accent);
  background: #fafafa;
}
.lang-switcher-label {
  font-size: 1.08rem;
  font-weight: 500;
  color: #222;
}
.lang-switcher-dropdown {
  min-width: 160px;
  border-radius: 10px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.12);
  border: 1px solid #eee;
  padding: 4px 0;
  margin-top: 4px;
}
.lang-switcher-dropdown-label {
  font-size: 1.07rem;
  color: #222;
  font-weight: 500;
}
.dropdown-item.active, .dropdown-item:active, .dropdown-item:focus, .dropdown-item:hover {
  background: var(--td-accent-soft);
  color: var(--td-accent);
}
.modal {
  display: block;
  background: rgba(0,0,0,0.25);
  z-index: 3000;
}
.modal-backdrop {
  z-index: 2999;
}
.notification-dropdown {
  min-width: 320px;
  max-width: 95vw;
  z-index: 2000;
  right: 0 !important;
  border-radius: 12px;
  border: 1px solid #eee;
  box-shadow: 0 4px 24px rgba(0,0,0,0.10);
  background: #fff;
}
.notification-item {
  transition: background 0.2s;
  border-radius: 0;
}
.notification-item:hover {
  background: #f8f9fa !important;
}
.notification-item:last-child {
  border-bottom: none !important;
}
.notification-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: linear-gradient(135deg, #00844d 60%, #006b3f 100%);
  color: #fff;
  border-radius: 50%;
  min-width: 26px;
  height: 26px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.08rem;
  font-weight: bold;
  box-shadow: 0 2px 8px rgba(0, 132, 77, 0.25);
  border: 2px solid #fff;
  z-index: 10;
  transition: background 0.2s, box-shadow 0.2s;
}
.notification-badge:hover {
  background: linear-gradient(135deg, #006b3f 60%, #00844d 100%);
  box-shadow: 0 4px 16px rgba(0, 132, 77, 0.3);
}

/* Specific fixes for tour elements */
#notification-wrapper {
  position: relative;
  display: inline-block;
}

#profile-dropdown-container {
  position: relative;
  display: inline-block;
}

/* Profile dropdown menu responsive styles */
#profile-dropdown-container .dropdown-menu {
  min-width: 220px;
  max-width: 95vw;
  border-radius: 8px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  border: 1px solid #eee;
  padding: 0.5rem 0;
  margin-top: 0.5rem;
}

#profile-dropdown-container .dropdown-item {
  padding: 0.5rem 0.875rem;
  font-size: 0.75rem;
  line-height: 1.25;
  transition: all 0.2s ease;
  white-space: nowrap;
  min-height: 32px;
}

#profile-dropdown-container .dropdown-item i {
  width: 16px;
  text-align: center;
  font-size: 0.875rem;
}

#profile-dropdown-container .dropdown-item span {
  flex: 1;
  overflow: hidden;
  text-overflow: ellipsis;
}

#profile-dropdown-container .dropdown-item:hover,
#profile-dropdown-container .dropdown-item:focus {
  background-color: var(--td-accent-soft);
  color: var(--td-accent);
}

#profile-dropdown-container .dropdown-divider {
  margin: 0.5rem 0;
  border-color: #eee;
}

/* Responsive adjustments for mobile devices */
@media (max-width: 768px) {
  #profile-dropdown-container .dropdown-menu {
    min-width: 200px;
    max-width: 90vw;
    font-size: 0.9rem;
  }

  #profile-dropdown-container .dropdown-item {
    padding: 0.5rem 0.875rem;
    font-size: 0.9rem;
    min-height: 38px;
  }

  #profile-dropdown-container .dropdown-item i {
    font-size: 0.95rem;
    width: 16px;
  }
}

@media (max-width: 576px) {
  #profile-dropdown-container .dropdown-menu {
    min-width: 180px;
    max-width: 85vw;
  }

  #profile-dropdown-container .dropdown-item {
    padding: 0.5rem 0.75rem;
    font-size: 0.85rem;
  }
}
</style>

<style>
/* Enhanced Driver.js Tour Styles */
.driver-popover.custom-driver-popover {
  border-radius: 12px !important;
  box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important;
  border: 1px solid #e0e0e0 !important;
  max-width: 320px !important;
  z-index: 10000 !important;
}

.driver-popover-title {
  font-weight: 600 !important;
  color: #00844d !important;
  font-size: 1.2rem !important;
  margin-bottom: 8px !important;
}

.driver-popover-description {
  font-size: 1rem !important;
  line-height: 1.5 !important;
  color: #444 !important;
  margin-bottom: 0 !important;
}

.driver-popover-footer {
  padding: 12px 16px !important;
  margin-top: 12px !important;
}

.driver-popover-btn {
  border-radius: 8px !important;
  padding: 8px 16px !important;
  font-weight: 500 !important;
  transition: all 0.2s ease !important;
  font-size: 0.9rem !important;
}

.driver-popover-btn.driver-popover-next-btn {
  background: #00844d !important;
  color: white !important;
}

.driver-popover-btn.driver-popover-next-btn:hover {
  background: #006b3f !important;
  transform: translateY(-1px);
}

.driver-popover-btn.driver-popover-prev-btn {
  border: 1px solid #e0e0e0 !important;
  color: #666 !important;
}

.driver-popover-btn.driver-popover-close-btn {
  color: #666 !important;
  font-size: 1.2rem !important;
}

/* Enhanced highlight styling */
.driver-highlighted-element {
  border-radius: 8px !important;
  border: 2px solid #00844d !important;
  box-shadow: 0 0 0 4px rgba(0, 132, 77, 0.12) !important;
}

.driver-highlight-active {
  z-index: 9999 !important;
  position: relative !important;
}

/* Fix for overlay positioning */
.driver-overlay {
  z-index: 9998 !important;
}

/* Specific fixes for your elements */
#profile-dropdown-container.driver-highlighted-element {
  transform: scale(1.05);
  transition: transform 0.2s ease;
}

#notification-wrapper.driver-highlighted-element {
  transform: scale(1.05);
  transition: transform 0.2s ease;
}

#main-navigation.driver-highlighted-element {
  margin: 4px !important;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
  .driver-popover.custom-driver-popover {
    max-width: 280px !important;
    margin: 10px !important;
  }

  .driver-popover-title {
    font-size: 1.1rem !important;
  }

  .driver-popover-description {
    font-size: 0.95rem !important;
  }
}
</style>
