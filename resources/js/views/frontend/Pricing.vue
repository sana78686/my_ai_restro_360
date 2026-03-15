<template>
  <div class="pricing-page">
    <!-- Pricing Header -->
    <section class="pricing-header py-5" style="background: #fafafa; margin-top: 96px;">
      <div class="container">
        <div class="text-center mb-3">
          <h1
            style="font-size: 2.8rem; font-weight: bold; margin-bottom: 0.5rem; color: #1a2734; display: inline-block; position: relative;">
            {{ $t('pricing.title') }}
            <span
              style="display: block; height: 4px; width: 90px; background: #c62828; border-radius: 2px; margin: 0.5rem auto 0 auto;"></span>
          </h1>
        </div>
        <p class="text-center lead text-muted mb-0" style="font-size: 1.1rem; color: #666;">
          {{ $t('pricing.subtitle') }}
        </p>
      </div>
    </section>

    <!-- Pricing Plans -->
    <section class="pricing-plans py-5">
      <div class="container" style="max-width: 1200px; margin: 0 auto;">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-3" style="font-size: 1.1rem; color: #666;">Loading plans...</p>
        </div>

        <div v-else-if="plans.length === 0" class="text-center py-5">
          <p class="text-muted" style="font-size: 1.1rem;">No subscription plans available at the moment.</p>
        </div>

        <div v-else>
          <!-- Get Started Button (No Plan Selected - Default Trial) -->
          <div class="text-center mb-5">
            <button class="btn btn-danger btn-lg px-5" @click="handleGetStartedWithoutPlan" 
                    style="background: #c62828; border: none; padding: 0.75rem 2rem; font-weight: 500; font-size: 1.1rem;">
              <i class="fas fa-rocket me-2"></i>
              Get Started Now (Start with Free Trial)
            </button>
            <p class="text-muted mt-3" style="font-size: 0.95rem;">
              Start with our free trial plan. You can upgrade anytime from your dashboard.
            </p>
          </div>

          <!-- Pricing Cards -->
          <div class="row g-4 justify-content-center">
            <div 
              class="col-12 col-md-6 col-lg-4" 
              v-for="(plan, index) in plans" 
              :key="plan.id"
            >
              <div class="pricing-card h-100 d-flex flex-column" :class="{ 'featured': plans.length >= 3 && index === 1 }">
                <div class="pricing-card-header">
                  <h3 style="font-size: 1.5rem; font-weight: bold; color: #1a2734; margin-bottom: 1rem;">{{ plan.name }}</h3>
                  <div class="price mb-4">
                    <span class="currency" style="font-size: 1.5rem; color: #666; vertical-align: top;">$</span>
                    <span class="amount" style="font-size: 3rem; font-weight: bold; color: #c62828; line-height: 1;">{{ plan.price }}</span>
                    <span class="period" style="font-size: 1rem; color: #666;">/{{ plan.interval }}</span>
                  </div>
                </div>
                <div class="pricing-card-body flex-grow-1">
                  <div class="features">
                    <ul class="list-unstyled mb-0">
                      <li v-for="(feature, idx) in plan.features" :key="feature" 
                          :style="{
                            padding: '0.75rem 0',
                            'border-bottom': idx < plan.features.length - 1 ? '1px solid #f0f0f0' : 'none',
                            display: 'flex',
                            'align-items': 'center'
                          }">
                        <i class="fas fa-check-circle text-danger me-2" style="font-size: 1rem;"></i>
                        <span style="color: #666;">{{ feature }}</span>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="pricing-card-footer mt-auto pt-4">
                  <button class="btn btn-danger btn-lg w-100" @click="handlePlanSelection(plan)" 
                          style="background: #c62828; border: none; padding: 0.75rem 1.5rem; font-weight: 500;">
                    {{ $t('pricing.getStarted') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Available Payment Methods Section -->
    <section class="payment-methods py-5" style="background: #f8f9fa;">
      <div class="container" style="max-width: 1200px; margin: 0 auto;">
        <div class="text-center mb-4">
          <h2
            style="font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem; color: #1a2734; display: inline-block; position: relative;">
            Available Payment Methods
            <span
              style="display: block; height: 4px; width: 90px; background: #c62828; border-radius: 2px; margin: 0.5rem auto 0 auto;"></span>
          </h2>
          <p class="text-center text-muted mb-5" style="font-size: 1.1rem;">Secure payment options for your convenience</p>
        </div>
        <div class="row g-4 justify-content-center">
          <div class="col-6 col-md-4 col-lg-3" v-for="(method, index) in paymentMethods" :key="index">
            <div class="payment-method-card">
              <div class="payment-method-icon-wrapper">
                <img :src="method.iconUrl" alt="Payment Method" class="payment-method-icon" />
              </div>
              <p class="mb-0 mt-3" style="font-weight: 500; color: #333;">{{ method.name }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Modals for Email, Code, and Payment -->
    <div v-if="showEmailModal" class="modal-backdrop" @click.self="closeModal">
      <div class="modal-box">
        <h3 style="font-size: 1.5rem; font-weight: bold; color: #1a2734; margin-bottom: 1.5rem;">Enter your business email</h3>
        <input 
          type="email" 
          v-model="email" 
          placeholder="you@business.com" 
          class="form-control mb-4" 
          style="padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px;"
        />
        <div class="modal-actions d-flex gap-2">
          <button class="btn btn-secondary flex-fill" @click="closeModal" 
                  style="padding: 0.75rem; border-radius: 4px;">Cancel</button>
          <button class="btn btn-danger flex-fill" @click="requestSubscription" :disabled="!email"
                  style="background: #c62828; border: none; padding: 0.75rem; border-radius: 4px;">
            Submit
          </button>
        </div>
      </div>
    </div>

    <div v-if="showCodeModal" class="modal-backdrop" @click.self="closeModal">
      <div class="modal-box">
        <h3 style="font-size: 1.5rem; font-weight: bold; color: #1a2734; margin-bottom: 1.5rem;">Enter the code sent to {{ email }}</h3>
        <input 
          type="text" 
          v-model="code" 
          placeholder="6-digit code" 
          class="form-control mb-4" 
          style="padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px;"
        />
        <div class="modal-actions d-flex gap-2">
          <button class="btn btn-secondary flex-fill" @click="closeModal"
                  style="padding: 0.75rem; border-radius: 4px;">Cancel</button>
          <button class="btn btn-danger flex-fill" @click="verifySubscription" :disabled="!code"
                  style="background: #c62828; border: none; padding: 0.75rem; border-radius: 4px;">
            Verify
          </button>
        </div>
      </div>
    </div>

    <div v-if="showPaymentModal" class="modal-backdrop" @click.self="closeModal">
      <div class="modal-box">
        <h3 style="font-size: 1.5rem; font-weight: bold; color: #1a2734; margin-bottom: 1rem;">Complete Payment for {{ selectedPlan?.name }}</h3>
        <p class="text-muted mb-4" style="font-size: 1.1rem;">Amount: <strong style="color: #c62828; font-size: 1.2rem;">${{ selectedPlan?.price }}</strong></p>
        <form id="payment-form" @submit.prevent="handlePayment">
          <div id="payment-element" class="form-control mb-4" style="padding: 1rem; border: 1px solid #ddd; border-radius: 4px; min-height: 60px;"></div>
          <div class="modal-actions d-flex gap-2">
            <button class="btn btn-secondary flex-fill" @click="closeModal"
                    style="padding: 0.75rem; border-radius: 4px;">Cancel</button>
            <button class="btn btn-danger flex-fill" type="submit" :disabled="paymentLoading"
                    style="background: #c62828; border: none; padding: 0.75rem; border-radius: 4px;">
              {{ paymentLoading ? 'Processing...' : `Pay $${selectedPlan?.price}` }}
            </button>
          </div>
          <p v-if="paymentMessage" class="text-danger mt-3 mb-0" style="font-size: 0.9rem;">{{ paymentMessage }}</p>
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
      paymentMethods: [],  // Array to store active payment methods
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
    // Fetch available payment methods dynamically from the backend
    async fetchPaymentMethods() {
      try {
        const response = await axios.get('/payment-methods');  // Fetch available methods from your backend
        this.paymentMethods = response.data.methods || [];  // Assuming the response gives you available methods
      } catch (error) {
        console.error("Error fetching payment methods:", error);
      }
    },

    async fetchPlans() {
      this.loading = true
      try {
        const response = await axios.get('/plans')
        this.plans = response.data.plans || response.data.data || []
      } catch (error) {
        console.error('Error fetching plans:', error)
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Failed to load subscription plans'
        })
      } finally {
        this.loading = false
      }
    },

    getBillingPeriod(durationDays) {
      if (durationDays === 30) return 'month'
      if (durationDays === 365) return 'year'
      if (durationDays === 7) return 'week'
      return `${durationDays} days`
    },

    handleGetStartedWithoutPlan() {
      // No plan selected - redirect to registration with default trial
      // Store in sessionStorage that no plan was selected (default trial)
      sessionStorage.removeItem('selectedPlanId')
      sessionStorage.setItem('registrationSource', 'pricing-default-trial')
      
      // Redirect to registration page
      this.$router.push({ name: 'tenant-register' })
    },

    handlePlanSelection(plan) {
      // Plan selected - show message and redirect to registration
      this.selectedPlan = plan
      
      // Store selected plan in sessionStorage for registration
      sessionStorage.setItem('selectedPlanId', plan.id)
      sessionStorage.setItem('selectedPlanName', plan.name)
      sessionStorage.setItem('selectedPlanPrice', plan.price)
      sessionStorage.setItem('registrationSource', 'pricing-plan-selected')
      
      // Show message and redirect
      Swal.fire({
        icon: 'info',
        title: 'Register to Purchase Plan',
        html: `
          <p style="font-size: 1.1rem; margin-bottom: 1rem;">
            You've selected the <strong style="color: #c62828;">${plan.name}</strong> plan.
          </p>
          <p style="font-size: 1rem; color: #666;">
            Please register your restaurant to complete the purchase. After registration, you'll be able to complete the payment and activate your subscription.
          </p>
        `,
        confirmButtonText: 'Register Now',
        confirmButtonColor: '#c62828',
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#6c757d'
      }).then((result) => {
        if (result.isConfirmed) {
          // Redirect to registration page
          this.$router.push({ name: 'tenant-register' })
        } else {
          // Clear selection if cancelled
          sessionStorage.removeItem('selectedPlanId')
          sessionStorage.removeItem('selectedPlanName')
          sessionStorage.removeItem('selectedPlanPrice')
          sessionStorage.removeItem('registrationSource')
        }
      })
    },

    openEmailModal(plan) {
      // Legacy method - redirect to registration instead
      this.handlePlanSelection(plan)
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
        await axios.post('/subscribe/request', { plan_id: this.selectedPlan.id, email: this.email })
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
        await axios.post('/subscribe/verify', { email: this.email, code: this.code })
        this.showCodeModal = false
        this.showPaymentModal = true
        this.stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY)

        const response = await axios.post('/acreate-payment-intent', {
          amount: this.planAmount,
          currency: 'usd',
          email: this.email,
          plan_id: this.selectedPlan.id,
          plan_name: this.selectedPlan.name
        })

        this.clientSecret = response.data.clientSecret
        this.elements = this.stripe.elements({ clientSecret: this.clientSecret })
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
  this.paymentLoading = true;
  this.paymentMessage = null;

  try {
    // Define the return URL where the user will be redirected after PayPal completes the payment
    const returnUrl = `${window.location.origin}/payment-success`;  // Replace with your desired URL

    const { error, paymentIntent } = await this.stripe.confirmPayment({
      elements: this.elements,
      redirect: 'if_required',
      confirmParams: {
        return_url: returnUrl,  // Add the return_url here
      },
    });

    if (error) {
      this.paymentMessage = error.message;
    } else if (paymentIntent?.status === 'succeeded') {
      await axios.post('/subscription/activate', {
        email: this.email,
        plan_id: this.selectedPlan.id,
        amount: this.selectedPlan.price,
        stripe_payment_intent_id: paymentIntent.id,
      });

      Swal.fire({
        icon: 'success',
        title: 'Payment Successful!',
        text: 'Your subscription is now active! You will receive a confirmation email shortly.',
        confirmButtonText: 'Great!',
      });

      this.closeModal();
    }
  } catch (error) {
    this.paymentMessage = 'Payment processing failed. Please try again.';
    console.error('Payment error:', error);
  } finally {
    this.paymentLoading = false;
  }
}

  },

  mounted() {
    this.fetchPlans()
    this.fetchPaymentMethods()  // Fetch payment methods dynamically when the component is mounted
  }
}
</script>

