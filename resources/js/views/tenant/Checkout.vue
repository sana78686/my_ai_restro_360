<template>
  <div class="container mt-100">
    <div class="checkout-page container py-4">
      <div class="row">
        <div class="col-lg-8 mb-4">
          <!-- Stepper -->
          <div class="checkout-steps mb-4 d-flex flex-wrap justify-content-between">
            <div v-for="(step, idx) in steps" :key="step.key" class="checkout-step flex-fill text-center mb-2"
                 :class="{active: currentStep === idx, done: idx < currentStep}">
              <span class="step-index">{{ idx + 1 }}</span>
              <span class="step-title">{{ step.title }}</span>
            </div>
          </div>

          <!-- Order Type Tabs -->
          <div v-if="showOrderTypeTabs" class="order-type-tabs mb-4">
            <ul class="nav nav-tabs" role="tablist">
              <li class="nav-item" v-for="option in orderTypeOptions" :key="option.value">
                <button
                  class="nav-link"
                  :class="{ active: orderType === option.value }"
                  @click="selectOrderType(option.value)"
                  type="button"
                >
                  {{ option.label }}
                </button>
              </li>
            </ul>
          </div>

          <!-- Step Content -->
          <div class="checkout-step-content p-4 bg-white rounded shadow-sm mb-3">
            <!-- Step 1: Customer Details -->
            <form v-if="currentStep === 0" @submit.prevent="nextStep">
              <!-- Your existing customer details forms remain exactly the same -->
              <template v-if="orderType === 'online'">
                <h4 class="mb-3">{{ $t('check_out.form.deliveryAddress') }}</h4>
                <div class="row g-3">
                  <div class="col-md-6 col-12">
                    <label class="form-label">
                      {{ $t('check_out.form.f_name') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.firstName')"
                         style="cursor: help;"></i>
                    </label>
                    <input v-model="address.firstName" class="form-control" required />
                  </div>
                  <div class="col-md-6 col-12">
                    <label class="form-label">
                      {{ $t('check_out.form.l_name') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.lastName')"
                         style="cursor: help;"></i>
                    </label>
                    <input v-model="address.lastName" class="form-control" />
                  </div>
                  <div class="col-md-6 col-12">
                    <label class="form-label">
                      {{ $t('check_out.form.email') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.email')"
                         style="cursor: help;"></i>
                    </label>
                    <input v-model="address.email" type="email" class="form-control" required />
                  </div>
                  <div class="col-md-6 col-12">
                    <label class="form-label">
                      {{ $t('check_out.form.phone') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.phone')"
                         style="cursor: help;"></i>
                    </label>
                    <input v-model="address.phone" class="form-control" type="tel" required />
                  </div>
                  <div class="col-12">
                    <label class="form-label">
                      {{ $t('check_out.form.address') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.address')"
                         style="cursor: help;"></i>
                    </label>
                    <input type="text" class="form-control" v-model="address.address" required />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">
                      {{ $t('check_out.form.city') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.city')"
                         style="cursor: help;"></i>
                    </label>
                    <input v-model="address.city" class="form-control" required />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">
                      {{ $t('check_out.form.state') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.state')"
                         style="cursor: help;"></i>
                    </label>
                    <input v-model="address.state" class="form-control" required />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">
                      {{ $t('check_out.form.postal') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.postal')"
                         style="cursor: help;"></i>
                    </label>
                    <input v-model="address.postalCode" class="form-control" required />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">
                      {{ $t('check_out.form.country') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.country')"
                         style="cursor: help;"></i>
                    </label>
                    <input v-model="address.country" class="form-control" required />
                  </div>
                  <div class="col-12">
                    <label class="form-label">
                      {{ $t('check_out.form.specialInstructions') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.specialInstructions')"
                         style="cursor: help;"></i>
                    </label>
                    <textarea
                      v-model="address.specialInstructions"
                      class="form-control"
                      rows="2"
                      :placeholder="$t('check_out.form.specialInstructionsPlaceholder')"
                    ></textarea>
                  </div>
                </div>
              </template>

              <template v-else>
                <h4 class="mb-3">
                  {{ orderType === 'in_house' ? $t('check_out.orderTypes.inHouseOrder') : $t('check_out.orderTypes.takeawayOrder') }}
                </h4>
                <div class="row g-3">
                  <div class="col-md-6 col-12">
                    <label class="form-label">
                      {{ $t('check_out.in_house.name') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.name')"
                         style="cursor: help;"></i>
                    </label>
                    <input v-model="simpleOrder.name" class="form-control" required />
                  </div>
                  <div class="col-md-6 col-12">
                    <label class="form-label">
                      {{ $t('check_out.in_house.phone') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.phone')"
                         style="cursor: help;"></i>
                    </label>
                    <input v-model="simpleOrder.phone" class="form-control" type="tel" required />
                  </div>
                  <div class="col-md-6 col-12">
                    <label class="form-label">
                      {{ $t('check_out.in_house.email') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.emailOptional')"
                         style="cursor: help;"></i>
                    </label>
                    <input v-model="simpleOrder.email" class="form-control" type="email" />
                  </div>
                  <div v-if="orderType === 'in_house'" class="col-12">
                    <label class="form-label">
                      {{ $t('check_out.in_house.tableNo') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.tableNumber')"
                         style="cursor: help;"></i>
                    </label>
                    <input v-model="simpleOrder.tableNumber" class="form-control" :placeholder="$t('check_out.in_house.tableNoPlaceholder')" />
                  </div>
                  <div v-if="orderType === 'takeaway'" class="col-12">
                    <label class="form-label">
                      {{ $t('check_out.in_house.pickUpTime') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.pickupTime')"
                         style="cursor: help;"></i>
                    </label>
                    <input v-model="simpleOrder.pickupTime" type="time" class="form-control" />
                  </div>
                  <div class="col-12">
                    <label class="form-label">
                      {{ $t('check_out.in_house.specialInstructions') }}
                      <i class="fas fa-info-circle ms-1 text-muted"
                         data-bs-toggle="tooltip"
                         :data-bs-title="$t('check_out.tooltips.specialInstructions')"
                         style="cursor: help;"></i>
                    </label>
                    <textarea
                      v-model="simpleOrder.specialInstructions"
                      class="form-control"
                      rows="2"
                      :placeholder="$t('check_out.in_house.specialInstructionsPlaceholder')"
                    ></textarea>
                  </div>
                </div>
              </template>

              <div class="d-flex justify-content-end mt-4">
                <button class="btn btn-danger w-100 w-md-auto" type="submit">{{ $t('check_out.in_house.next') }}</button>
              </div>
            </form>

            <!-- Step 2: Payment Method - UPDATED TO SHOW ALL GATEWAYS -->
            <form v-else-if="currentStep === 1 && orderType === 'online'" @submit.prevent="handlePaymentSubmit">
              <h4 class="mb-3">{{ $t('check_out.delivery_method.deliveryMethod') }}</h4>

              <!-- Loading State for Gateways -->
              <div v-if="loadingPaymentMethods" class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                <span class="text-muted">{{ $t('check_out.delivery_method.deliveryTime') }}</span>
              </div>

              <div v-else class="payment-options">
                <!-- Cash on Delivery - ALWAYS AVAILABLE -->
                <div class="form-check mb-3 p-3 border rounded" :class="{ 'border-primary': paymentMethod === 'cash' }">
                  <input class="form-check-input" type="radio" id="cash" value="cash" v-model="paymentMethod" required />
                  <label class="form-check-label w-100" for="cash">
                    <div class="d-flex align-items-center">
                      <i class="fas fa-money-bill-wave text-success me-3 fs-4"></i>
                      <div>
                        <b>{{ $t('check_out.orderDetail.cashOnDelivery') }}</b>
                        <div class="text-muted small">{{ $t('check_out.orderDetail.payWhenreceive') }}</div>
                      </div>
                    </div>
                  </label>
                </div>

                <!-- All Available Payment Gateways -->
                <div v-for="gateway in availablePaymentMethods" :key="gateway.name"
                     class="form-check mb-3 p-3 border rounded gateway-option"
                     :class="{
                       'border-primary': paymentMethod === gateway.name,
                       'border-warning': gateway.test_mode
                     }">
                  <input class="form-check-input" type="radio"
                         :id="gateway.name"
                         :value="gateway.name"
                         v-model="paymentMethod" />
                  <label class="form-check-label w-100" :for="gateway.name">
                    <div class="d-flex align-items-center justify-content-between">
                      <div class="d-flex align-items-center">
                        <i class="fas fa-credit-card text-primary me-3 fs-4"></i>
                        <div>
                          <b>{{ gateway.display_name || gateway.name }}</b>
                          <div class="text-muted small">
                            {{ gateway.description || $t('check_out.payment.secureOnlinePayment') }}
                            <span v-if="gateway.test_mode" class="text-info ms-1">{{ $t('check_out.payment.testMode') }}</span>
                          </div>
                        </div>
                      </div>
                      <div v-if="gateway.is_default" class="badge bg-success ms-2">
                        {{ $t('check_out.payment.default') }}
                      </div>
                    </div>
                  </label>
                </div>

                <!-- Stripe Checkout Info -->
                <div v-if="paymentMethod === 'stripe'" class="stripe-checkout-info mt-3 p-3 border rounded bg-light">
                  <div class="d-flex align-items-start">
                    <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                    <div>
                      <h6 class="mb-1">{{ $t('check_out.checkout.secureStripe') }}</h6>
                      <p class="mb-2 small">
                      {{   $t('check_out.checkout.trustText') }}
                      </p>
                      <div class="test-cards-info">
                        <strong class="small">{{ $t('check_out.payment.testCards') }}:</strong>
                        <div class="test-cards-grid mt-1">
                          <div class="test-card">
                            <strong>4242 4242 4242 4242</strong>
                            <span>{{ $t('check_out.payment.visaSuccess') }}</span>
                          </div>
                          <div class="test-card">
                            <strong>4000 0000 0000 0002</strong>
                            <span>{{ $t('check_out.payment.visaDeclined') }}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Other Gateway Info -->
                <div v-if="paymentMethod && paymentMethod !== 'cash' && paymentMethod !== 'stripe'"
                     class="gateway-info mt-3 p-3 border rounded bg-light">
                  <div class="d-flex align-items-start">
                    <i class="fas fa-info-circle text-info me-2 mt-1"></i>
                    <div>
                      <h6 class="mb-1">{{ $t('check_out.payment.securePayment', { method: paymentMethod }) }}</h6>
                      <p class="mb-2 small">
                      {{   $t('check_out.checkout.trustText') }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- No Active Gateways Message -->
                <div v-if="availablePaymentMethods.length === 0 && !loadingPaymentMethods" class="alert alert-info">
                  <i class="fas fa-info-circle me-2"></i>
                   {{   $t('check_out.orderDetail.gateWayOff') }}
                </div>
              </div>

              <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-secondary w-50 me-2" type="button" @click="prevStep">{{ $t('check_out.buttons.back') }}</button>
                <button class="btn btn-danger w-50" type="submit" :disabled="placingOrder || processingStripeCheckout">
                  <span v-if="placingOrder || processingStripeCheckout" class="spinner-border spinner-border-sm me-2"></span>
                  <span v-if="paymentMethod === 'cash'">{{ $t('check_out.buttons.placeOrderNow') }}</span>
                  <span v-else-if="paymentMethod === 'stripe'">{{ $t('check_out.buttons.payWithStripe') }}</span>
                  <span v-else>{{ $t('check_out.buttons.proceedToPayment') }}</span>
                </button>
              </div>
            </form>

            <!-- Skip Payment for In-House & Takeaway -->
            <div v-else-if="currentStep === 1 && (orderType === 'in_house' || orderType === 'takeaway')">
              <h4 class="mb-3">{{ $t('check_out.payment.paymentInformation') }}</h4>
              <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                {{ orderType === 'in_house'
                  ? $t('check_out.payment.paymentInHouse')
                  : $t('check_out.payment.paymentTakeaway')
                }}
              </div>

              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="agreeTerms" v-model="agreeTerms" required />
                <label class="form-check-label" for="agreeTerms">
                  {{   $t('check_out.orderDetail.terms') }}
                </label>
              </div>

              <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-secondary w-50 me-2" type="button" @click="prevStep">{{ $t('check_out.buttons.back') }}</button>
                <button class="btn btn-danger w-50" @click="handleInHouseTakeawayOrder" :disabled="!agreeTerms || placingOrder">
                  <span v-if="placingOrder" class="spinner-border spinner-border-sm me-2"></span>
                  {{ $t('check_out.buttons.confirmOrder') }}
                </button>
              </div>
            </div>

            <!-- Thank You Page -->
            <div v-if="orderPlaced">
              <div class="text-center py-5">
                <div class="success-animation mb-4">
                  <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h3 class="mb-3 text-success">{{ $t('check_out.success.orderConfirmed') }}</h3>
                <p class="mb-4">{{ $t('check_out.success.thankYouOrder', { orderNumber: orderNumber }) }}</p>

                <a
    :href="invoiceUrl"
    target="_blank"
    class="btn btn-outline-danger btn-lg"
  >
    <i class="fas fa-file-invoice me-2"></i>
    {{ $t('viewInvoice') }}
  </a>

                  <!-- Order Summary -->
                  <div class="p-3 bg-white rounded border">
                    <!-- <div class="d-flex justify-content-between mb-1">
                      <span>{{ $t('check_out.review.subtotal') }}:</span>
                      <span>{{ settings.currency_symbol || '$' }}{{ orderDetails.subtotal.toFixed(2) }}</span>
                    </div> -->
                    <!-- <div v-if="orderDetails.deliveryFee > 0" class="d-flex justify-content-between mb-1">
                      <span>{{ $t('check_out.review.deliveryFee') }}:</span>
                      <span>{{ settings.currency_symbol || '$' }}{{ orderDetails.deliveryFee.toFixed(2) }}</span>
                    </div> -->
                    <!-- <div v-if="orderDetails.discountAmount > 0" class="d-flex justify-content-between mb-1 text-success">
                      <span>{{ $t('check_out.review.discount', { percent: settings.discount }) }}:</span>
                      <span>-{{ settings.currency_symbol || '$' }}{{ orderDetails.discountAmount.toFixed(2) }}</span>
                    </div> -->
                    <!-- <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-2 mt-2">
                      <span>{{ $t('check_out.review.grandTotal') }}:</span>
                      <span class="text-danger">{{ settings.currency_symbol || '$' }}{{ orderDetails.grandTotal.toFixed(2) }}</span>
                    </div> -->
                  </div>
                </div>

                <div class="alert alert-info text-start mb-4">
                  <h6 class="alert-heading">{{ $t('check_out.success.whatsNext') }}</h6>
                  <div v-if="orderType === 'online'">
                    {{ $t('check_out.success.onlineNext') }}
                  </div>
                  <div v-else-if="orderType === 'in_house'">
                    {{ $t('check_out.success.inHouseNext') }}
                  </div>
                  <div v-else-if="orderType === 'takeaway'">
                    {{ $t('check_out.success.takeawayNext') }}
                  </div>
                </div>

                <button class="btn btn-danger mt-3" @click="goToShop">{{ $t('check_out.buttons.continueShopping') }}</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="col-lg-4">
          <div class="order-summary bg-white rounded shadow-sm p-3 mb-4 sticky-top" style="top: 20px;">
            <h5 class="mb-3"><i class="fas fa-list-alt me-2"></i>{{ $t('check_out.summary.orderSummary') }}</h5>
            <div class="mb-3">
              <strong>{{ $t('check_out.summary.orderType') }}:</strong>
              <span class="badge bg-primary ms-2">{{ orderTypeLabels[orderType] }}</span>
            </div>
            <div v-if="cart.cart.items.length">
              <div v-for="item in cart.cart.items" :key="item.id" class="d-flex align-items-center mb-2 pb-2 border-bottom">
                <img
                  v-if="item.image"
                  :src="item.image"
                  :alt="item.name"
                  class="me-2 rounded"
                  style="width: 50px; height: 50px; object-fit: cover;"
                >
                <div class="ms-2 flex-grow-1">
                  <div class="fw-bold" style="font-size: 0.9rem;">{{ item.name }}</div>
                  <div class="text-muted" style="font-size:0.85em;">
                    {{ settings.currency_symbol || '$' }}{{ item.price }} x {{ item.quantity }}
                  </div>
                </div>
                <div class="fw-bold" style="font-size: 0.9rem;">
                  {{ settings.currency_symbol || '$' }}{{ (item.price * item.quantity).toFixed(2) }}
                </div>
              </div>
              <div class="border-top pt-2 mt-2">
                <div class="d-flex justify-content-between mb-1">
                  <span>{{ $t('check_out.summary.subtotal') }}:</span>
                  <span>{{ settings.currency_symbol || '$' }}{{ subtotal.toFixed(2) }}</span>
                </div>
                <div v-if="deliveryFee > 0" class="d-flex justify-content-between mb-1">
                  <span>{{ $t('check_out.summary.deliveryFee') }}:</span>
                  <span>{{ settings.currency_symbol || '$' }}{{ deliveryFee.toFixed(2) }}</span>
                </div>
                <div v-if="discountAmount > 0" class="d-flex justify-content-between mb-1 text-success">
                  <span>{{ $t('check_out.summary.discount', { percent: settings.discount }) }}:</span>
                  <span>-{{ settings.currency_symbol || '$' }}{{ discountAmount.toFixed(2) }}</span>
                </div>
                <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-2 mt-2">
                  <span>{{ $t('check_out.summary.grandTotal') }}:</span>
                  <span class="text-danger">{{ settings.currency_symbol || '$' }}{{ grandTotal.toFixed(2) }}</span>
                </div>
              </div>
            </div>
            <div v-else class="text-muted text-center py-4">
              <i class="fas fa-shopping-cart fa-2x mb-3"></i>
              <p>{{ $t('check_out.summary.cartEmpty') }}</p>
              <button class="btn btn-danger btn-sm" @click="$router.push({ name: 'tenant-landing' })">
                {{ $t('check_out.buttons.continueShopping') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  <!-- </div> -->
</template>

<script>
import { useRouter } from 'vue-router'
import { useCart } from '../../composables/useCart'
import axios from 'axios'
import Swal from 'sweetalert2'
import { inject, onMounted, ref, nextTick } from 'vue'
import { Tooltip } from 'bootstrap'

export default {
  name: 'Checkout',
  setup() {
    const router = useRouter()
    const cart = inject('cart', useCart())
    const settings = inject('settings', ref({}))

    return {
      router,
      cart,
      settings
    }
  },
  data() {
    return {
      // Your existing data properties
      isLoggedIn: false,
      userRole: '',
      orderType: 'online',
      currentStep: 0,
      orderPlaced: false,
      placingOrder: false,
      orderNumber: null,
      agreeTerms: false,
      orderDetails: {
        items: [],
        subtotal: 0,
        deliveryFee: 0,
        discountAmount: 0,
        grandTotal: 0
      },

      // Address data
      address: {
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        address: '',
        city: '',
        state: '',
        postalCode: '',
        country: '',
        specialInstructions: ''
      },

      // Simple order data
      simpleOrder: {
        name: '',
        phone: '',
        email: '',
        tableNumber: '',
        pickupTime: '',
        specialInstructions: ''
      },

      deliveryMethod: 'free',

      // Payment method
      paymentMethod: 'cash',

      // Payment gateways
      availablePaymentMethods: [],
      stripeGateway: null,
      loadingPaymentMethods: false,
      processingStripeCheckout: false
    }
  },
  computed: {
    // Your existing computed properties
    orderTypeOptions() {
      return [
        { value: 'online', label: this.$t('check_out.orderTypes.onlineDelivery') },
        { value: 'in_house', label: this.$t('check_out.orderTypes.inHouse') },
        { value: 'takeaway', label: this.$t('check_out.orderTypes.takeaway') }
      ];
    },
    orderTypeLabels() {
      return {
        'online': this.$t('check_out.orderTypes.onlineDelivery'),
        'in_house': this.$t('check_out.orderTypes.inHouse'),
        'takeaway': this.$t('check_out.orderTypes.takeaway')
      };
    },
    steps() {
      return [
        { key: 'details', title: this.$t('check_out.steps.customerDetails') },
        { key: 'payment', title: this.$t('check_out.steps.payment') }
      ];
    },
    showOrderTypeTabs() {
      if (!this.isLoggedIn) return false;
      const normalizedRole = this.userRole?.toLowerCase().replace(/\s+/g, '_');
      const allowedRoles = ['manager', 'restaurant_owner', 'order_taker', 'admin', 'staff'];
      return allowedRoles.includes(normalizedRole);
    },
    deliveryMethodLabel() {
      switch (this.deliveryMethod) {
        case 'free': return this.$t('check_out.deliveryMethod.freeDelivery')
        case 'express': return this.$t('check_out.deliveryMethod.expressDelivery', { symbol: this.settings.currency_symbol || '$' })
        default: return ''
      }
    },
    paymentMethodLabel() {
      if (this.paymentMethod === 'cash') return this.$t('check_out.payment.cashOnDelivery')
      if (this.paymentMethod === 'stripe') return this.$t('check_out.payment.stripeCheckout')
      // Find the gateway display name
      const gateway = this.availablePaymentMethods.find(g => g.name === this.paymentMethod)
      return gateway ? gateway.display_name || gateway.name : this.$t('check_out.payment.onlinePayment')
    },
    subtotal() {
      return this.cart.cart.items.reduce((sum, item) => sum + item.price * item.quantity, 0)
    },
    discountAmount() {
      return this.subtotal * (this.settings.discount / 100)
    },
    deliveryFee() {
      return this.orderType === 'online' && this.deliveryMethod === 'express' ? 2.99 : 0
    },
    grandTotal() {
      return this.subtotal - this.discountAmount + this.deliveryFee
    }
  },
  watch: {
    // Watch for step changes to load payment methods
    currentStep(newStep) {
      if (newStep === 1) {
        this.loadPaymentMethods();
      }
      // Reinitialize tooltips when step changes
      this.$nextTick(() => {
        this.initTooltips();
      });
    }
  },
  async mounted() {
    await this.checkUserAuth();

    // Check for Stripe checkout return
    this.checkStripeReturn();

    // Initialize tooltips
    this.initTooltips();
  },
  methods: {
    // Load available payment gateways
    async loadPaymentMethods() {
      if (this.orderType !== 'online') return;

      this.loadingPaymentMethods = true;

      try {
        const response = await axios.get('/tenant/checkout/payment-methods');

        if (response.data.success) {
          const gateways = response.data.payment_methods || [];

          // Store all active payment gateways
          this.availablePaymentMethods = gateways.filter(g => g.is_active);

          // Keep reference to Stripe for specific handling if needed
          this.stripeGateway = gateways.find(g => g.name.includes('stripe') && g.is_active);

          // Auto-select the default payment method or first available
          const defaultGateway = this.availablePaymentMethods.find(g => g.is_default) ||
                                this.availablePaymentMethods[0];

          if (defaultGateway) {
            this.paymentMethod = defaultGateway.name;
          }
        }
      } catch (error) {
        console.error('Error loading payment methods:', error);
        this.availablePaymentMethods = [];
        this.stripeGateway = null;
      } finally {
        this.loadingPaymentMethods = false;
      }
    },

    // Check if returning from Stripe checkout
    async checkStripeReturn() {
      const urlParams = new URLSearchParams(window.location.search);
      const sessionId = urlParams.get('session_id');
      const success = urlParams.get('success');

      if (sessionId && success === 'true') {
        await this.handleStripeReturn(sessionId);
      }
    },

    // Handle return from Stripe checkout
    async handleStripeReturn(sessionId) {
      try {
        this.placingOrder = true;

        // Verify the Stripe checkout session
        const verifyResponse = await axios.get(`/tenant/stripe/verify-checkout-session?session_id=${sessionId}`);

        if (verifyResponse.data.success) {
          const session = verifyResponse.data.session;

          // Create order with payment confirmed
          await this.createOrder('stripe', true, session.payment_intent);

          this.$toast.success(this.$t('check_out.messages.paymentSuccessful'));
        } else {
          throw new Error(this.$t('check_out.errors.paymentVerificationFailed'));
        }
      } catch (error) {
        console.error('Stripe return error:', error);
        this.$toast.error(this.$t('check_out.errors.paymentVerificationFailedContact'));
      } finally {
        this.placingOrder = false;
      }
    },

    // Redirect to Stripe Checkout
    async redirectToStripeCheckout() {
      this.processingStripeCheckout = true;

      try {
        // First, create a temporary order with pending status
        const tempOrderId = await this.createTempOrder();

        // Create Stripe Checkout Session
        const requestData = {
          gateway: 'stripe', // Explicitly send gateway
          order_id: String(tempOrderId), // Ensure it's a string
          amount: Math.round(this.grandTotal * 100),
          currency: 'usd',
          customer_email: this.address.email,
          customer_name: `${this.address.firstName} ${this.address.lastName}`,
          success_url: `${window.location.origin}${window.location.pathname}?session_id={CHECKOUT_SESSION_ID}&success=true`,
          cancel_url: `${window.location.origin}${window.location.pathname}?canceled=true`
        };

        console.log('Sending checkout request:', requestData);

        const response = await axios.post('/tenant/stripe/create-checkout-session', requestData);

        console.log('Stripe checkout response:', response.data);

        if (response.data.success && (response.data.sessionUrl || response.data.session_url)) {
          // Redirect to Stripe Checkout
          const sessionUrl = response.data.sessionUrl || response.data.session_url;
          window.location.href = sessionUrl;
        } else {
          const errorMessage = response.data.message || this.$t('check_out.errors.failedToCreateCheckoutSession');
          console.error('Checkout session error:', response.data);
          throw new Error(errorMessage);
        }
      } catch (error) {
        console.error('Stripe checkout error:', error);
        let errorMessage = this.$t('check_out.errors.failedToRedirectPayment');

        if (error.response) {
          // Server responded with error
          if (error.response.data) {
            if (error.response.data.message) {
              errorMessage = error.response.data.message;
            } else if (error.response.data.errors) {
              // Laravel validation errors
              const errors = error.response.data.errors;
              const firstError = Object.values(errors)[0];
              errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
            }
          }
        } else if (error.message) {
          errorMessage = error.message;
        }

        // Use toast if available, otherwise use Swal
        if (this.$toast) {
          this.$toast.error(errorMessage);
        } else {
          Swal.fire({
            icon: 'error',
            title: this.$t('check_out.errors.paymentError'),
            text: errorMessage,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000
          });
        }
        this.processingStripeCheckout = false;
      }
    },

    // Create temporary order for Stripe checkout
    async createTempOrder() {
      let customer, order;

      if (this.orderType === 'online') {
        customer = {
          name: this.address.firstName + ' ' + (this.address.lastName || ''),
          email: this.address.email,
          address: this.address.address,
          city: this.address.city,
          state: this.address.state,
          postal_code: this.address.postalCode,
          country: this.address.country,
          phone: this.address.phone,
          special_instructions: this.address.specialInstructions,
          device_info: navigator.userAgent,
          status: 'active'
        };

        order = {
          total_amount: this.grandTotal,
          subtotal_amount: this.subtotal,
          delivery_fee: this.deliveryFee,
          discount_amount: this.discountAmount,
          status: 'payment_pending',
          delivery_address: this.address.address,
          delivery_city: this.address.city,
          delivery_state: this.address.state,
          delivery_postal_code: this.address.postalCode,
          delivery_country: this.address.country,
          special_instructions: this.address.specialInstructions,
          currency_symbol: this.settings.currency_symbol || '$',
          order_type: this.orderType,
          delivery_method: this.deliveryMethod,
          payment_method: 'stripe',
          payment_status: 'pending'
        };
      } else {
        customer = {
          name: this.simpleOrder.name,
          email: this.simpleOrder.email,
          phone: this.simpleOrder.phone,
          device_info: navigator.userAgent,
          status: 'active'
        };

        order = {
          total_amount: this.grandTotal,
          subtotal_amount: this.subtotal,
          discount_amount: this.discountAmount,
          status: 'payment_pending',
          currency_symbol: this.settings.currency_symbol || '$',
          order_type: this.orderType,
          table_number: this.simpleOrder.tableNumber || null,
          pickup_time: this.simpleOrder.pickupTime || null,
          special_instructions: this.simpleOrder.specialInstructions || null,
          payment_method: 'stripe',
          payment_status: 'pending'
        };
      }

      const order_details = this.cart.cart.items.map(item => ({
        product_id: item.id,
        product_name: item.name,
        quantity: item.quantity,
        unit_price: item.price,
        total_price: item.price * item.quantity
      }));

      const response = await axios.post('/tenant/save-order', {
        customer,
        order,
        order_details
      });

      if (response.data.success) {
        return response.data.order_id;
      } else {
          throw new Error(this.$t('check_out.errors.failedToCreateTempOrder'));
      }
    },

    // Create final order after payment confirmation
    async createOrder(paymentMethod, paymentSuccess = false, paymentIntentId = null) {
      let customer, order;

      if (this.orderType === 'online') {
        customer = {
          name: this.address.firstName + ' ' + (this.address.lastName || ''),
          email: this.address.email,
          address: this.address.address,
          city: this.address.city,
          state: this.address.state,
          postal_code: this.address.postalCode,
          country: this.address.country,
          phone: this.address.phone,
          special_instructions: this.address.specialInstructions,
          device_info: navigator.userAgent,
          status: 'active'
        };

        order = {
          total_amount: this.grandTotal,
          subtotal_amount: this.subtotal,
          delivery_fee: this.deliveryFee,
          discount_amount: this.discountAmount,
          status: paymentSuccess ? 'confirmed' : 'pending',
          delivery_address: this.address.address,
          delivery_city: this.address.city,
          delivery_state: this.address.state,
          delivery_postal_code: this.address.postalCode,
          delivery_country: this.address.country,
          special_instructions: this.address.specialInstructions,
          currency_symbol: this.settings.currency_symbol || '$',
          order_type: this.orderType,
          delivery_method: this.deliveryMethod,
          payment_method: paymentMethod,
          payment_status: paymentSuccess ? 'paid' : 'pending',
          payment_intent_id: paymentIntentId
        };
      } else {
        customer = {
          name: this.simpleOrder.name,
          email: this.simpleOrder.email,
          phone: this.simpleOrder.phone,
          device_info: navigator.userAgent,
          status: 'active'
        };

        order = {
          total_amount: this.grandTotal,
          subtotal_amount: this.subtotal,
          discount_amount: this.discountAmount,
          status: 'confirmed',
          currency_symbol: this.settings.currency_symbol || '$',
          order_type: this.orderType,
          table_number: this.simpleOrder.tableNumber || null,
          pickup_time: this.simpleOrder.pickupTime || null,
          special_instructions: this.simpleOrder.specialInstructions || null,
          payment_method: 'cash'
        };
      }

      const order_details = this.cart.cart.items.map(item => ({
        product_id: item.id,
        product_name: item.name,
        quantity: item.quantity,
        unit_price: item.price,
        total_price: item.price * item.quantity
      }));

      const response = await axios.post('/tenant/save-order', {
        customer,
        order,
        order_details
      });

      if (response.data.success) {
        this.orderNumber = response.data.order_number || 'ORD-' + Date.now();

        // Store order details before clearing cart
        this.orderDetails = {
          items: [...this.cart.cart.items],
          subtotal: this.subtotal,
          deliveryFee: this.deliveryFee,
          discountAmount: this.discountAmount,
          grandTotal: this.grandTotal
        };

        this.cart.clearCart();
        this.orderPlaced = true;
        // Show thank you page - no need to go to next step
        await this.showRatingPrompt();
        return true;
      } else {
        throw new Error(response.data.message || this.$t('check_out.errors.orderNotSaved'));
      }
    },

    // Regular place order for cash payments
    async placeOrder() {
      if (this.placingOrder) return;

      this.placingOrder = true;

      try {
        await this.createOrder(this.paymentMethod);
      } catch (error) {
        console.error('Order error:', error);
        Swal.fire({
          icon: 'error',
          title: this.$t('check_out.errors.orderFailed'),
          text: error.response?.data?.message || error.message || this.$t('check_out.errors.orderCouldNotBeProcessed')
        });
      } finally {
        this.placingOrder = false;
      }
    },

    // Your existing other methods remain exactly the same
    async checkUserAuth() {
      try {
        const response = await axios.get('/tenant/user-role');

        if (response.data && response.data.data) {
          this.isLoggedIn = true;

          if (response.data.data.name) {
            this.userRole = response.data.data.name;
          } else if (response.data.data.role) {
            this.userRole = response.data.data.role;
          } else if (typeof response.data.data === 'string') {
            this.userRole = response.data.data;
          } else {
            this.userRole = '';
          }
        } else {
          this.isLoggedIn = false;
          this.userRole = '';
        }
      } catch (error) {
        console.log('User not logged in or error fetching role:', error);
        this.isLoggedIn = false;
        this.userRole = '';
      }
    },

    selectOrderType(type) {
      this.orderType = type;
      this.currentStep = 0;
      this.agreeTerms = false;
    },

    nextStep() {
      // Basic validation
      if (this.currentStep === 0) {
        if (this.orderType === 'online') {
          if (!this.address.firstName || !this.address.email || !this.address.phone ||
              !this.address.address || !this.address.city || !this.address.state ||
              !this.address.postalCode || !this.address.country) {
            Swal.fire({
              icon: 'error',
              title: this.$t('check_out.errors.missingInformation'),
              text: this.$t('check_out.errors.fillAllRequiredFields'),
            });
            return;
          }
        } else {
          if (!this.simpleOrder.name || !this.simpleOrder.phone) {
            Swal.fire({
              icon: 'error',
              title: this.$t('check_out.errors.missingInformation'),
              text: this.$t('check_out.errors.fillAllRequiredFields'),
            });
            return;
          }
        }
      }

      if (this.currentStep < this.steps.length - 1) this.currentStep++
    },

    async handlePaymentSubmit() {
      // If cash on delivery, place order directly
      if (this.paymentMethod === 'cash') {
        await this.placeOrder();
      } else if (this.paymentMethod === 'stripe') {
        // Redirect to Stripe checkout
        await this.redirectToStripeCheckout();
      } else {
        // Other payment gateways - place order (they handle payment separately)
        await this.placeOrder();
      }
    },

    async handleInHouseTakeawayOrder() {
      if (!this.agreeTerms) {
        Swal.fire({
          icon: 'error',
          title: this.$t('check_out.errors.missingInformation'),
          text: this.$t('check_out.errors.agreeToTerms'),
        });
        return;
      }
      await this.placeOrder();
    },

    prevStep() {
      if (this.currentStep > 0) this.currentStep--
    },

    async showRatingPrompt() {
      await Swal.fire({
        title: this.$t('check_out.rating.thankYou'),
        text: this.$t('check_out.rating.rateExperience'),
        icon: 'success',
        html: `<div id='swal-stars' style='font-size:2rem; color: #f6c700; display: flex; justify-content: center;'>
          <i class='fa fa-star' data-value='1'></i>
          <i class='fa fa-star' data-value='2'></i>
          <i class='fa fa-star' data-value='3'></i>
          <i class='fa fa-star' data-value='4'></i>
          <i class='fa fa-star' data-value='5'></i>
        </div>`,
        showConfirmButton: true,
        confirmButtonText: this.$t('check_out.rating.submitRating'),
        showCancelButton: true,
        cancelButtonText: this.$t('check_out.rating.skip'),
        didOpen: () => {
          let selected = 5;
          const stars = Swal.getHtmlContainer().querySelectorAll('.fa-star');
          stars.forEach((star, idx) => {
            star.style.cursor = 'pointer';
            star.addEventListener('mouseenter', () => {
              stars.forEach((s, i) => s.style.color = i <= idx ? '#f6c700' : '#ccc');
            });
            star.addEventListener('mouseleave', () => {
              stars.forEach((s, i) => s.style.color = i < selected ? '#f6c700' : '#ccc');
            });
            star.addEventListener('click', () => {
              selected = idx + 1;
              stars.forEach((s, i) => s.style.color = i < selected ? '#f6c700' : '#ccc');
              Swal.setInputValue(selected);
            });
          });
          Swal.setInputValue(selected);
        },
        preConfirm: () => {
          return Swal.getInput().value;
        },
        input: 'hidden',
        inputValue: 5
      }).then((result) => {
        if (result.isConfirmed) {
          axios.post('/tenant/save-rating', {
            rating: result.value,
            order_number: this.orderNumber
          }).catch(err => console.error('Failed to save rating:', err));
        }
      });
    },

    goToShop() {
      this.router.push({ name: 'tenant-landing' })
    },

    initTooltips() {
      this.$nextTick(() => {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
          new Tooltip(el);
        });
      });
    }
  }
}
</script>

computed: {
  // 🔽 existing computed properties yahin rahengi

  invoiceUrl() {
    if (!this.orderNumber) return '#'
    return `/tenant/orders/${this.orderNumber}/invoice`
  }
}


<style scoped>
/* Your existing CSS styles remain the same */
.checkout-page {
  background: #f8f9fa;
}
.checkout-steps {
  display: flex;
  gap: 1.5rem;
  margin-bottom: 2rem;
  flex-wrap: wrap;
}
.checkout-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  opacity: 0.5;
  transition: opacity 0.2s;
  min-width: 100px;
}
.checkout-step.active {
  opacity: 1;
  font-weight: bold;
}
.checkout-step.done {
  opacity: 0.8;
}
.step-index {
  background: #c62828;
  color: #fff;
  border-radius: 50%;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  margin-bottom: 0.3rem;
}
.checkout-step-title {
  font-size: 1rem;
}
.checkout-step-content {
  min-height: 350px;
}
.order-summary {
  min-width: 260px;
}

/* Order Type Tabs */
.order-type-tabs .nav-tabs .nav-link {
  font-weight: 500;
  color: #333;
  border: none;
  border-bottom: 2px solid transparent;
  padding: 12px 20px;
}
.order-type-tabs .nav-tabs .nav-link.active {
  color: #d9534f;
  border-bottom-color: #d9534f;
  background: transparent;
}
.order-type-tabs .nav-tabs .nav-link:hover {
  border-color: transparent;
  color: #d9534f;
}

/* Delivery & Payment Options */
.delivery-options .form-check,
.payment-options .form-check {
  transition: all 0.3s ease;
  cursor: pointer;
}
.delivery-options .form-check:hover,
.payment-options .form-check:hover {
  background-color: #f8f9fa;
}
.delivery-options .border-primary,
.payment-options .border-primary {
  border-color: #007bff !important;
  background-color: #f8f9ff;
}

/* Cart Items */
.cart-items {
  max-height: 300px;
  overflow-y: auto;
}
.cart-item:last-child {
  border-bottom: none !important;
}

/* Success Animation */
.success-animation {
  animation: bounceIn 0.6s ease;
}

@keyframes bounceIn {
  0% { transform: scale(0.3); opacity: 0; }
  50% { transform: scale(1.05); }
  70% { transform: scale(0.9); }
  100% { transform: scale(1); opacity: 1; }
}

/* Sticky sidebar */
.sticky-top {
  position: sticky;
  z-index: 10;
}

@media (max-width: 991.98px) {
  .checkout-page .row {
    flex-direction: column;
  }
  .order-summary {
    margin-top: 2rem;
    min-width: 100%;
  }
  .order-type-tabs .nav-tabs .nav-link {
    padding: 10px 15px;
    font-size: 0.9rem;
  }
}
@media (max-width: 767.98px) {
  .checkout-step {
    min-width: 80px;
    font-size: 0.95rem;
  }
  .checkout-step-content {
    padding: 1rem !important;
  }
  .order-summary {
    padding: 1rem !important;
  }
  .order-type-tabs .nav-tabs {
    flex-direction: column;
  }
  .order-type-tabs .nav-tabs .nav-link {
    border-bottom: none;
    border-left: 2px solid transparent;
    text-align: left;
  }
  .order-type-tabs .nav-tabs .nav-link.active {
    border-left-color: #d9534f;
    border-bottom: none;
  }
}
</style>
