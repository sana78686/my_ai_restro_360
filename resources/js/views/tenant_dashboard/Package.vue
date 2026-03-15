<template>
  <div class="subscription-plans-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">{{ $t('tenant_dashboard.subscription_plans.title') }}</h4>
      <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" @click="refreshPlans">
          <i class="fas fa-refresh me-2"></i>
          {{ $t('tenant_dashboard.subscription_plans.refresh') }}
        </button>
        <button class="btn btn-primary" @click="openCreateModal">
          <i class="fas fa-plus me-2"></i>
          {{ $t('tenant_dashboard.subscription_plans.newPlan') }}
        </button>
      </div>
    </div>

    <!-- Current Plan Status -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card bg-light">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-md-8">
                <h5 class="card-title mb-1">{{ $t('tenant_dashboard.subscription_plans.currentPlan') }}</h5>
                <p class="card-text text-muted mb-0">
                  <strong>Premium Plan</strong> - Expires on Dec 31, 2024
                </p>
              </div>
              <div class="col-md-4 text-end">
                <span class="badge bg-success fs-6">Active</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Plans Grid -->
    <div class="row">
      <div class="col-lg-4 col-md-6 mb-4" v-for="plan in plans" :key="plan.id">
        <div class="card plan-card h-100" :class="{ 'featured-plan': plan.featured }">
          <div class="card-header text-center py-4" :class="plan.headerClass">
            <h5 class="card-title mb-2 text-white">{{ plan.name }}</h5>
            <div class="plan-price">
              <span class="h2 text-white">${{ plan.price }}</span>
              <span class="text-white-50">/{{ plan.interval }}</span>
            </div>
          </div>
          <div class="card-body">
            <p class="card-text text-muted text-center mb-4">{{ plan.description }}</p>
            
            <ul class="list-unstyled mb-4">
              <li v-for="feature in plan.features" :key="feature" class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                {{ feature }}
              </li>
            </ul>

            <div class="text-center mt-auto">
              <button v-if="plan.current" class="btn btn-outline-primary w-100" disabled>
                <i class="fas fa-check me-2"></i>
                {{ $t('tenant_dashboard.subscription_plans.currentPlan') }}
              </button>
              <button v-else class="btn btn-primary w-100" @click="selectPlan(plan)">
                {{ $t('tenant_dashboard.subscription_plans.selectPlan') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Billing History -->
    <div class="card mt-5">
      <div class="card-header">
        <h5 class="card-title mb-0">{{ $t('tenant_dashboard.subscription_plans.billingHistory') }}</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>{{ $t('tenant_dashboard.subscription_plans.invoiceId') }}</th>
                <th>{{ $t('tenant_dashboard.subscription_plans.date') }}</th>
                <th>{{ $t('tenant_dashboard.subscription_plans.plan') }}</th>
                <th>{{ $t('tenant_dashboard.subscription_plans.amount') }}</th>
                <th>{{ $t('tenant_dashboard.subscription_plans.status') }}</th>
                <th>{{ $t('tenant_dashboard.subscription_plans.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="invoice in billingHistory" :key="invoice.id">
                <td>#{{ invoice.id }}</td>
                <td>{{ formatDate(invoice.date) }}</td>
                <td>{{ invoice.plan }}</td>
                <td>${{ invoice.amount }}</td>
                <td>
                  <span :class="`badge bg-${getStatusBadge(invoice.status)}`">
                    {{ invoice.status }}
                  </span>
                </td>
                <td>
                  <button class="btn btn-outline-primary btn-sm" @click="downloadInvoice(invoice)">
                    <i class="fas fa-download me-1"></i>
                    {{ $t('tenant_dashboard.subscription_plans.download') }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Create Plan Modal -->
    <div class="modal fade" id="createPlanModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $t('tenant_dashboard.subscription_plans.createNewPlan') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="createPlan">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">{{ $t('tenant_dashboard.subscription_plans.planName') }}</label>
                    <input v-model="newPlan.name" type="text" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">{{ $t('tenant_dashboard.subscription_plans.price') }}</label>
                    <div class="input-group">
                      <span class="input-group-text">$</span>
                      <input v-model="newPlan.price" type="number" class="form-control" min="0" step="0.01" required>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="mb-3">
                <label class="form-label">{{ $t('tenant_dashboard.subscription_plans.description') }}</label>
                <textarea v-model="newPlan.description" class="form-control" rows="2"></textarea>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">{{ $t('tenant_dashboard.subscription_plans.billingInterval') }}</label>
                    <select v-model="newPlan.interval" class="form-select" required>
                      <option value="monthly">Monthly</option>
                      <option value="yearly">Yearly</option>
                      <option value="lifetime">Lifetime</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">{{ $t('tenant_dashboard.subscription_plans.maxUsers') }}</label>
                    <input v-model="newPlan.maxUsers" type="number" class="form-control" min="1">
                  </div>
                </div>
              </div>
              
              <div class="mb-3">
                <label class="form-label">{{ $t('tenant_dashboard.subscription_plans.features') }}</label>
                <div v-for="(feature, index) in newPlan.features" :key="index" class="input-group mb-2">
                  <input v-model="newPlan.features[index]" type="text" class="form-control" 
                         :placeholder="`Feature ${index + 1}`">
                  <button type="button" class="btn btn-outline-danger" @click="removeFeature(index)">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" @click="addFeature">
                  <i class="fas fa-plus me-1"></i>
                  {{ $t('tenant_dashboard.subscription_plans.addFeature') }}
                </button>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              {{ $t('tenant_dashboard.subscription_plans.cancel') }}
            </button>
            <button type="button" class="btn btn-primary" @click="createPlan" :disabled="creating">
              <span v-if="creating" class="spinner-border spinner-border-sm me-2"></span>
              {{ $t('tenant_dashboard.subscription_plans.createPlan') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { Modal } from 'bootstrap'
import { useI18n } from 'vue-i18n'

const { t: $t } = useI18n()

const plans = ref([
  {
    id: 1,
    name: 'Basic',
    price: 19,
    interval: 'month',
    description: 'Perfect for small businesses',
    features: [
      'Up to 10 users',
      '5GB storage',
      'Basic support',
      'Email notifications'
    ],
    headerClass: 'bg-secondary',
    featured: false,
    current: false
  },
  {
    id: 2,
    name: 'Premium',
    price: 49,
    interval: 'month',
    description: 'Ideal for growing companies',
    features: [
      'Up to 50 users',
      '50GB storage',
      'Priority support',
      'Advanced analytics',
      'Custom domains'
    ],
    headerClass: 'bg-primary',
    featured: true,
    current: true
  },
  {
    id: 3,
    name: 'Enterprise',
    price: 99,
    interval: 'month',
    description: 'For large organizations',
    features: [
      'Unlimited users',
      '500GB storage',
      '24/7 phone support',
      'Advanced security',
      'Custom integrations',
      'Dedicated account manager'
    ],
    headerClass: 'bg-success',
    featured: false,
    current: false
  }
])

const billingHistory = ref([
  {
    id: 'INV-001',
    date: '2024-10-01',
    plan: 'Premium Plan',
    amount: 49.00,
    status: 'paid'
  },
  {
    id: 'INV-002',
    date: '2024-09-01',
    plan: 'Premium Plan',
    amount: 49.00,
    status: 'paid'
  },
  {
    id: 'INV-003',
    date: '2024-08-01',
    plan: 'Basic Plan',
    amount: 19.00,
    status: 'paid'
  }
])

const newPlan = ref({
  name: '',
  price: 0,
  description: '',
  interval: 'monthly',
  maxUsers: 1,
  features: ['']
})

const creating = ref(false)

let createModal = null

// Helper Methods
const formatDate = (dateString) => {
  if (!dateString) return '-'
  try {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    })
  } catch (error) {
    return '-'
  }
}

const getStatusBadge = (status) => {
  const badges = {
    paid: 'success',
    pending: 'warning',
    failed: 'danger',
    refunded: 'info'
  }
  return badges[status] || 'secondary'
}

const openCreateModal = () => {
  newPlan.value = {
    name: '',
    price: 0,
    description: '',
    interval: 'monthly',
    maxUsers: 1,
    features: ['']
  }
  if (!createModal) {
    createModal = new Modal(document.getElementById('createPlanModal'))
  }
  createModal.show()
}

const selectPlan = (plan) => {
  Swal.fire({
    title: $t('tenant_dashboard.subscription_plans.confirmSelection'),
    text: $t('tenant_dashboard.subscription_plans.confirmSelectionText', { plan: plan.name }),
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#c62828',
    cancelButtonColor: '#6c757d',
    confirmButtonText: $t('tenant_dashboard.subscription_plans.confirm'),
    cancelButtonText: $t('tenant_dashboard.subscription_plans.cancel')
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        icon: 'success',
        title: $t('tenant_dashboard.subscription_plans.planSelected'),
        text: $t('tenant_dashboard.subscription_plans.planSelectedText', { plan: plan.name }),
        confirmButtonColor: '#c62828'
      })
    }
  })
}

const createPlan = async () => {
  creating.value = true
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    Swal.fire({
      icon: 'success',
      title: $t('tenant_dashboard.dashboard_common.success'),
      text: $t('tenant_dashboard.subscription_plans.planCreated'),
      confirmButtonColor: '#c62828'
    })
    createModal.hide()
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: $t('tenant_dashboard.dashboard_common.error'),
      text: error.response?.data?.message || $t('tenant_dashboard.subscription_plans.createFailed'),
      confirmButtonColor: '#c62828'
    })
  } finally {
    creating.value = false
  }
}

