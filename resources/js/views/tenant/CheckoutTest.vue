<template>
  <div class="checkout-test">
    <div class="header">
      <h2>Test Payment Checkout</h2>
      <p>Test your Stripe integration with real payments</p>
    </div>

    <!-- Debug Info -->
    <div v-if="debugInfo" class="debug-info">
      <h3>Debug Information</h3>
      <pre>{{ debugInfo }}</pre>
      <button @click="debugInfo = null" class="btn btn-sm">Hide Debug</button>
    </div>

    <div class="checkout-container">
      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <p>Loading payment methods...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="loadError" class="error-state">
        <h3>Failed to load payment methods</h3>
        <p>{{ loadError }}</p>
        <button @click="loadPaymentMethods" class="btn btn-primary">Retry</button>
      </div>

      <!-- No Payment Methods -->
      <div v-else-if="paymentMethods.length === 0" class="no-methods">
        <h3>No Payment Methods Available</h3>
        <p>Please configure payment gateways in the admin panel first.</p>
        <button @click="$router.push('/dashboard/payment-gateways')" class="btn btn-primary">
          Configure Payment Gateways
        </button>
      </div>

      <!-- Payment Methods -->
      <div v-else class="payment-methods">
        <h3>Select Payment Method</h3>
        <div class="methods-grid">
          <div 
            v-for="method in paymentMethods" 
            :key="method.name"
            class="method-card"
            :class="{ active: selectedMethod === method.name }"
            @click="selectMethod(method.name)"
          >
            <div class="method-icon">
              <span v-if="method.name === 'stripe'">💳</span>
              <span v-else>🔗</span>
            </div>
            <div class="method-info">
              <h4>{{ method.display_name }}</h4>
              <span v-if="method.is_default" class="default-badge">Default</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Checkout Form -->
      <div class="checkout-form" v-if="selectedMethod && paymentMethods.length > 0">
        <div class="form-group">
          <label>Test Amount</label>
          <div class="amount-input">
            <select v-model="currency" class="currency-select">
              <option value="usd">USD</option>
              <option value="eur">EUR</option>
              <option value="gbp">GBP</option>
              <option value="inr">INR</option>
            </select>
            <input 
              type="number" 
              v-model="amount" 
              placeholder="0.00" 
              min="0.5" 
              max="1000"
              step="0.01"
            >
          </div>
          <small class="help-text">Test with small amounts (e.g., $1.00)</small>
        </div>

        <!-- Stripe Card Element -->
        <div v-if="selectedMethod === 'stripe'" class="stripe-element">
          <div ref="cardElement" class="card-element"></div>
          <div v-if="cardError" class="error-message">{{ cardError }}</div>
        </div>

        <!-- Test Cards Info -->
        <div class="test-cards" v-if="selectedMethod === 'stripe'">
          <h4>Test Card Numbers:</h4>
          <div class="test-cards-grid">
            <div class="test-card">
              <strong>4242 4242 4242 4242</strong>
              <span>Visa - Success</span>
            </div>
            <div class="test-card">
              <strong>4000 0000 0000 0002</strong>
              <span>Visa - Declined</span>
            </div>
            <div class="test-card">
              <strong>5555 5555 5555 4444</strong>
              <span>MasterCard - Success</span>
            </div>
          </div>
        </div>

        <!-- Payment Button -->
        <button 
          @click="processPayment" 
          :disabled="processingPayment || !amount || amount < 0.5"
          class="pay-button"
        >
          <span v-if="processingPayment">Processing...</span>
          <span v-else>Pay {{ currency.toUpperCase() }} {{ amount }}</span>
        </button>

        <!-- Payment Result -->
        <div v-if="paymentResult" class="payment-result" :class="paymentResult.success ? 'success' : 'error'">
          <div class="result-icon">
            {{ paymentResult.success ? '✅' : '❌' }}
          </div>
          <div class="result-content">
            <h4>{{ paymentResult.success ? 'Payment Successful!' : 'Payment Failed' }}</h4>
            <p>{{ paymentResult.message }}</p>
            
            <div v-if="paymentResult.success" class="success-details">
              <p><strong>Transaction ID:</strong> {{ paymentResult.transaction_id }}</p>
              <p><strong>Order Reference:</strong> {{ paymentResult.order_reference }}</p>
              <p><strong>Amount:</strong> {{ paymentResult.amount }} {{ paymentResult.currency?.toUpperCase() }}</p>
              
              <button @click="refundPayment" class="btn btn-outline" :disabled="refunding">
                {{ refunding ? 'Refunding...' : 'Refund Test Payment' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { loadStripe } from '@stripe/stripe-js';

let stripe;
let cardElement;

export default {
  name: 'CheckoutTest',
  data() {
    return {
      paymentMethods: [],
      selectedMethod: null,
      amount: 1.00,
      currency: 'usd',
      processingPayment: false,
      paymentResult: null,
      cardError: null,
      clientSecret: null,
      refunding: false,
      loading: false,
      loadError: null,
      debugInfo: null
    }
  },
  async mounted() {
    await this.loadPaymentMethods();
    
    // Initialize Stripe when component mounts
    this.$nextTick(() => {
      this.loadStripeJS();
    });
  },
  methods: {
    async loadPaymentMethods() {
      this.loading = true;
      this.loadError = null;
      this.debugInfo = null;
      
      try {
        console.log('🔄 Loading payment methods from /tenant/checkout/payment-methods');
        
        const response = await axios.get('/tenant/checkout/payment-methods');
        console.log('✅ Payment methods API response:', response.data);
        
        if (response.data.success) {
          this.paymentMethods = response.data.payment_methods || [];
          console.log('✅ Payment methods loaded:', this.paymentMethods);
          
          // Auto-select first available method
          if (this.paymentMethods.length > 0) {
            this.selectedMethod = this.paymentMethods[0].name;
            console.log('✅ Auto-selected method:', this.selectedMethod);
          }
        } else {
          throw new Error(response.data.message || 'Failed to load payment methods');
        }
        
      } catch (error) {
        console.error('❌ Error loading payment methods:', error);
        
        this.loadError = error.response?.data?.message || error.message;
        
        // Detailed debug info
        this.debugInfo = {
          error: {
            message: error.message,
            response: error.response?.data,
            status: error.response?.status,
            url: error.config?.url
          },
          timestamp: new Date().toISOString()
        };
        
        this.$toast.error('Failed to load payment methods: ' + this.loadError);
      } finally {
        this.loading = false;
      }
    },

    async loadStripeJS() {
      try {
        console.log('🔄 Loading Stripe.js...');
        
        // Use your test publishable key
        stripe = await loadStripe('pk_test_51RyT1d2MCIBAsjNoBiqu950NoNFsB0OBWIMTgyeAUojV7QPTLJbeIGzVF6C6KOWnUU0NNXqPAtGAojJBT9X2GrfR00y0FgXiWM');
        
        if (stripe) {
          console.log('✅ Stripe loaded successfully');
          
          const elements = stripe.elements();
          cardElement = elements.create('card', {
            style: {
              base: {
                fontSize: '16px',
                color: '#424770',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                '::placeholder': {
                  color: '#aab7c4',
                },
              },
              invalid: {
                color: '#9e2146',
              },
            },
          });
          
          // Wait for card element ref to be available
          this.$nextTick(() => {
            if (this.$refs.cardElement) {
              cardElement.mount(this.$refs.cardElement);
              console.log('✅ Card element mounted successfully');
              
              cardElement.on('change', (event) => {
                this.cardError = event.error ? event.error.message : '';
                if (event.complete) {
                  console.log('✅ Card input complete');
                }
              });
            } else {
              console.error('❌ Card element ref not found');
            }
          });
        } else {
          console.error('❌ Failed to load Stripe');
          this.$toast.error('Failed to load Stripe payment system');
        }
      } catch (error) {
        console.error('❌ Error loading Stripe:', error);
        this.$toast.error('Error loading payment system: ' + error.message);
      }
    },

    selectMethod(methodName) {
      console.log('🔄 Selecting payment method:', methodName);
      this.selectedMethod = methodName;
      this.paymentResult = null;
      this.cardError = null;
      
      if (methodName === 'stripe') {
        // Re-mount card element when switching to Stripe
        this.$nextTick(() => {
          if (this.$refs.cardElement && cardElement) {
            cardElement.mount(this.$refs.cardElement);
            console.log('✅ Card element re-mounted for Stripe');
          }
        });
      }
    },

    async processPayment() {
      if (this.selectedMethod === 'stripe') {
        await this.processStripePayment();
      } else {
        this.$toast.error('Payment method not implemented yet');
      }
    },

    async processStripePayment() {
  this.processingPayment = true;
  this.paymentResult = null;
  this.cardError = null;

  try {
    console.log('🔄 Creating test payment...');
    
    // 1. Create payment intent
    const paymentResponse = await axios.post('/tenant/checkout/test-payment', {
      amount: parseFloat(this.amount),
      currency: this.currency,
      gateway: this.selectedMethod
    });

    console.log('✅ Payment intent response:', paymentResponse.data);

    if (!paymentResponse.data.success) {
      throw new Error(paymentResponse.data.message || 'Failed to create payment intent');
    }

    this.clientSecret = paymentResponse.data.client_secret;

    // 2. Confirm payment with Stripe
    console.log('🔄 Confirming payment with Stripe...');
    const { error, paymentIntent } = await stripe.confirmCardPayment(this.clientSecret, {
      payment_method: {
        card: cardElement,
      }
    });

    if (error) {
      console.error('❌ Stripe confirmation error:', error);
      throw new Error(error.message);
    }

    console.log('✅ Stripe payment confirmed:', paymentIntent);

    // 3. Confirm payment with our backend
    const confirmResponse = await axios.post('/tenant/checkout/confirm-payment', {
      payment_intent_id: paymentIntent.id,
      gateway: this.selectedMethod
    });

    console.log('✅ Backend confirmation:', confirmResponse.data);

    // FIX: Check if response data exists and has success property
    if (confirmResponse.data && confirmResponse.data.success) {
      this.paymentResult = confirmResponse.data;
      
      if (confirmResponse.data.payment_status === 'succeeded') {
        this.$toast.success('Test payment completed successfully!');
      } else {
        this.$toast.warning(`Payment status: ${confirmResponse.data.payment_status}`);
      }
    } else {
      throw new Error(confirmResponse.data?.message || 'Invalid response from server');
    }

  } catch (error) {
    console.error('❌ Payment error:', error);
    
    // FIX: Better error handling
    this.paymentResult = {
      success: false,
      message: error.response?.data?.message || error.message || 'Unknown payment error'
    };
    
    this.$toast.error('Payment failed: ' + this.paymentResult.message);
  } finally {
    this.processingPayment = false;
  }
},

    async refundPayment() {
      if (!this.paymentResult?.transaction_id) {
        this.$toast.error('No transaction ID found for refund');
        return;
      }

      this.refunding = true;
      
      try {
        console.log('🔄 Processing refund...');
        
        const response = await axios.post('/tenant/checkout/refund-payment', {
          payment_intent_id: this.paymentResult.transaction_id,
          gateway: this.selectedMethod
        });

        console.log('✅ Refund response:', response.data);

        if (response.data.success) {
          this.$toast.success('Payment refunded successfully');
          this.paymentResult = null;
        } else {
          this.$toast.error(response.data.message);
        }
      } catch (error) {
        console.error('❌ Refund error:', error);
        this.$toast.error('Refund failed: ' + error.message);
      } finally {
        this.refunding = false;
      }
    }
  }
}
</script>

<style scoped>
.checkout-test {
  max-width: 600px;
  margin: 0 auto;
  padding: 2rem;
}

/* Responsive Design */
@media (max-width: 767.98px) {
  .checkout-test {
    max-width: 100%;
    padding: 1.5rem;
  }
  .header h2 {
    font-size: 1.5rem;
  }
  .header p {
    font-size: 0.9rem;
  }
  .methods-grid {
    grid-template-columns: 1fr;
  }
  .method-card {
    padding: 0.75rem;
  }
  .amount-input {
    flex-direction: column;
  }
  .currency-select {
    width: 100%;
    margin-bottom: 0.5rem;
  }
  .pay-button {
    font-size: 1rem;
    padding: 0.875rem 1.5rem;
  }
}

/* Android-specific fixes */
@media screen and (-webkit-min-device-pixel-ratio: 0) {
  .checkout-test {
    -webkit-text-size-adjust: 100%;
    -webkit-font-smoothing: antialiased;
  }
  
  input, select {
    -webkit-appearance: none;
    -webkit-border-radius: 6px;
    border-radius: 6px;
    font-size: 16px; /* Prevents zoom on Android */
  }
  
  button, .btn, .pay-button {
    -webkit-tap-highlight-color: rgba(59, 130, 246, 0.2);
    touch-action: manipulation;
  }
  
  .method-card {
    -webkit-tap-highlight-color: rgba(59, 130, 246, 0.1);
    touch-action: manipulation;
  }
}

@media (max-width: 575.98px) {
  .checkout-test {
    padding: 1rem;
  }
  .header h2 {
    font-size: 1.25rem;
  }
  .header p {
    font-size: 0.85rem;
  }
  .method-card {
    padding: 0.625rem;
  }
  .method-icon {
    font-size: 1.25rem;
  }
  .form-group label {
    font-size: 0.9rem;
  }
  .amount-input input,
  .currency-select {
    font-size: 0.9rem;
    padding: 0.625rem;
  }
  .card-element {
    padding: 0.75rem;
  }
  .test-cards {
    padding: 1rem;
  }
  .test-cards h4 {
    font-size: 0.95rem;
  }
  .test-card {
    padding: 0.625rem;
  }
  .pay-button {
    font-size: 0.95rem;
    padding: 0.75rem 1.25rem;
  }
  .payment-result {
    padding: 1rem;
  }
  .result-icon {
    font-size: 2rem;
  }
}

/* Debug Info Styles */
.debug-info {
  background: #f3f4f6;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1rem;
  font-family: 'Courier New', monospace;
  font-size: 0.75rem;
  max-height: 300px;
  overflow-y: auto;
}

.debug-info pre {
  margin: 0;
  white-space: pre-wrap;
  word-break: break-all;
}

.debug-info h3 {
  margin-top: 0;
  color: #374151;
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 3rem;
  background: #f9fafb;
  border-radius: 8px;
  color: #6b7280;
}

.loading-state p {
  margin: 0;
  font-size: 1.1rem;
}

/* Error State */
.error-state {
  text-align: center;
  padding: 2rem;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 8px;
  color: #dc2626;
}

.error-state h3 {
  margin-top: 0;
  color: #dc2626;
}

/* No Methods State */
.no-methods {
  text-align: center;
  padding: 2rem;
  background: #fffbeb;
  border: 1px solid #fed7aa;
  border-radius: 8px;
  color: #92400e;
}

.no-methods h3 {
  margin-top: 0;
  color: #92400e;
}

/* Payment Methods */
.methods-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 1rem;
  margin: 1rem 0;
}

.method-card {
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  padding: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.method-card:hover {
  border-color: #3b82f6;
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.method-card.active {
  border-color: #3b82f6;
  background-color: #f0f9ff;
  box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.1);
}

.method-icon {
  font-size: 1.5rem;
}

.default-badge {
  background: #10b981;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 500;
}

/* Form Styles */
.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.amount-input {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}

.currency-select {
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: white;
  font-size: 1rem;
  min-width: 80px;
}

.amount-input input {
  flex: 1;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 1rem;
}

.help-text {
  color: #6b7280;
  font-size: 0.875rem;
  margin-top: 0.5rem;
  display: block;
}

/* Stripe Element */
.stripe-element {
  margin: 1.5rem 0;
}

.card-element {
  padding: 1rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: white;
}

.error-message {
  color: #ef4444;
  font-size: 0.875rem;
  margin-top: 0.5rem;
  font-weight: 500;
}

/* Test Cards */
.test-cards {
  background: #f8fafc;
  padding: 1.5rem;
  border-radius: 8px;
  margin: 1.5rem 0;
  border: 1px solid #e2e8f0;
}

.test-cards h4 {
  margin-top: 0;
  margin-bottom: 1rem;
  color: #374151;
}

.test-cards-grid {
  display: grid;
  gap: 0.75rem;
}

.test-card {
  padding: 0.75rem;
  background: white;
  border-radius: 6px;
  border-left: 4px solid #3b82f6;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.test-card strong {
  display: block;
  font-family: 'Courier New', monospace;
  margin-bottom: 0.25rem;
}

.test-card span {
  color: #6b7280;
  font-size: 0.875rem;
}

/* Payment Button */
.pay-button {
  width: 100%;
  padding: 1rem 2rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-top: 1rem;
}

.pay-button:hover:not(:disabled) {
  background: #2563eb;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.pay-button:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* Payment Result */
.payment-result {
  padding: 1.5rem;
  border-radius: 8px;
  margin-top: 1.5rem;
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.payment-result.success {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
}

.payment-result.error {
  background: #fef2f2;
  border: 1px solid #fecaca;
}

.result-icon {
  font-size: 2.5rem;
  text-align: center;
  margin-bottom: 1rem;
}

.result-content h4 {
  margin-top: 0;
  margin-bottom: 0.5rem;
  text-align: center;
}

.result-content p {
  margin-bottom: 1rem;
  text-align: center;
}

.success-details {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #d1d5db;
}

.success-details p {
  margin-bottom: 0.5rem;
  text-align: left;
}

.btn {
  padding: 0.5rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: white;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn:hover:not(:disabled) {
  background: #f9fafb;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-primary {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
}

.btn-primary:hover:not(:disabled) {
  background: #2563eb;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
}

.btn-outline {
  background: transparent;
  border-color: #3b82f6;
  color: #3b82f6;
}

.btn-outline:hover:not(:disabled) {
  background: #3b82f6;
  color: white;
}
</style>