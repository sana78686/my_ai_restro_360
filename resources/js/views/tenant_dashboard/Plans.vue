<template>
  <div class="pricing-page">
    <!-- Pricing Header -->
    <section class="pricing-header pt-20">
      <div class="container mt-20">
        <h1 class="text-center">{{ $t('pricing.title') }}</h1>
        <p class="text-center lead">{{ $t('pricing.subtitle') }}</p>
      </div>
    </section>

    <!-- Pricing Plans -->
    <section class="pricing-plans">
      <div class="container">
        <!-- Debug info -->
        <!-- <div class="text-center mb-3">
          <small class="text-muted">Debug: plans.length = {{ plans.length }}, loading = {{ loading }}</small>
        </div> -->

        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Loading plans...</p>
        </div>

        <div v-else-if="plans.length === 0" class="text-center py-5">
          <p class="text-muted">No subscription plans available at the moment.</p>
        </div>

        <div v-else class="row">
          <div 
            class="col-md-4 mb-4" 
            v-for="(plan, index) in plans" 
            :key="plan.id"
          >
            <div 
              class="pricing-card" 
              :class="{ 'featured': plans.length >= 3 && index === 1 }"
            >
              <div class="pricing-header">
                <h3>{{ plan.name }}</h3>
                <div class="price">
                  <span class="currency">$</span>
                  <span class="amount">{{ plan.price }}</span>
                  <span class="period">/{{ getBillingPeriod(plan.duration_days) }}</span>
                </div>
              </div>
              <div class="pricing-features">
                <div v-html="plan.description"></div>
                <p class="text-muted small mt-3">
                  <i class="fas fa-calendar me-1"></i>
                  {{ plan.duration_days }} days access
                </p>
              </div>
              <div class="pricing-footer">
                <button 
                  class="btn" 
                  :class="(plans.length >= 3 && index === 1) ? 'btn-primary' : 'btn-outline-primary'"
                  @click="openEmailModal(plan)"
                >
                  {{ $t('pricing.getStarted') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <!-- <section class="faq-section">
      <div class="container">
        <h2 class="text-center mb-5">{{ $t('pricing.faq.title') }}</h2>
        <div class="row">
          <div class="col-md-6">
            <div class="faq-item">
              <h4>{{ $t('pricing.faq.questions.1.question') }}</h4>
              <p>{{ $t('pricing.faq.questions.1.answer') }}</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="faq-item">
              <h4>{{ $t('pricing.faq.questions.2.question') }}</h4>
              <p>{{ $t('pricing.faq.questions.2.answer') }}</p>
            </div>
          </div>
        </div>
      </div>
    </section> -->

    <!-- Modals remain the same -->
    <div v-if="showEmailModal" class="modal-backdrop">
      <div class="modal-box">
        <h3>Enter your business email</h3>
        <input 
          type="email" 
          v-model="email" 
          placeholder="you@business.com" 
          class="form-control mb-3" 
        />
        <div class="modal-actions">
          <button class="btn btn-secondary" @click="closeModal">Cancel</button>
          <button class="btn btn-primary" @click="requestSubscription" :disabled="!email">
            Submit
          </button>
        </div>
      </div>
    </div>

    <div v-if="showCodeModal" class="modal-backdrop">
      <div class="modal-box">
        <h3>Enter the code sent to {{ email }}</h3>
        <input 
          type="text" 
          v-model="code" 
          placeholder="6-digit code" 
          class="form-control mb-3" 
  
        />
        <div class="modal-actions">
          <button class="btn btn-secondary" @click="closeModal">Cancel</button>
          <button class="btn btn-primary" @click="verifySubscription" :disabled="!code">
            Verify
          </button>
        </div>
      </div>
    </div>

    <div v-if="showPaymentModal" class="modal-backdrop">
      <div class="modal-box">
        <h3>Complete Payment for {{ selectedPlan?.name }}</h3>
        <p class="text-muted mb-3">Amount: ${{ selectedPlan?.price }}</p>
        
        <form id="payment-form" @submit.prevent="handlePayment">
          <div id="payment-element" class="form-control mb-3"></div>
          <div class="modal-actions">
            <button class="btn btn-secondary" @click="closeModal">Cancel</button>
            <button class="btn btn-primary" type="submit" :disabled="paymentLoading">
              {{ paymentLoading ? 'Processing...' : `Pay $${selectedPlan?.price}` }}
            </button>
          </div>
          <p v-if="paymentMessage" class="text-danger mt-2">{{ paymentMessage }}</p>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import Swal from 'sweetalert2'
import { loadStripe } from '@stripe/stripe-js'

export default {
  name: 'Pricing',
  data() {
    return {
      plans: [],
      loading: false,
      showEmailModal: false,
      showCodeModal: false,
      showPaymentModal: false,
      email: '',
      code: '',
      selectedPlan: null,
      stripe: null,
      elements: null,
      clientSecret: null,
      paymentLoading: false, // Changed from duplicate 'loading'
      paymentMessage: null,
    }
  },
  computed: {
    planAmount() {
      return this.selectedPlan ? Math.round(this.selectedPlan.price * 100) : 0
    }
  },
  methods: {
    async fetchPlans() {
      this.loading = true
      try {
        console.log('Fetching plans...')
        const response = await axios.get('/plans')
        console.log('API Response:', response.data)
        
        // Fix: Access the data directly from response.data.data
        this.plans = response.data.data || []
        console.log('Plans set:', this.plans)
        
      } catch (error) {
        console.error('Error fetching plans:', error)
        console.error('Error details:', error.response)
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Failed to load subscription plans'
        })
      } finally {
        this.loading = false
        console.log('Loading completed, plans:', this.plans)
      }
    },

    getBillingPeriod(durationDays) {
      if (durationDays === 30) return 'month'
      if (durationDays === 365) return 'year'
      if (durationDays === 7) return 'week'
      return `${durationDays} days`
    },

    openEmailModal(plan) {
      this.selectedPlan = plan
      this.showEmailModal = true
    },

    closeModal() {
      this.showEmailModal = false
      this.showCodeModal = false
      this.showPaymentModal = false
      this.email = ''
      this.code = ''
      this.selectedPlan = null
      this.clientSecret = null
      this.elements = null
    },

    async requestSubscription() {
      try {
        await axios.post('/subscribe/request', {
          plan_id: this.selectedPlan.id.toString(),
          email: this.email
        })
        this.showEmailModal = false
        this.showCodeModal = true

        Swal.fire({
          icon: 'success',
          title: 'Email Sent!',
          text: 'Check your inbox for a verification code.'
        })
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Request Failed',
          text: error.response?.data?.message || 'Something went wrong'
        })
      }
    },

    async verifySubscription() {
      try {
        await axios.post('/subscribe/verify', {
          email: this.email,
          code: this.code
        })

        this.showCodeModal = false
        this.showPaymentModal = true

        this.stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY)

        const response = await axios.post('/create-payment-intent', {
          amount: this.planAmount,
          currency: 'usd',
          email: this.email,
          plan_id: this.selectedPlan.id.toString(),
          plan_name: this.selectedPlan.name
        })

        this.clientSecret = response.data.clientSecret

        this.elements = this.stripe.elements({ 
          clientSecret: this.clientSecret,
          appearance: {
            theme: 'stripe',
          }
        })
        
        const paymentElement = this.elements.create('payment')
        paymentElement.mount('#payment-element')

      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Verification Failed',
          text: error.response?.data?.message || 'Invalid verification code'
        })
      }
    },

    async handlePayment() {
      this.paymentLoading = true
      this.paymentMessage = null

      try {
        const { error, paymentIntent } = await this.stripe.confirmPayment({
          elements: this.elements,
          redirect: 'if_required',
          confirmParams: {
            return_url: `${window.location.origin}/payment-success`,
          }
        })

        if (error) {
          this.paymentMessage = error.message
        } else if (paymentIntent?.status === 'succeeded') {
          await axios.post('/subscription/activate', {
            email: this.email,
            plan_id: this.selectedPlan.id.toString(),
            amount: this.selectedPlan.price,
            stripe_payment_intent_id: paymentIntent.id,
          })

          Swal.fire({
            icon: 'success',
            title: '🎉 Payment Successful!',
            text: 'Your subscription is now active! You will receive a confirmation email shortly.',
            confirmButtonText: 'Great!'
          })

          this.closeModal()
        }
      } catch (error) {
        this.paymentMessage = 'Payment processing failed. Please try again.'
        console.error('Payment error:', error)
      } finally {
        this.paymentLoading = false
      }
    }
  },

  mounted() {
    console.log('Component mounted, fetching plans...')
    this.fetchPlans()
  }
}
</script>

