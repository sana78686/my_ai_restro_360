<template>
  <div class="Pricing-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">Subscription Plans</h4>
      <div class="d-flex align-items-center gap-2">
        <div v-if="loadingSubscription || loadingPlans" class="spinner-border spinner-border-sm text-danger" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <button
          v-if="currentPlan"
          class="btn btn-outline-secondary btn-sm"
          @click="refreshData"
          :disabled="loadingSubscription"
        >
          <span v-if="loadingSubscription" class="spinner-border spinner-border-sm me-2"></span>
          Refresh
        </button>
      </div>
    </div>

    <!-- Current Subscription Info -->
    <div class="card mb-4" v-if="currentPlan || currentSubscription">
      <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
          <div>
            <h5 class="mb-2 text-dark" v-if="currentPlan">{{ currentPlan.name }}</h5>
            <h5 class="mb-2 text-dark" v-else>{{ isTrialing ? 'Trial plan' : 'No active plan' }}</h5>
            <p class="text-muted mb-0 small" v-if="isTrialing && trialEndsAt">
              Trial ends {{ formatDate(trialEndsAt) }} ({{ trialDaysRemaining }} days left)
            </p>
            <p class="text-muted mb-0 small" v-else-if="currentSubscription?.ends_at">
              Renews on {{ formatDate(currentSubscription.ends_at) }}
            </p>
            <p class="text-muted mb-0 small" v-else>
              Select a plan to get started.
            </p>
          </div>
          <div>
            <span
              v-if="currentSubscription"
              class="badge px-3 py-2"
              :class="isTrialing ? 'bg-warning text-dark' : 'bg-success'"
            >
              {{ statusLabel }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Plans Grid -->
    <div v-if="plans.length" class="row g-4">
      <div class="col-12 col-md-6 col-xl-4" v-for="plan in plans" :key="plan.id">
        <div class="card plan-card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <h5 class="mb-1 text-dark">{{ plan.name }}</h5>
                <p class="text-muted small mb-0">{{ plan.interval === 'year' ? 'Billed yearly' : 'Billed monthly' }}</p>
              </div>
              <span v-if="currentPlan?.id === plan.id" class="badge bg-secondary">Current</span>
            </div>

            <div class="mb-3">
              <span class="h4 text-dark fw-bold">
                {{ plan.currency?.toUpperCase() }} {{ formatPrice(plan.price) }}
                <small class="text-muted fw-normal">/{{ plan.interval }}</small>
              </span>
            </div>

            <ul class="list-unstyled mb-3">
              <li
                v-for="(feature, index) in normalizeFeatures(plan.features).slice(0, 6)"
                :key="index"
                class="d-flex align-items-start mb-2 small"
              >
                <i class="fas fa-check text-success me-2 mt-1"></i>
                <span>{{ feature }}</span>
              </li>
              <li v-if="normalizeFeatures(plan.features).length > 6" class="text-muted ms-4 small">
                +{{ normalizeFeatures(plan.features).length - 6 }} more...
              </li>
            </ul>

            <div class="bg-light rounded p-2 mb-3 small">
              <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">New features</span>
                <strong :class="getPlanDiff(plan).gains.length ? 'text-success' : 'text-muted'">
                  +{{ getPlanDiff(plan).gains.length }}
                </strong>
              </div>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <span class="text-muted">Features you'd lose</span>
                <strong :class="getPlanDiff(plan).losses.length ? 'text-danger' : 'text-muted'">
                  -{{ getPlanDiff(plan).losses.length }}
                </strong>
              </div>
            </div>

            <button
              class="btn w-100"
              :class="buttonClass(plan)"
              :disabled="determinePlanChangeType(plan) === 'current' || actionLoading"
              @click="handlePlanClick(plan)"
            >
              {{ ctaLabel(plan) }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="!loadingPlans" class="card">
      <div class="card-body text-center py-5 text-muted">
        <i class="fas fa-credit-card fa-3x mb-3" style="color: #bdbdbd;"></i>
        <div class="h5 mb-2">No plans available</div>
        <div>No subscription plans are available at the moment.</div>
      </div>
    </div>

    <!-- Upgrade Modal -->
    <transition name="fade">
          <div v-if="modalType === 'upgrade'" class="plan-modal-backdrop">
        <div class="plan-modal upgrade shadow-lg">
          <button class="btn-close" @click="closeModal" aria-label="Close"></button>
          <h4 class="mb-2 text-dark">Upgrade to {{ selectedPlan?.name }}</h4>
          <p class="text-muted mb-3">Great choice! You'll instantly unlock the features below.</p>
          <div class="modal-feature-list">
            <p class="text-uppercase small text-success fw-semibold mb-1">New features</p>
            <ul class="list-unstyled mb-3">
              <li v-if="!comparison.gains.length" class="text-muted">
                This plan includes all features you already have.
              </li>
              <li
                v-for="(feature, index) in comparison.gains"
                :key="`gain-${index}`"
                class="d-flex align-items-start mb-2"
              >
                <i class="fas fa-arrow-up text-success me-2 mt-1"></i>
                <span>{{ feature }}</span>
              </li>
            </ul>
          </div>
          <div class="alert alert-info border-0">
            You'll be redirected to Stripe to securely complete the upgrade.
          </div>
          <button class="btn btn-danger w-100 mt-3" @click="confirmPlanChange" :disabled="actionLoading">
            <span v-if="actionLoading" class="spinner-border spinner-border-sm me-2"></span>
            Continue to checkout
          </button>
        </div>
      </div>
    </transition>

    <!-- Downgrade Modal -->
    <transition name="fade">
      <div v-if="modalType === 'downgrade'" class="plan-modal-backdrop">
        <div class="plan-modal downgrade shadow-lg">
          <button class="btn-close" @click="closeModal" aria-label="Close"></button>
          <h4 class="mb-2 text-dark">Downgrade to {{ selectedPlan?.name }}</h4>
          <p class="text-muted mb-3">
            Heads up: downgrading means you'll lose access to the features below until you upgrade again.
          </p>
          <div class="modal-feature-list">
            <p class="text-uppercase small text-danger fw-semibold mb-1">Features you'll lose</p>
            <ul class="list-unstyled mb-3">
              <li v-if="!comparison.losses.length" class="text-muted">
                No feature loss detected for this downgrade.
              </li>
              <li
                v-for="(feature, index) in comparison.losses"
                :key="`loss-${index}`"
                class="d-flex align-items-start mb-2"
              >
                <i class="fas fa-exclamation-triangle text-danger me-2 mt-1"></i>
                <span>{{ feature }}</span>
              </li>
            </ul>
          </div>
          <div class="alert alert-warning border-0">
            Once downgraded, those features remain locked until you upgrade again.
          </div>
          <button class="btn btn-outline-danger w-100 mt-3" @click="confirmPlanChange" :disabled="actionLoading">
            <span v-if="actionLoading" class="spinner-border spinner-border-sm me-2"></span>
            Confirm downgrade
          </button>
        </div>
      </div>
    </transition>

    <!-- Trial / lateral Modal -->
    <transition name="fade">
      <div v-if="modalType === 'trial' || modalType === 'lateral'" class="plan-modal-backdrop">
        <div class="plan-modal trial shadow-lg">
          <button class="btn-close" @click="closeModal" aria-label="Close"></button>
          <h4 class="mb-2 text-dark">
            {{ modalType === 'trial' ? 'Activate your subscription' : 'Switch plan' }}
          </h4>
          <p class="text-muted mb-3">
            {{
              modalType === 'trial'
                ? 'Complete checkout to lock in this plan before your trial ends.'
                : 'Review the highlights before switching plans.'
            }}
          </p>
          <div class="modal-feature-list">
            <p class="text-uppercase small text-primary fw-semibold mb-1">Included features</p>
            <ul class="list-unstyled mb-0">
              <li
                v-for="(feature, index) in normalizeFeatures(selectedPlan?.features)"
                :key="`trial-${index}`"
                class="d-flex align-items-start mb-2"
              >
                <i class="fas fa-check text-primary me-2 mt-1"></i>
                <span>{{ feature }}</span>
              </li>
            </ul>
          </div>
          <button class="btn btn-danger w-100 mt-3" @click="confirmPlanChange" :disabled="actionLoading">
            <span v-if="actionLoading" class="spinner-border spinner-border-sm me-2"></span>
            Proceed to checkout
          </button>
        </div>
      </div>
    </transition>
  </div>
  </template>

  <script setup>
import { ref, computed, onMounted } from 'vue'
  import axios from 'axios'

  const plans = ref([])
const loadingPlans = ref(true)
const currentSubscription = ref(null)
const loadingSubscription = ref(true)
const selectedPlan = ref(null)
const modalType = ref(null)
const actionLoading = ref(false)

onMounted(() => {
  loadPlans()
  loadSubscription()
})

async function loadPlans() {
  loadingPlans.value = true
  try {
    const { data } = await axios.get('/tenant/plans')
    const planPayload = data?.plans || data?.data || []
    plans.value = planPayload.map(plan => ({
          ...plan,
      features: normalizeFeatures(plan.features)
        }))
    } catch (error) {
      console.error('Failed to load plans:', error)
    } finally {
    loadingPlans.value = false
  }
}

async function loadSubscription() {
  loadingSubscription.value = true
  try {
    const { data } = await axios.get('/tenant/my-subscription')
    currentSubscription.value = data?.data || data
  } catch (error) {
    console.error('Failed to load subscription:', error)
    currentSubscription.value = null
  } finally {
    loadingSubscription.value = false
  }
}

function refreshData() {
  loadSubscription()
  loadPlans()
}

const currentPlan = computed(() => {
  if (!plans.value.length || !currentSubscription.value) return null
  const subscription = currentSubscription.value
  const planId = subscription?.plan?.id ?? subscription?.plan_id
  const planName = subscription?.plan?.name ?? subscription?.plan_name
  if (planId) {
    return plans.value.find(plan => Number(plan.id) === Number(planId)) || null
  }
  if (planName) {
    return plans.value.find(plan => plan.name === planName) || null
  }
  return null
})

const currentFeatures = computed(() => normalizeFeatures(currentPlan.value?.features))

const trialEndsAt = computed(() => currentSubscription.value?.trial_ends_at ?? currentSubscription.value?.ends_at ?? null)

const isTrialing = computed(() => {
  if (!currentSubscription.value) return false
  const status = String(currentSubscription.value.status || '').toLowerCase()
  return status.includes('trial') || Boolean(currentSubscription.value.is_trial)
})

const trialDaysRemaining = computed(() => {
  if (!trialEndsAt.value) return 0
  const end = new Date(trialEndsAt.value).getTime()
  const today = new Date().getTime()
  const diff = Math.ceil((end - today) / (1000 * 60 * 60 * 24))
  return diff > 0 ? diff : 0
})

const statusLabel = computed(() => {
  if (!currentSubscription.value) return 'Not subscribed'
  if (isTrialing.value) return `Trial · ${trialDaysRemaining.value} days left`
  return (
    currentSubscription.value.status?.toString().replace(/_/g, ' ').toUpperCase() || 'ACTIVE'
  )
})

const comparison = computed(() => {
  if (!selectedPlan.value) {
    return { gains: [], losses: [] }
  }
  return getPlanDiff(selectedPlan.value)
})

function handlePlanClick(plan) {
  const type = determinePlanChangeType(plan)
  if (type === 'current') return
  selectedPlan.value = plan
  modalType.value = type
}

function closeModal() {
  modalType.value = null
  selectedPlan.value = null
}

function determinePlanChangeType(plan) {
  if (!currentPlan.value) return 'trial'
  if (currentPlan.value.id === plan.id) return 'current'
  const currentPrice = Number(currentPlan.value.price) || 0
  const targetPrice = Number(plan.price) || 0
  if (targetPrice > currentPrice) return 'upgrade'
  if (targetPrice < currentPrice) return 'downgrade'
  return 'lateral'
}

function getPlanDiff(plan) {
  const targetFeatures = normalizeFeatures(plan?.features)
  const current = currentFeatures.value
  const gains = targetFeatures.filter(feature => !current.includes(feature))
  const losses = current.filter(feature => !targetFeatures.includes(feature))
  return { gains, losses }
}

function ctaLabel(plan) {
  const type = determinePlanChangeType(plan)
  switch (type) {
    case 'current':
      return 'Current plan'
    case 'upgrade':
      return 'Upgrade plan'
    case 'downgrade':
      return 'Downgrade plan'
    case 'trial':
      return isTrialing.value ? 'Activate plan' : 'Get started'
    case 'lateral':
      return 'Switch plan'
    default:
      return 'Select plan'
  }
}

function buttonClass(plan) {
  const type = determinePlanChangeType(plan)
  switch (type) {
    case 'upgrade':
    case 'trial':
      return 'btn-danger'
    case 'downgrade':
      return 'btn-outline-danger'
    case 'lateral':
      return 'btn-outline-secondary'
    default:
      return 'btn-outline-secondary disabled'
  }
}

function formatPrice(value) {
  const amount = Number(value) || 0
  return amount.toLocaleString(undefined, {
    minimumFractionDigits: amount % 1 ? 2 : 0,
    maximumFractionDigits: 2
  })
}

function formatDate(value) {
  if (!value) return ''
  return new Date(value).toLocaleDateString(undefined, {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

function normalizeFeatures(features) {
  if (!features) return []
  if (Array.isArray(features)) return features
  if (typeof features === 'string') {
    try {
      const parsed = JSON.parse(features)
      if (Array.isArray(parsed)) {
        return parsed
      }
    } catch (e) {
      // fall back to splitting string
    }
    return features
      .split(/[\n,]/)
      .map(item => item.trim())
      .filter(Boolean)
  }
  return []
}

async function confirmPlanChange() {
  if (!selectedPlan.value) return
  actionLoading.value = true
    try {
      const { data } = await axios.post('/tenant/create-checkout-session', {
      plan_id: selectedPlan.value.id
      })
    if (data?.url) {
      window.location.href = data.url
      } else {
        alert('Failed to get checkout link.')
      }
    } catch (error) {
      console.error('Error creating checkout session:', error)
      alert('Something went wrong while redirecting to checkout.')
  } finally {
    actionLoading.value = false
    }
  }
  </script>

<style scoped>
.Pricing-page {
  padding: 20px;
  font-family: 'Inter', 'Roboto', 'Segoe UI', Arial, sans-serif;
  font-size: 16px;
}

.plan-card {
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(60, 60, 60, 0.06);
}

.plan-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(60, 60, 60, 0.15);
}

.card {
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(60, 60, 60, 0.06);
}

.plan-modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(5, 5, 5, 0.65);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  z-index: 1055;
}

.plan-modal {
  position: relative;
  max-width: 560px;
  width: 100%;
  background: #fff;
  border-radius: 12px;
  padding: 2rem;
}

.plan-modal.upgrade,
.plan-modal.downgrade,
.plan-modal.trial {
  border-top: 4px solid #dc3545;
}

.plan-modal .btn-close {
  position: absolute;
  top: 1rem;
  right: 1rem;
}

.modal-feature-list {
  max-height: 260px;
  overflow-y: auto;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@media (max-width: 768px) {
  .Pricing-page {
    padding: 15px;
  }

  .plan-card {
    margin-bottom: 1rem;
  }

  .plan-modal {
    max-height: 90vh;
    overflow-y: auto;
    padding: 1.5rem;
  }
}
</style>
