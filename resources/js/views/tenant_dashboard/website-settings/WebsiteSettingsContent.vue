<template>
  <div class="wsc">
    <header class="wsc__head">
      <div class="wsc__icon"><i :class="head.icon"></i></div>
      <div>
        <h1 class="wsc__title">{{ head.title }}</h1>
        <p class="wsc__sub">{{ head.sub }}</p>
      </div>
    </header>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-success" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Template / Layout Selection -->
    <div v-else-if="tab === 'template'" class="wsc__body">
      <p class="text-muted small mb-3">Choose the design template for your restaurant website.</p>
      <div class="row g-3">
        <div v-for="theme in themes" :key="theme.id" class="col-md-3">
          <div 
            class="tpl" 
            :class="{ 'tpl--on': form.theme === theme.id }"
            @click="selectTheme(theme.id)"
            role="button"
          >
            <div class="tpl__mock" :style="{ background: theme.preview_color }"></div>
            <div class="fw-bold small mt-2 text-capitalize">{{ theme.name }}</div>
            <p class="small text-muted mb-0">{{ theme.description }}</p>
            <i v-if="form.theme === theme.id" class="fas fa-check-circle tpl__check text-success"></i>
          </div>
        </div>
      </div>
      <div class="alert alert-info mt-3 small">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Note:</strong> After saving, refresh your frontend to see the new theme.
      </div>
    </div>

    <!-- Branding -->
    <div v-else-if="tab === 'branding'" class="wsc__body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted text-uppercase">Restaurant name</label>
          <input class="form-control" v-model="form.business_name" placeholder="Your Restaurant" />
        </div>
        <div class="col-md-6">
          <label class="form-label small fw-bold text-muted text-uppercase">Tagline</label>
          <input class="form-control" v-model="form.tagline" placeholder="Quality food, every day" />
        </div>
        <div class="col-12">
          <label class="form-label small fw-bold text-muted text-uppercase">Description</label>
          <textarea class="form-control" rows="3" v-model="form.description" placeholder="Short public description"></textarea>
        </div>
      </div>
      <p class="small fw-bold text-muted text-uppercase mt-4 mb-2">Logo &amp; favicon</p>
      <div class="row g-3">
        <div class="col-md-6 border rounded-3 p-3">
          <label class="form-label small">Logo</label>
          <div v-if="form.logo_url" class="mb-2">
            <img :src="form.logo_url" alt="Logo" class="img-thumbnail" style="max-height: 80px" />
          </div>
          <input type="file" class="form-control form-control-sm" accept="image/*" @change="handleLogoUpload" />
        </div>
        <div class="col-md-6 border rounded-3 p-3">
          <label class="form-label small">Favicon</label>
          <div v-if="form.favicon_url" class="mb-2">
            <img :src="form.favicon_url" alt="Favicon" class="img-thumbnail" style="max-height: 32px" />
          </div>
          <input type="file" class="form-control form-control-sm" accept=".png,.ico" @change="handleFaviconUpload" />
        </div>
      </div>
    </div>

    <!-- SEO -->
    <div v-else-if="tab === 'seo'" class="wsc__body">
      <p class="small text-muted mb-3">These fields power search snippets and social cards.</p>
      <div class="mb-3">
        <label class="form-label fw-bold">Page title</label>
        <input class="form-control" v-model="form.meta_title" placeholder="Your restaurant — Order online" />
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold">Meta description</label>
        <textarea class="form-control" rows="3" v-model="form.meta_description" placeholder="Describe your restaurant..."></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold">Keywords</label>
        <input class="form-control" v-model="form.meta_keywords" placeholder="restaurant, food, delivery" />
      </div>
      <div class="d-flex justify-content-between align-items-center border rounded-3 p-3">
        <div>
          <div class="fw-bold small">Hide from search engines</div>
          <div class="text-muted small">When on, adds noindex.</div>
        </div>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" type="checkbox" v-model="form.hide_from_search" id="seoNo" />
          <label class="form-check-label small" for="seoNo">{{ form.hide_from_search ? 'Hidden' : 'Visible' }}</label>
        </div>
      </div>
    </div>

    <!-- Contact -->
    <div v-else-if="tab === 'contact'" class="wsc__body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Phone</label>
          <input class="form-control" v-model="form.public_phone" placeholder="+1 234 567 8900" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input class="form-control" type="email" v-model="form.public_email" placeholder="info@restaurant.com" />
        </div>
        <div class="col-12">
          <label class="form-label">Address</label>
          <textarea class="form-control" rows="2" v-model="form.address" placeholder="123 Main St, City"></textarea>
        </div>
      </div>
    </div>

    <!-- Hero -->
    <div v-else-if="tab === 'hero'" class="wsc__body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Hero headline</label>
          <input class="form-control" v-model="form.hero_headline" placeholder="Welcome to our kitchen" />
        </div>
        <div class="col-md-6">
          <label class="form-label">Hero subheadline</label>
          <input class="form-control" v-model="form.hero_subheadline" placeholder="Fresh food, made with love" />
        </div>
        <div class="col-md-6">
          <label class="form-label">CTA Button Text</label>
          <input class="form-control" v-model="form.hero_cta_text" placeholder="Order Now" />
        </div>
        <div class="col-md-6">
          <label class="form-label">CTA Link</label>
          <input class="form-control" v-model="form.hero_cta_link" placeholder="/menu" />
        </div>
        <div class="col-12">
          <label class="form-label">Hero background image</label>
          <div v-if="form.hero_image_url" class="mb-2">
            <img :src="form.hero_image_url" alt="Hero" class="img-thumbnail" style="max-height: 150px" />
          </div>
          <input type="file" class="form-control" accept="image/*" @change="handleHeroImageUpload" />
          <small class="text-muted">Recommended: 1920x600px or larger</small>
        </div>
      </div>

      <!-- Banner Management -->
      <div class="mt-4 pt-4 border-top">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h6 class="mb-0">
            <i class="fas fa-images me-2"></i>
            Hero Slider Banners
          </h6>
          <button type="button" class="btn btn-sm btn-success" @click="showBannerModal = true">
            <i class="fas fa-plus me-1"></i> Add Banner
          </button>
        </div>
        
        <div v-if="banners.length === 0" class="text-center py-4 bg-light rounded-3">
          <i class="fas fa-image fa-2x text-muted mb-2"></i>
          <p class="text-muted mb-0">No banners yet. Add your first slider banner.</p>
        </div>
        
        <div v-else class="banner-list">
          <div v-for="banner in banners" :key="banner.id" class="banner-item d-flex align-items-center gap-3 p-2 border rounded-3 mb-2">
            <img :src="banner.image_url" :alt="banner.title" class="banner-thumb" />
            <div class="flex-grow-1">
              <div class="fw-bold small">{{ banner.title }}</div>
              <div class="text-muted small">{{ banner.subtitle }}</div>
            </div>
            <div class="badge" :class="banner.is_active ? 'bg-success' : 'bg-secondary'">
              {{ banner.is_active ? 'Active' : 'Inactive' }}
            </div>
            <div class="btn-group btn-group-sm">
              <button type="button" class="btn btn-outline-secondary" @click="editBanner(banner)">
                <i class="fas fa-edit"></i>
              </button>
              <button type="button" class="btn btn-outline-danger" @click="deleteBanner(banner)">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Theme / Colors -->
    <div v-else-if="tab === 'theme'" class="wsc__body">
      <div class="row g-4 align-items-start">
        <div class="col-md-7">
          <div class="mb-3">
            <label class="form-label">Primary color</label>
            <div class="d-flex gap-2 align-items-center">
              <input type="color" class="form-control form-control-color" v-model="form.primary_color" />
              <input class="form-control" v-model="form.primary_color" />
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Secondary color</label>
            <div class="d-flex gap-2 align-items-center">
              <input type="color" class="form-control form-control-color" v-model="form.secondary_color" />
              <input class="form-control" v-model="form.secondary_color" />
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Accent color</label>
            <div class="d-flex gap-2 align-items-center">
              <input type="color" class="form-control form-control-color" v-model="form.accent_color" />
              <input class="form-control" v-model="form.accent_color" />
            </div>
          </div>
        </div>
        <div class="col-md-5">
          <div class="nav-preview small fw-bold text-muted mb-2">Navbar preview</div>
          <div class="nav-preview__bar" :style="{ background: form.primary_color }">
            <span class="text-white small fw-bold">{{ form.business_name || 'Your brand' }}</span>
            <span class="nav-preview__btn">Order now</span>
          </div>
          <div class="nav-preview__accent" :style="{ background: form.accent_color }"></div>
        </div>
      </div>
    </div>

    <!-- Social -->
    <div v-else-if="tab === 'social'" class="wsc__body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label"><i class="fab fa-facebook me-2"></i>Facebook URL</label>
          <input class="form-control" v-model="form.facebook_link" placeholder="https://facebook.com/..." />
        </div>
        <div class="col-md-6">
          <label class="form-label"><i class="fab fa-instagram me-2"></i>Instagram URL</label>
          <input class="form-control" v-model="form.instagram_link" placeholder="https://instagram.com/..." />
        </div>
        <div class="col-md-6">
          <label class="form-label"><i class="fab fa-twitter me-2"></i>Twitter URL</label>
          <input class="form-control" v-model="form.twitter_link" placeholder="https://twitter.com/..." />
        </div>
        <div class="col-md-6">
          <label class="form-label"><i class="fab fa-tiktok me-2"></i>TikTok URL</label>
          <input class="form-control" v-model="form.tiktok_link" placeholder="https://tiktok.com/..." />
        </div>
        <div class="col-md-6">
          <label class="form-label"><i class="fab fa-youtube me-2"></i>YouTube URL</label>
          <input class="form-control" v-model="form.youtube_link" placeholder="https://youtube.com/..." />
        </div>
      </div>
    </div>

    <!-- Hours -->
    <div v-else-if="tab === 'hours'" class="wsc__body">
      <p class="text-muted small">Weekly opening hours — configure in Business Settings &gt; Branches.</p>
      <div class="border rounded-3 p-3 bg-light small text-muted">
        <i class="fas fa-info-circle me-2"></i>
        Opening hours are managed per branch in Business Settings.
      </div>
    </div>

    <!-- Sections -->
    <div v-else-if="tab === 'sections'" class="wsc__body">
      <p class="text-muted small mb-3">Toggle homepage sections visibility.</p>
      <ul class="list-group">
        <li v-for="(enabled, section) in form.visible_sections" :key="section" class="list-group-item d-flex justify-content-between align-items-center">
          <span class="text-capitalize">{{ section.replace('_', ' ') }}</span>
          <div class="form-check form-switch m-0">
            <input class="form-check-input" type="checkbox" v-model="form.visible_sections[section]" />
          </div>
        </li>
      </ul>
    </div>

    <!-- Preferences -->
    <div v-else-if="tab === 'preferences'" class="wsc__body">
      <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" v-model="form.show_cookie_banner" id="p1" />
        <label class="form-check-label" for="p1">Show cookie banner</label>
      </div>
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" v-model="form.maintenance_mode" id="p2" />
        <label class="form-check-label" for="p2">
          Maintenance mode
          <span v-if="form.maintenance_mode" class="badge bg-warning ms-2">Site is offline</span>
        </label>
      </div>
    </div>

    <!-- Domain (Static) -->
    <div v-else-if="tab === 'domain'" class="wsc__body">
      <label class="form-label fw-bold">Custom domain</label>
      <div class="input-group mb-2">
        <input class="form-control" placeholder="orders.yourrestaurant.com" readonly />
        <button type="button" class="btn btn-outline-secondary" disabled>Check DNS</button>
      </div>
      <p class="small text-muted">Enter domain without https://. Custom domains coming soon.</p>
      <div class="alert alert-info small">
        <i class="fas fa-info-circle me-2"></i>
        Your current URL: <strong>{{ currentUrl }}</strong>
      </div>
    </div>

    <div class="wsc__foot">
      <button type="button" class="btn wsc__save" :disabled="saving" @click="saveSettings">
        <span v-if="saving" class="spinner-border spinner-border-sm me-2"></span>
        <i v-else class="fas fa-save me-2" aria-hidden="true"></i>
        Save
      </button>
      <span class="text-muted small ms-3">Settings apply to all themes. Active theme: <strong>{{ form.theme }}</strong></span>
    </div>

    <!-- Banner Modal -->
    <div v-if="showBannerModal" class="modal fade show d-block" style="background: rgba(0,0,0,0.5)">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingBanner ? 'Edit' : 'Add' }} Banner</h5>
            <button type="button" class="btn-close" @click="closeBannerModal"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Title *</label>
                <input class="form-control" v-model="bannerForm.title" required />
              </div>
              <div class="col-md-6">
                <label class="form-label">Subtitle</label>
                <input class="form-control" v-model="bannerForm.subtitle" />
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea class="form-control" rows="2" v-model="bannerForm.description"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Image *</label>
                <input type="file" class="form-control" accept="image/*" @change="handleBannerImage" />
                <div v-if="bannerPreview || bannerForm.image_url" class="mt-2">
                  <img :src="bannerPreview || bannerForm.image_url" class="img-thumbnail" style="max-height: 100px" />
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Price (optional)</label>
                <input class="form-control" v-model="bannerForm.price" placeholder="Rs 550" />
              </div>
              <div class="col-md-6">
                <label class="form-label">CTA Text</label>
                <input class="form-control" v-model="bannerForm.cta_text" placeholder="Order Now" />
              </div>
              <div class="col-md-6">
                <label class="form-label">CTA Link</label>
                <input class="form-control" v-model="bannerForm.cta_link" placeholder="/menu" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Badge</label>
                <input class="form-control" v-model="bannerForm.badge" placeholder="NEW" />
              </div>
              <div class="col-md-6">
                <div class="form-check form-switch mt-4">
                  <input class="form-check-input" type="checkbox" v-model="bannerForm.is_active" id="bannerActive" />
                  <label class="form-check-label" for="bannerActive">Active</label>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeBannerModal">Cancel</button>
            <button type="button" class="btn btn-success" :disabled="savingBanner" @click="saveBanner">
              <span v-if="savingBanner" class="spinner-border spinner-border-sm me-2"></span>
              {{ editingBanner ? 'Update' : 'Create' }} Banner
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useSettingsSectionProgress } from '../../../composables/useSettingsSectionProgress'
import axios from 'axios'
import Swal from 'sweetalert2'