<style scoped>
.pricing-page {
  padding-top: 60px;
}

.pricing-header {
  padding: 80px 0;
  background: linear-gradient(135deg, #f8f9fa 0%, #fff5f5 100%);
}

.pricing-header h1 {
  font-size: 3rem;
  color: #c62828;
  margin-bottom: 1rem;
  font-weight: 700;
}

.pricing-header .lead {
  color: #666;
  font-size: 1.25rem;
}

.pricing-plans {
  padding: 80px 0;
  background: white;
}

.pricing-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 5px 20px rgba(198, 40, 40, 0.08);
  padding: 2.5rem 2rem;
  height: 100%;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
  border: 2px solid #f8f9fa;
}

.pricing-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 35px rgba(198, 40, 40, 0.15);
  border-color: #ffebee;
}

.pricing-card.featured {
  border: 2px solid #c62828;
  transform: scale(1.02);
  background: linear-gradient(135deg, #fff 0%, #fffaf5 100%);
}

.pricing-card.featured:hover {
  transform: scale(1.02) translateY(-8px);
  box-shadow: 0 20px 40px rgba(198, 40, 40, 0.2);
}

.btn-primary {
  background: linear-gradient(135deg, #c62828 0%, #d32f2f 100%);
  border: none;
  padding: 12px 30px;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(198, 40, 40, 0.3);
}
.btn-outline-primary {
  border: 2px solid #c62828;
  color: #c62828;
  background: transparent;
  padding: 12px 30px;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.pricing-card.featured::before {
  content: 'Most Popular';
  position: absolute;
  top: 20px;
  right: -35px;
  background: linear-gradient(135deg, #c62828 0%, #d32f2f 100%);
  color: white;
  padding: 8px 40px;
  font-size: 0.8rem;
  font-weight: 700;
  transform: rotate(45deg);
  letter-spacing: 0.5px;
  box-shadow: 0 2px 10px rgba(198, 40, 40, 0.3);
}

.pricing-header h3 {
  font-size: 1.75rem;
  color: #c62828;
  margin-bottom: 1rem;
  font-weight: 600;
}

.price {
  font-size: 3rem;
  color: #c62828;
  margin-bottom: 1.5rem;
  font-weight: 700;
}

.price .currency {
  font-size: 1.8rem;
  vertical-align: super;
  color: #c62828;
}

.price .period {
  font-size: 1.1rem;
  color: #666;
  font-weight: 500;
}

.pricing-features {
  margin-bottom: 2.5rem;
  min-height: 120px;
}

.pricing-features :deep(.plan-features) {
  list-style: none;
  padding: 0;
  margin: 0;
}

.pricing-features :deep(.plan-features li) {
  margin-bottom: 0.8rem;
  display: flex;
  align-items: flex-start;
  font-size: 0.95rem;
  line-height: 1.4;
}

.pricing-features :deep(.plan-features i) {
  margin-top: 0.2rem;
  margin-right: 0.75rem;
  flex-shrink: 0;
}

.pricing-features :deep(.plan-features .fa-check) {
  color: #4caf50;
}

.pricing-features :deep(.plan-features .fa-times) {
  color: #9e9e9e;
}

.pricing-footer {
  text-align: center;
  margin-top: auto;
}

.btn-danger {
  background: linear-gradient(135deg, #c62828 0%, #d32f2f 100%);
  border: none;
  padding: 12px 30px;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(198, 40, 40, 0.3);
}

.btn-danger:hover {
  background: linear-gradient(135deg, #b71c1c 0%, #c62828 100%);
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(198, 40, 40, 0.4);
}

.btn-outline-danger {
  border: 2px solid #c62828;
  color: #c62828;
  background: transparent;
  padding: 12px 30px;
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.btn-outline-danger:hover {
  background: #c62828;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(198, 40, 40, 0.3);
}

.features-comparison {
  background: linear-gradient(135deg, #f8f9fa 0%, #fff5f5 100%);
}

.features-comparison h2 {
  color: #c62828;
  font-weight: 700;
}

.features-comparison table {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 5px 20px rgba(198, 40, 40, 0.08);
}

.features-comparison th {
  background: #c62828;
  color: white;
  font-weight: 600;
  border: none !important;
  padding: 1rem;
}

.features-comparison th:first-child {
  background: #b71c1c;
}

.features-comparison td {
  padding: 1rem;
  border-color: #ffebee !important;
  vertical-align: middle;
}

.features-comparison .fa-check {
  color: #4caf50;
  font-size: 1.2rem;
}

.features-comparison .fa-times {
  color: #f44336;
  font-size: 1.2rem;
}

.faq-section {
  padding: 80px 0;
  background: white;
}

.faq-section h2 {
  color: #c62828;
  font-weight: 700;
}

.faq-item {
  margin-bottom: 2.5rem;
  padding: 2rem;
  background: #fafafa;
  border-radius: 12px;
  border-left: 4px solid #c62828;
}

.faq-item h4 {
  color: #c62828;
  margin-bottom: 1rem;
  font-weight: 600;
}

.faq-item p {
  color: #666;
  line-height: 1.6;
  margin-bottom: 0;
}

.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
}

.modal-box {
  background: #fff;
  padding: 2.5rem;
  border-radius: 12px;
  width: 500px;
  max-width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 50px rgba(198, 40, 40, 0.2);
  border: 1px solid #ffebee;
}

.modal-box h3 {
  color: #c62828;
  font-weight: 600;
  margin-bottom: 1.5rem;
}

.modal-actions {
  margin-top: 1.5rem;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.btn-secondary {
  background: #6c757d;
  border: none;
  padding: 10px 25px;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.btn-secondary:hover {
  background: #5a6268;
  transform: translateY(-1px);
}

@media (max-width: 991.98px) {
  .pricing-card.featured {
    transform: none;
    margin: 1.5rem 0;
  }
  
  .pricing-card.featured:hover {
    transform: translateY(-8px);
  }
  
  .pricing-header h1 {
    font-size: 2.5rem;
  }
  
  .price {
    font-size: 2.5rem;
  }
}

.spinner-border.text-danger {
  border-color: #c62828;
  border-right-color: transparent;
}

.text-muted {
  color: #6c757d !important;
}

/* Additional red theme enhancements */
.table-bordered {
  border-color: #ffebee;
}

.table-bordered th,
.table-bordered td {
  border-color: #ffebee;
}

.form-control:focus {
  border-color: #c62828;
  box-shadow: 0 0 0 0.2rem rgba(198, 40, 40, 0.25);
}
</style>