const addFeature = () => {
  newPlan.value.features.push('')
}

const removeFeature = (index) => {
  newPlan.value.features.splice(index, 1)
}

const downloadInvoice = (invoice) => {
  Swal.fire({
    icon: 'info',
    title: $t('tenant_dashboard.subscription_plans.downloadStarted'),
    text: $t('tenant_dashboard.subscription_plans.downloadStartedText', { invoice: invoice.id }),
    confirmButtonColor: '#c62828'
  })
}

const refreshPlans = () => {
  Swal.fire({
    icon: 'success',
    title: $t('tenant_dashboard.dashboard_common.success'),
    text: $t('tenant_dashboard.subscription_plans.plansRefreshed'),
    confirmButtonColor: '#c62828',
    timer: 1500
  })
}

// Lifecycle
onMounted(() => {
  // Initialize any required functionality
})
</script>

<style scoped>
.subscription-plans-page {
  padding: 20px;
  font-family: 'Inter', 'Roboto', 'Segoe UI', Arial, sans-serif;
}

.plan-card {
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(60,60,60,0.06);
}

.plan-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(60,60,60,0.15);
}

.featured-plan {
  border: 2px solid #c62828;
  position: relative;
  overflow: hidden;
}

.featured-plan::before {
  content: 'Most Popular';
  position: absolute;
  top: 15px;
  right: -30px;
  background: #c62828;
  color: white;
  padding: 5px 30px;
  font-size: 0.75rem;
  font-weight: 600;
  transform: rotate(45deg);
}

.plan-card .card-header {
  border-radius: 10px 10px 0 0 !important;
  border-bottom: none;
}

.plan-price {
  margin-top: 10px;
}

.plan-features {
  min-height: 200px;
}

.list-unstyled li {
  padding: 4px 0;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .plan-card {
    margin-bottom: 20px;
  }
  
  .featured-plan::before {
    font-size: 0.65rem;
    right: -35px;
  }
}

.badge {
  font-size: 0.75em;
  font-weight: 500;
}

.table-responsive {
  border-radius: 8px;
  overflow: hidden;
}

.table {
  border: 1px solid #e0e0e0;
  box-shadow: 0 2px 8px rgba(60,60,60,0.06);
}

.table thead th {
  background: #fafbfc;
  font-weight: 500;
  color: #b71c1c;
}
</style>