const route = useRoute()
const tab = computed(() => route.meta.websiteTab || 'template')
const { markDone } = useSettingsSectionProgress()

const loading = ref(true)
const saving = ref(false)
const themes = ref([])
const banners = ref([])

const form = ref({
  theme: 'classic',
  business_name: '',
  tagline: '',
  description: '',
  logo: null,
  logo_url: null,
  favicon: null,
  favicon_url: null,
  hero_headline: '',
  hero_subheadline: '',
  hero_image: null,
  hero_image_url: null,
  hero_cta_text: 'Order Now',
  hero_cta_link: '/menu',
  primary_color: '#E31837',
  secondary_color: '#1A1A1A',
  accent_color: '#FFD700',
  meta_title: '',
  meta_description: '',
  meta_keywords: '',
  hide_from_search: false,
  facebook_link: '',
  instagram_link: '',
  twitter_link: '',
  tiktok_link: '',
  youtube_link: '',
  public_phone: '',
  public_email: '',
  address: '',
  show_cookie_banner: true,
  maintenance_mode: false,
  visible_sections: {
    hero: true,
    categories: true,
    best_sellers: true,
    deals: true,
    promotions: true,
    gallery: false,
    testimonials: false,
  }
})

const currentUrl = computed(() => window.location.origin)