<style scoped>
.pricing-page {
  background-color: #fff;
  min-height: 100vh;
}

/* Pricing Cards */
.pricing-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
  padding: 2rem;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid #f0f0f0;
}

.pricing-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
}

.pricing-card.featured {
  border: 2px solid #c62828;
  position: relative;
}

.pricing-card.featured::before {
  content: 'Popular';
  position: absolute;
  top: -12px;
  right: 20px;
  background: #c62828;
  color: white;
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 600;
}

.pricing-card-header {
  text-align: center;
  padding-bottom: 1.5rem;
  border-bottom: 1px solid #f0f0f0;
}

.pricing-card-body {
  padding: 1.5rem 0;
}

.pricing-card-footer {
  border-top: 1px solid #f0f0f0;
  padding-top: 1.5rem;
}

.price {
  text-align: center;
  margin: 1rem 0;
}

/* Payment Methods */
.payment-method-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
  padding: 1.5rem;
  text-align: center;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid #f0f0f0;
}

.payment-method-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.12);
}

.payment-method-icon-wrapper {
  width: 80px;
  height: 80px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8f9fa;
  border-radius: 8px;
  padding: 1rem;
}

.payment-method-icon {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

/* Modals */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
  padding: 1rem;
}

.modal-box {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  max-width: 500px;
  width: 100%;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
  animation: modalFadeIn 0.3s ease;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-actions {
  display: flex;
  gap: 0.75rem;
}

.btn-danger:hover {
  background: #b71c1c !important;
  border-color: #b71c1c !important;
}

.btn-danger:disabled {
  background: #ccc !important;
  border-color: #ccc !important;
  cursor: not-allowed;
}

.form-control:focus {
  border-color: #c62828;
  box-shadow: 0 0 0 0.25rem rgba(198, 40, 40, 0.25);
  outline: none;
}

/* Responsive Styles */
@media (max-width: 991.98px) {
  .pricing-header h1 {
    font-size: 2.2rem !important;
  }

  .payment-methods h2 {
    font-size: 2rem !important;
  }
}

@media (max-width: 768px) {
  .pricing-header {
    margin-top: 80px !important;
    padding: 2rem 0 !important;
  }

  .pricing-header h1 {
    font-size: 1.8rem !important;
  }

  .pricing-card {
    padding: 1.5rem;
  }

  .payment-method-card {
    padding: 1rem;
  }

  .payment-method-icon-wrapper {
    width: 60px;
    height: 60px;
  }

  .modal-box {
    padding: 1.5rem;
    margin: 1rem;
  }
}

@media (max-width: 576px) {
  .pricing-header h1 {
    font-size: 1.5rem !important;
  }

  .pricing-card-header h3 {
    font-size: 1.25rem !important;
  }

  .price .amount {
    font-size: 2.5rem !important;
  }

  .modal-actions {
    flex-direction: column;
  }

  .modal-actions .btn {
    width: 100%;
  }
}
</style>