const heads = {
  template: { title: 'Storefront layout', sub: 'Pick how your online menu and homepage feel to guests.', icon: 'fas fa-layer-group' },
  branding: { title: 'Brand & story', sub: 'Name, logo, and short story guests see on your public site.', icon: 'fas fa-palette' },
  seo: { title: 'Findability', sub: 'Search and social previews so diners can find you.', icon: 'fas fa-search' },
  domain: { title: 'Custom domain', sub: 'Point your own web address to this ordering site.', icon: 'fas fa-link' },
  contact: { title: 'Guest contact', sub: 'Phone, email, and address shown to customers.', icon: 'fas fa-address-book' },
  hero: { title: 'Home hero', sub: 'Top banner on your homepage — offers, photos, or welcome text.', icon: 'fas fa-image' },
  theme: { title: 'Colors & theme', sub: 'Primary and accent colors for your guest-facing site.', icon: 'fas fa-star' },
  social: { title: 'Social links', sub: 'Instagram, Facebook, and other profiles.', icon: 'fas fa-share-alt' },
  hours: { title: 'Hours & service', sub: 'When you welcome dine-in, pickup, or delivery.', icon: 'fas fa-clock' },
  sections: { title: 'Page sections', sub: 'Turn homepage blocks like menu highlights or gallery on or off.', icon: 'fas fa-th-large' },
  preferences: { title: 'Site preferences', sub: 'Cookie banner, maintenance, and other behaviour.', icon: 'fas fa-sliders-h' }
}
const head = computed(() => heads[tab.value] || heads.template)

// Banner management
const showBannerModal = ref(false)
const editingBanner = ref(null)
const savingBanner = ref(false)
const bannerPreview = ref(null)
const bannerFile = ref(null)
const bannerForm = ref({
  title: '',
  subtitle: '',
  description: '',
  image_url: null,
  price: '',
  cta_text: 'Order Now',
  cta_link: '/menu',
  badge: '',
  is_active: true
})

async function fetchThemes() {
  try {
    const res = await axios.get('/tenant/website-settings/themes')
    if (res.data.success) {
      themes.value = res.data.data
    }
  } catch (e) {
    console.error('Failed to fetch themes:', e)
  }
}

async function fetchSettings() {
  loading.value = true
  try {
    const res = await axios.get('/tenant/website-settings')
    if (res.data.success && res.data.data) {
      Object.assign(form.value, res.data.data)
      // Ensure theme is set (fallback to window.TENANT_THEME if not in response)
      if (!form.value.theme && typeof window !== 'undefined' && window.TENANT_THEME) {
        form.value.theme = window.TENANT_THEME
      }
    }
  } catch (e) {
    console.error('Failed to fetch website settings:', e)
    // Fallback to window values
    if (typeof window !== 'undefined' && window.TENANT_THEME) {
      form.value.theme = window.TENANT_THEME
    }
  } finally {
    loading.value = false
  }
}

async function fetchBanners() {
  try {
    const res = await axios.get('/tenant/banners', { params: { position: 'hero' } })
    if (res.data.success) {
      banners.value = res.data.data
    }
  } catch (e) {
    console.error('Failed to fetch banners:', e)
  }
}

function selectTheme(themeId) {
  form.value.theme = themeId
}

async function saveSettings() {
  saving.value = true
  try {
    const res = await axios.put('/tenant/website-settings', form.value)
    if (res.data.success) {
      markDone('website', tab.value)
      Swal.fire({
        icon: 'success',
        title: 'Saved!',
        text: 'Website settings updated successfully.',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
      })
    }
  } catch (e) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: e.response?.data?.message || 'Failed to save settings'
    })
  } finally {
    saving.value = false
  }
}

async function handleLogoUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  
  const formData = new FormData()
  formData.append('logo', file)
  
  try {
    const res = await axios.post('/tenant/upload-logo', formData)
    if (res.data.success) {
      await fetchSettings()
      Swal.fire({ icon: 'success', title: 'Logo uploaded!', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false })
    }
  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Upload failed', text: e.response?.data?.message || 'Failed to upload logo' })
  }
}

async function handleFaviconUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  
  const formData = new FormData()
  formData.append('favicon', file)
  
  try {
    const res = await axios.post('/tenant/website-settings/favicon', formData)
    if (res.data.success) {
      form.value.favicon = res.data.data.favicon
      form.value.favicon_url = res.data.data.favicon_url
      Swal.fire({ icon: 'success', title: 'Favicon uploaded!', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false })
    }
  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Upload failed', text: e.response?.data?.message || 'Failed to upload favicon' })
  }
}

async function handleHeroImageUpload(e) {
  const file = e.target.files[0]
  if (!file) return
  
  const formData = new FormData()
  formData.append('hero_image', file)
  
  try {
    const res = await axios.post('/tenant/website-settings/hero-image', formData)
    if (res.data.success) {
      form.value.hero_image = res.data.data.hero_image
      form.value.hero_image_url = res.data.data.hero_image_url
      Swal.fire({ icon: 'success', title: 'Hero image uploaded!', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false })
    }
  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Upload failed', text: e.response?.data?.message || 'Failed to upload hero image' })
  }
}

function handleBannerImage(e) {
  const file = e.target.files[0]
  if (!file) return
  bannerFile.value = file
  bannerPreview.value = URL.createObjectURL(file)
}

function editBanner(banner) {
  editingBanner.value = banner
  bannerForm.value = { ...banner }
  bannerPreview.value = null
  bannerFile.value = null
  showBannerModal.value = true
}

function closeBannerModal() {
  showBannerModal.value = false
  editingBanner.value = null
  bannerFile.value = null
  bannerPreview.value = null
  bannerForm.value = {
    title: '',
    subtitle: '',
    description: '',
    image_url: null,
    price: '',
    cta_text: 'Order Now',
    cta_link: '/menu',
    badge: '',
    is_active: true
  }
}

async function saveBanner() {
  if (!bannerForm.value.title || (!bannerFile.value && !editingBanner.value)) {
    Swal.fire({ icon: 'warning', title: 'Required', text: 'Title and image are required.' })
    return
  }
  
  savingBanner.value = true
  const formData = new FormData()
  formData.append('title', bannerForm.value.title)
  formData.append('subtitle', bannerForm.value.subtitle || '')
  formData.append('description', bannerForm.value.description || '')
  formData.append('price', bannerForm.value.price || '')
  formData.append('cta_text', bannerForm.value.cta_text || 'Order Now')
  formData.append('cta_link', bannerForm.value.cta_link || '/menu')
  formData.append('badge', bannerForm.value.badge || '')
  formData.append('is_active', bannerForm.value.is_active ? '1' : '0')
  formData.append('position', 'hero')
  
  if (bannerFile.value) {
    formData.append('image', bannerFile.value)
  }
  
  try {
    let res
    if (editingBanner.value) {
      res = await axios.post(`/tenant/banners/${editingBanner.value.id}`, formData)
    } else {
      res = await axios.post('/tenant/banners', formData)
    }
    
    if (res.data.success) {
      await fetchBanners()
      closeBannerModal()
      Swal.fire({ icon: 'success', title: 'Banner saved!', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false })
    }
  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Error', text: e.response?.data?.message || 'Failed to save banner' })
  } finally {
    savingBanner.value = false
  }
}

async function deleteBanner(banner) {
  const result = await Swal.fire({
    title: 'Delete banner?',
    text: 'This action cannot be undone.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    confirmButtonText: 'Yes, delete'
  })
  
  if (result.isConfirmed) {
    try {
      await axios.delete(`/tenant/banners/${banner.id}`)
      await fetchBanners()
      Swal.fire({ icon: 'success', title: 'Deleted!', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false })
    } catch (e) {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete banner' })
    }
  }
}

onMounted(() => {
  fetchThemes()
  fetchSettings()
  fetchBanners()
})

watch(tab, () => {
  if (tab.value === 'hero') {
    fetchBanners()
  }
})
</script>

<style scoped>
.wsc {
  --a: #00844d;
  --ink: #0f172a;
  --muted: #64748b;
  --line: #e2e8f0;
  --surface: #f1f5f9;
  color: var(--ink);
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
.wsc__head {
  display: flex;
  gap: 0.85rem;
  align-items: flex-start;
  margin-bottom: clamp(1rem, 3vw, 1.25rem);
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--line);
}
.wsc__icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  background: color-mix(in srgb, var(--a) 12%, #fff);
  color: var(--a);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  flex-shrink: 0;
}
.wsc__title {
  font-size: clamp(1.05rem, 2.5vw, 1.2rem);
  font-weight: 800;
  margin: 0;
  letter-spacing: -0.02em;
  color: var(--ink);
}
.wsc__sub {
  margin: 0.25rem 0 0;
  font-size: 0.875rem;
  color: var(--muted);
  line-height: 1.5;
}
.tpl {
  position: relative;
  border: 2px solid var(--line);
  border-radius: 12px;
  padding: 0.75rem;
  background: #fff;
  cursor: pointer;
  transition: all 0.2s ease;
}
.tpl:hover {
  border-color: var(--a);
}
.tpl--on {
  border-color: var(--a);
  box-shadow: 0 0 0 2px color-mix(in srgb, var(--a) 25%, #fff);
}
.tpl__mock {
  height: 60px;
  border-radius: 8px;
}
.tpl__check {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  font-size: 1.25rem;
}
.nav-preview__bar {
  border-radius: 8px 8px 0 0;
  padding: 0.65rem 0.75rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.nav-preview__btn {
  background: rgba(255, 255, 255, 0.2);
  color: #fff;
  font-size: 0.7rem;
  padding: 0.2rem 0.5rem;
  border-radius: 6px;
}
.nav-preview__accent {
  height: 4px;
  border-radius: 0 0 8px 8px;
}
.wsc__foot {
  margin-top: 1.75rem;
  padding-top: 1rem;
  border-top: 1px solid var(--line);
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 0.5rem;
}
.wsc__save {
  background: var(--a);
  color: #fff;
  border: none;
  border-radius: 999px;
  font-weight: 700;
  padding: 0.55rem 1.35rem;
}
.wsc__save:hover {
  background: #006b3d;
}
.wsc__save:disabled {
  opacity: 0.6;
}
.banner-thumb {
  width: 80px;
  height: 50px;
  object-fit: cover;
  border-radius: 6px;
}
.banner-item {
  transition: background 0.2s;
}
.banner-item:hover {
  background: var(--surface);
}
</style>
