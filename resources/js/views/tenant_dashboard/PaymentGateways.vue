<template>
  <div class="payment-gateways">
    <div class="header">
      <h2>{{ $t('tenant_dashboard.paymentGateway.title') }}</h2>
      <p>{{ $t('tenant_dashboard.paymentGateway.description') }}</p>

    </div>

    <!-- Status Information -->
    <div class="status-info">
      <div class="status-info-header">
        <i class="fas fa-info-circle"></i>
        <h4>{{ $t('tenant_dashboard.paymentGateway.statusTitle') }}</h4>
      </div>
      <div class="status-info-content">
        <div class="status-item">
          <span class="status-badge-demo badge badge-secondary">{{ $t('tenant_dashboard.paymentGateway.statuses.inactive') }}</span>
          <p><strong>{{ $t('tenant_dashboard.paymentGateway.statuses.inactive') }}:</strong> {{ $t('tenant_dashboard.paymentGateway.statuses.inactiveDesc') }}</p>
        </div>
        <div class="status-item">
          <span class="status-badge-demo badge badge-success">{{ $t('tenant_dashboard.paymentGateway.statuses.configured') }}</span>
          <p><strong>{{ $t('tenant_dashboard.paymentGateway.statuses.configured') }}:</strong> {{ $t('tenant_dashboard.paymentGateway.statuses.configuredDesc') }}</p>
        </div>
        <div class="status-item">
          <span class="status-badge-demo badge badge-success">{{ $t('tenant_dashboard.paymentGateway.statuses.active') }}</span>
          <p><strong>{{ $t('tenant_dashboard.paymentGateway.statuses.active') }}:</strong> {{ $t('tenant_dashboard.paymentGateway.statuses.activeDesc') }}</p>
        </div>
        <div class="status-item">
          <i class="fas fa-vial text-info"></i>
          <p><strong>{{ $t('tenant_dashboard.paymentGateway.statuses.testConnection') }}:</strong> {{ $t('tenant_dashboard.paymentGateway.statuses.testConnectionDesc') }}</p>
        </div>
      </div>
    </div>

    <!-- Debug Section -->
    <!-- <div class="debug-section" style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
      <h4>🔍 Debug Information</h4>
      <div v-for="gateway in gateways" :key="gateway.name" style="margin: 10px 0; padding: 10px; background: white; border-radius: 4px;">
        <strong>{{ gateway.display_name }}:</strong><br>
        • is_configured: <span :style="{ color: gateway.is_configured ? 'green' : 'red', fontWeight: 'bold' }">{{ gateway.is_configured = true }}</span><br>
        • is_active: {{ gateway.is_active }}<br>
        • is_default: {{ gateway.is_default }}<br>
        • status: {{ gateway.status }}
      </div>
    </div> -->

    <div class="gateways-grid">
      <div
        v-for="gateway in gateways"
        :key="gateway.name"
        class="gateway-card"
        :class="{
          'configured': gateway.is_configured,
          'active': gateway.is_active && gateway.is_configured,
          'default': gateway.is_default
        }"
      >
        <div class="gateway-header">
          <h3>{{ gateway.display_name }}</h3>
          <div class="status-badges">
            <span v-if="gateway.is_default" class="badge badge-primary">{{ $t('tenant_dashboard.paymentGateway.badges.default') }}</span>
            <span v-if="gateway.is_configured" class="badge badge-success">{{ $t('tenant_dashboard.paymentGateway.badges.configured') }}</span>
            <span v-if="gateway.is_active && gateway.is_configured" class="badge badge-success">{{ $t('tenant_dashboard.paymentGateway.badges.active') }}</span>
            <span v-else-if="gateway.is_configured" class="badge badge-secondary">{{ $t('tenant_dashboard.paymentGateway.badges.inactive') }}</span>
            <span v-else class="badge badge-secondary">{{ $t('tenant_dashboard.paymentGateway.badges.notConfigured') }}</span>
          </div>
        </div>

        <p class="gateway-description">{{ gateway.description }}</p>

        <div class="gateway-features">
          <span
            v-for="feature in gateway.supported_features"
            :key="feature"
            class="feature-tag"
          >
            {{ feature }}
          </span>
        </div>

        <div class="gateway-actions">
          <button
            v-if="!gateway.is_configured"
            @click="configureGateway(gateway.name)"
            class="btn btn-primary"
          >
            {{ $t('tenant_dashboard.paymentGateway.actions.configure') }}
          </button>

          <div v-else class="action-buttons">
            <button
              @click="configureGateway(gateway.name)"
              class="btn btn-outline-primary"
            >
              {{ $t('tenant_dashboard.paymentGateway.actions.edit') }}
            </button>
            <button
              v-if="!gateway.is_default"
              @click="setDefault(gateway.name)"
              class="btn btn-outline-success"
            >
              {{ $t('tenant_dashboard.paymentGateway.actions.setDefault') }}
            </button>
            <button
              @click="toggleActive(gateway.name)"
              :class="gateway.is_active ? 'btn btn-warning' : 'btn btn-success'"
            >
              {{ gateway.is_active ? $t('tenant_dashboard.paymentGateway.actions.deactivate') : $t('tenant_dashboard.paymentGateway.actions.activate') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Configuration Modal -->
    <div v-if="showConfigModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <div class="modal-header">
          <h3>{{ $t('tenant_dashboard.paymentGateway.modal.configureTitle', { name: currentGatewaySchema.display_name }) }}</h3>
          <button @click="closeModal" class="close-btn">&times;</button>
        </div>

        <div class="modal-body">
          <form @submit.prevent="saveConfiguration">
            <!-- Credentials Fields -->
            <div class="form-section">
              <h4>{{ $t('tenant_dashboard.paymentGateway.modal.credentials') }}</h4>
              <div
                v-for="(schema, field) in currentGatewaySchema.credentials_schema"
                :key="field"
                class="form-group"
              >
                <label :for="field">{{ schema.label }}</label>

                <input
                  v-if="schema.type === 'password'"
                  :type="showPasswords[field] ? 'text' : 'password'"
                  :id="field"
                  v-model="formData[field]"
                  @input="errors[field] = null"
                  :placeholder="schema.placeholder || schema.label"
                  :required="schema.required"
                  class="form-control"
                  :class="{ 'is-invalid': errors[field] }"
                >

                <input
                  v-else-if="schema.type === 'text'"
                  type="text"
                  :id="field"
                  v-model="formData[field]"
                  @input="errors[field] = null"
                  :placeholder="schema.placeholder || schema.label"
                  :required="schema.required"
                  class="form-control"
                  :class="{ 'is-invalid': errors[field] }"
                >

                <select
                  v-else-if="schema.type === 'select'"
                  :id="field"
                  v-model="formData[field]"
                  @change="errors[field] = null"
                  class="form-control"
                  :class="{ 'is-invalid': errors[field] }"
                >
                  <option value="">{{ $t('tenant_dashboard.paymentGateway.modal.selectField', { field: schema.label }) }}</option>
                  <option
                    v-for="option in schema.options"
                    :key="option"
                    :value="option"
                  >
                    {{ option }}
                  </option>
                </select>

                <div v-if="schema.type === 'password'" class="password-toggle">
                  <button
                    type="button"
                    @click="togglePassword(field)"
                    class="toggle-btn"
                  >
                    {{ showPasswords[field] ? $t('tenant_dashboard.paymentGateway.modal.hide') : $t('tenant_dashboard.paymentGateway.modal.show') }}
                  </button>
                </div>

                <div v-if="errors[field]" class="error-message">
                  {{ errors[field] }}
                </div>
              </div>
            </div>

            <!-- Configuration Fields -->
            <div v-if="Object.keys(currentGatewaySchema.config_schema).length" class="form-section">
              <h4>{{ $t('tenant_dashboard.paymentGateway.modal.configuration') }}</h4>
              <div
                v-for="(schema, field) in currentGatewaySchema.config_schema"
                :key="field"
                class="form-group"
              >
                <label :for="field">{{ schema.label }}</label>

                <select
                  v-if="schema.type === 'select'"
                  :id="field"
                  v-model="formData[field]"
                  class="form-control"
                  :class="{ 'is-invalid': errors[field] }"
                >
                  <option value="">{{ $t('tenant_dashboard.paymentGateway.modal.selectField', { field: schema.label }) }}</option>
                  <option
                    v-for="option in schema.options"
                    :key="option"
                    :value="option"
                  >
                    {{ option }}
                  </option>
                </select>

                <input
                  v-else
                  type="text"
                  :id="field"
                  v-model="formData[field]"
                  @input="errors[field] = null"
                  :placeholder="schema.placeholder || schema.label"
                  class="form-control"
                  :class="{ 'is-invalid': errors[field] }"
                >

                <div v-if="errors[field]" class="error-message">
                  {{ errors[field] }}
                </div>
              </div>
            </div>

            <div class="form-actions">
              <button
                type="button"
                @click="closeModal"
                class="btn btn-secondary"
                :disabled="loading || testing"
              >
                {{ $t('tenant_dashboard.paymentGateway.modal.cancel') }}
              </button>
              <button
                type="button"
                @click="testConnectionFromModal"
                class="btn btn-outline-info"
                :disabled="loading || testing"
              >
                <span v-if="testing" class="btn-loading">
                  <div class="btn-spinner"></div>
                  {{ $t('tenant_dashboard.paymentGateway.modal.testing') }}
                </span>
                <span v-else>{{ $t('tenant_dashboard.paymentGateway.modal.testConnection') }}</span>
              </button>
              <button
                type="submit"
                :disabled="loading || testing"
                class="btn btn-primary"
              >
                <span v-if="loading" class="btn-loading">
                  <div class="btn-spinner"></div>
                  {{ $t('tenant_dashboard.paymentGateway.modal.saving') }}
                </span>
                <span v-else>{{ $t('tenant_dashboard.paymentGateway.modal.saveConfiguration') }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'PaymentGateways',
  data() {
    return {
      gateways: [],
      showConfigModal: false,
      selectedGateway: null,
      currentGatewaySchema: {
        display_name: '',
        credentials_schema: {},
        config_schema: {},
      },
      formData: {},
      showPasswords: {},
      loading: false,
      testing: false,
      errors: {}
    }
  },
  async mounted() {
    await this.loadGateways()
  },
  methods: {
    async loadGateways() {
      try {
        console.log('🔄 Loading gateways...')
        const response = await axios.get('/tenant/payment-gateways')

        // Log full response for debugging
        console.log('📦 Full response:', response)
        console.log('📦 Response data:', response.data)
        console.log('📦 Response status:', response.status)

        // Initialize gateways as empty array first
        this.gateways = []

        // Handle different response structures
        if (response && response.data) {
          // Check if response has success property and data
          if (response.data.success !== undefined && response.data.data !== undefined) {
            const gatewaysData = response.data.data
            this.gateways = Array.isArray(gatewaysData) ? gatewaysData : []
          }
          // Check if response.data is directly an array
          else if (Array.isArray(response.data)) {
            this.gateways = response.data
          }
          // Check if response.data has a data property
          else if (response.data.data && Array.isArray(response.data.data)) {
            this.gateways = response.data.data
          }
          // Fallback: try to extract any array from response
          else {
            console.warn('⚠️ Unexpected response structure:', response.data)
            this.gateways = []
          }
        } else {
          console.warn('⚠️ Response or response.data is undefined')
          this.gateways = []
        }

        console.log('✅ Gateways loaded:', this.gateways.length, 'gateways')

        // Debug each gateway
        if (this.gateways && this.gateways.length > 0) {
          this.gateways.forEach(gateway => {
            console.log(`🔍 ${gateway.name}:`, {
              is_configured: gateway.is_configured,
              is_active: gateway.is_active,
              is_default: gateway.is_default
            })
          })
        } else {
          console.warn('⚠️ No gateways found in response')
        }
      } catch (error) {
        console.error('❌ Failed to load gateways:', error)
        console.error('❌ Error response:', error.response)
        console.error('❌ Error data:', error.response?.data)

        // Extract error message safely
        let errorMessage = this.$t('tenant_dashboard.paymentGateway.errors.failedToLoad')
        if (error.response) {
          if (error.response.data) {
            errorMessage = error.response.data.message ||
                          error.response.data.error ||
                          `HTTP ${error.response.status}: ${error.response.statusText}`
          } else {
            errorMessage = `HTTP ${error.response.status}: ${error.response.statusText}`
          }
        } else if (error.message) {
          errorMessage = error.message
        }

        // Show error notification
        this.$swal.fire({
          icon: 'error',
          title: this.$t('tenant_dashboard.paymentGateway.errors.error'),
          text: errorMessage,
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 4000
        })

        // Always set to empty array on error
        this.gateways = []
      }
    },

    async configureGateway(gatewayName) {
      console.log('🔧 Configuring gateway:', gatewayName)
      this.selectedGateway = gatewayName
      this.showConfigModal = true
      await this.loadGatewaySchema()
    },

    async loadGatewaySchema() {
      try {
        console.log('🔄 Loading gateway schema for:', this.selectedGateway)
        const response = await axios.get(`/tenant/payment-gateways/${this.selectedGateway}/schema`)
        this.currentGatewaySchema = response.data.data
        console.log('✅ Gateway schema loaded:', this.currentGatewaySchema)

        this.initializeFormData()
      } catch (error) {
        console.error('❌ Failed to load gateway schema:', error)
        this.$swal.fire({
          icon: 'error',
          title: this.$t('tenant_dashboard.paymentGateway.errors.error'),
          text: this.$t('tenant_dashboard.paymentGateway.errors.failedToLoadConfiguration'),
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 4000
        })
        this.closeModal()
      }
    },

    initializeFormData() {
      console.log('🔄 Initializing form data...')
      this.formData = {}
      this.errors = {}
      this.showPasswords = {}

      // Initialize ALL fields from schema
      Object.keys(this.currentGatewaySchema.credentials_schema).forEach(field => {
        this.formData[field] = ''
      })
      Object.keys(this.currentGatewaySchema.config_schema).forEach(field => {
        this.formData[field] = ''
      })

      // Set values from current config if exists
      if (this.currentGatewaySchema.current_config) {
        console.log('📁 Current config found:', this.currentGatewaySchema.current_config)
        const { credentials, config } = this.currentGatewaySchema.current_config

        // Set credential values (but don't pre-fill masked passwords)
        Object.keys(credentials).forEach(field => {
          const schema = this.currentGatewaySchema.credentials_schema[field]
          if (schema?.type === 'password' && credentials[field] === '••••••••') {
            this.formData[field] = '' // Leave empty for passwords
          } else {
            this.formData[field] = credentials[field] || ''
          }
        })

        // Set config values
        Object.keys(config).forEach(field => {
          this.formData[field] = config[field] || ''
        })
      }

      // Initialize password visibility state
      Object.keys(this.currentGatewaySchema.credentials_schema).forEach(field => {
        if (this.currentGatewaySchema.credentials_schema[field].type === 'password') {
          this.showPasswords[field] = false
        }
      })

      console.log('✅ Form data initialized:', this.formData)
    },

    togglePassword(field) {
      this.showPasswords[field] = !this.showPasswords[field]
    },

    async testConnectionFromModal() {
      this.testing = true
      this.errors = {}

      console.log('🔌 Testing connection from modal...')
      console.log('📤 Form data being tested:', this.formData)
      console.log('🎯 Gateway:', this.selectedGateway)

      // Validate required fields first
      const credentialsSchema = this.currentGatewaySchema.credentials_schema || {}
      const validationErrors = {}

      Object.keys(credentialsSchema).forEach(field => {
        const schema = credentialsSchema[field]
        if (schema.required && (!this.formData[field] || this.formData[field].trim() === '')) {
          validationErrors[field] = this.$t('tenant_dashboard.paymentGateway.validation.fieldRequired', { field: schema.label })
        }
      })

      if (Object.keys(validationErrors).length > 0) {
        this.errors = validationErrors
        this.testing = false

        this.$swal.fire({
          icon: 'error',
          title: this.$t('tenant_dashboard.paymentGateway.validation.validationError'),
          text: this.$t('tenant_dashboard.paymentGateway.validation.fillRequiredFields'),
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        })
        return
      }

      try {
        // Temporarily save credentials to test them
        const response = await axios.post(
          `/tenant/payment-gateways/${this.selectedGateway}/configure`,
          this.formData
        )

        // Now test the connection
        const testResponse = await axios.post(
          `/tenant/payment-gateways/${this.selectedGateway}/test`
        )

        if (testResponse.data.success) {
          this.$swal.fire({
            icon: 'success',
            title: this.$t('tenant_dashboard.paymentGateway.messages.connectionTestSuccessful'),
            text: testResponse.data.message || this.$t('tenant_dashboard.paymentGateway.messages.credentialsValid'),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000
          })

          // Clear any previous errors
          this.errors = {}
        } else {
          throw new Error(testResponse.data.message || 'Connection test failed')
        }

      } catch (error) {
        console.error('❌ Test connection failed:', error)

        // Parse error to show field-specific errors
        if (error.response?.status === 422) {
          // Validation errors from backend
          const backendErrors = error.response.data.errors || {}
          this.errors = {}

          Object.keys(backendErrors).forEach(field => {
            this.errors[field] = Array.isArray(backendErrors[field])
              ? backendErrors[field][0]
              : backendErrors[field]
          })

          this.$swal.fire({
            icon: 'error',
            title: this.$t('tenant_dashboard.paymentGateway.validation.validationError'),
            text: this.$t('tenant_dashboard.paymentGateway.validation.checkHighlightedFields'),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
          })
        } else {
          // Connection test errors - try to identify which field failed
          const errorMessage = error.response?.data?.message || error.message || this.$t('tenant_dashboard.paymentGateway.errors.connectionTestFailed')

          // Check if error mentions specific fields
          const lowerError = errorMessage.toLowerCase()

          // Map common Stripe errors to fields
          if (lowerError.includes('publishable') || lowerError.includes('pk_')) {
            const publishableKeyField = Object.keys(credentialsSchema).find(
              key => key.toLowerCase().includes('publishable') || key.toLowerCase().includes('public')
            )
            if (publishableKeyField) {
              this.errors[publishableKeyField] = errorMessage
            }
          } else if (lowerError.includes('secret') || lowerError.includes('sk_')) {
            const secretKeyField = Object.keys(credentialsSchema).find(
              key => key.toLowerCase().includes('secret') || key.toLowerCase().includes('private')
            )
            if (secretKeyField) {
              this.errors[secretKeyField] = errorMessage
            }
          } else {
            // General error - show on first required credential field
            const firstRequiredField = Object.keys(credentialsSchema).find(
              key => credentialsSchema[key].required
            )
            if (firstRequiredField) {
              this.errors[firstRequiredField] = errorMessage
            }
          }

          this.$swal.fire({
            icon: 'error',
            title: this.$t('tenant_dashboard.paymentGateway.errors.connectionTestFailedTitle'),
            text: errorMessage,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000
          })
        }
      } finally {
        this.testing = false
      }
    },

    async saveConfiguration() {
      this.loading = true
      this.errors = {}

      console.log('💾 Saving configuration...')
      console.log('📤 Form data being sent:', this.formData)
      console.log('🎯 Gateway:', this.selectedGateway)

      // Validate required fields first
      const credentialsSchema = this.currentGatewaySchema.credentials_schema || {}
      const validationErrors = {}

      Object.keys(credentialsSchema).forEach(field => {
        const schema = credentialsSchema[field]
        if (schema.required && (!this.formData[field] || this.formData[field].trim() === '')) {
          validationErrors[field] = this.$t('tenant_dashboard.paymentGateway.validation.fieldRequired', { field: schema.label })
        }
      })

      if (Object.keys(validationErrors).length > 0) {
        this.errors = validationErrors
        this.loading = false

        this.$swal.fire({
          icon: 'error',
          title: this.$t('tenant_dashboard.paymentGateway.validation.validationError'),
          text: this.$t('tenant_dashboard.paymentGateway.validation.fillRequiredFields'),
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        })
        return
      }

      try {
        // Save the configuration (backend sets is_active to false)
        const response = await axios.post(
          `/tenant/payment-gateways/${this.selectedGateway}/configure`,
          this.formData
        )

        console.log('✅ Save successful:', response.data)

        // Test the connection (backend will activate only if test passes)
        console.log('🔌 Testing connection...')
        const testResponse = await axios.post(
          `/tenant/payment-gateways/${this.selectedGateway}/test`
        )

        if (testResponse.data.success) {
          // Test passed - gateway is now activated by backend
          this.$swal.fire({
            icon: 'success',
            title: this.$t('tenant_dashboard.paymentGateway.messages.configurationSavedActivated'),
            text: this.$t('tenant_dashboard.paymentGateway.messages.gatewayConfiguredActive'),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000
          })

          // Close modal
          this.closeModal()

          // Reload gateways to get updated status
          await this.loadGateways()
        } else {
          throw new Error(testResponse.data.message || 'Connection test failed')
        }

      } catch (error) {
        console.error('❌ Save/Test failed:', error)
        console.log('🔍 Error response:', error.response)

        if (error.response?.status === 422) {
          this.errors = error.response.data.errors || {}
          console.log('🔍 Validation errors:', this.errors)

          // Show validation error notification
          this.$swal.fire({
            icon: 'error',
            title: this.$t('tenant_dashboard.paymentGateway.validation.validationError'),
            text: this.$t('tenant_dashboard.paymentGateway.validation.fixFormErrors'),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000
          })
        } else {
          const errorMessage = error.response?.data?.message || error.message || this.$t('tenant_dashboard.paymentGateway.errors.connectionTestFailedNotActivated')

          // Parse error to show field-specific errors
          const lowerError = errorMessage.toLowerCase()
          const credentialsSchema = this.currentGatewaySchema.credentials_schema || {}

          // Map common Stripe errors to fields
          if (lowerError.includes('publishable') || lowerError.includes('pk_')) {
            const publishableKeyField = Object.keys(credentialsSchema).find(
              key => key.toLowerCase().includes('publishable') || key.toLowerCase().includes('public')
            )
            if (publishableKeyField) {
              this.errors[publishableKeyField] = errorMessage
            }
          } else if (lowerError.includes('secret') || lowerError.includes('sk_')) {
            const secretKeyField = Object.keys(credentialsSchema).find(
              key => key.toLowerCase().includes('secret') || key.toLowerCase().includes('private')
            )
            if (secretKeyField) {
              this.errors[secretKeyField] = errorMessage
            }
          } else {
            // General error - show on first required credential field
            const firstRequiredField = Object.keys(credentialsSchema).find(
              key => credentialsSchema[key].required
            )
            if (firstRequiredField) {
              this.errors[firstRequiredField] = errorMessage
            }
          }

          // Show error notification
          this.$swal.fire({
            icon: 'error',
            title: this.$t('tenant_dashboard.paymentGateway.errors.connectionTestFailedTitle'),
            text: this.$t('tenant_dashboard.paymentGateway.errors.gatewaySavedNotActivated'),
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000
          })
        }
      } finally {
        this.loading = false
      }
    },

    closeModal() {
      console.log('🚪 Closing modal...')
      this.showConfigModal = false
      this.selectedGateway = null
      this.currentGatewaySchema = {
        display_name: '',
        credentials_schema: {},
        config_schema: {},
      }
      this.formData = {}
      this.errors = {}
      this.showPasswords = {}
      this.loading = false
    },

    async testConnection(gatewayName) {
      try {
        console.log('🔌 Testing connection for:', gatewayName)
        const response = await axios.post(`/tenant/payment-gateways/${gatewayName}/test`)

        this.$swal.fire({
          icon: 'success',
          title: this.$t('tenant_dashboard.paymentGateway.messages.success'),
          text: response.data.message || this.$t('tenant_dashboard.paymentGateway.messages.connectionTestSuccessful'),
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        })

        await this.loadGateways() // Refresh list
      } catch (error) {
        console.error('❌ Test connection failed:', error)

        this.$swal.fire({
          icon: 'error',
          title: this.$t('tenant_dashboard.paymentGateway.errors.testFailed'),
          text: error.response?.data?.message || this.$t('tenant_dashboard.paymentGateway.errors.connectionTestFailed'),
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 4000
        })
      }
    },

    async toggleActive(gatewayName) {
      try {
        const response = await axios.post(`/tenant/payment-gateways/${gatewayName}/toggle-active`)

        this.$swal.fire({
          icon: 'success',
          title: this.$t('tenant_dashboard.paymentGateway.messages.success'),
          text: response.data.message || this.$t('tenant_dashboard.paymentGateway.messages.gatewayStatusUpdated'),
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        })

        await this.loadGateways()
      } catch (error) {
        this.$swal.fire({
          icon: 'error',
          title: this.$t('tenant_dashboard.paymentGateway.errors.error'),
          text: error.response?.data?.message || this.$t('tenant_dashboard.paymentGateway.errors.failedToToggleGateway'),
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 4000
        })
      }
    },

    async setDefault(gatewayName) {
      try {
        const response = await axios.post(`/tenant/payment-gateways/${gatewayName}/set-default`)

        this.$swal.fire({
          icon: 'success',
          title: this.$t('tenant_dashboard.paymentGateway.messages.success'),
          text: response.data.message || this.$t('tenant_dashboard.paymentGateway.messages.defaultGatewayUpdated'),
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        })

        await this.loadGateways()
      } catch (error) {
        this.$swal.fire({
          icon: 'error',
          title: this.$t('tenant_dashboard.paymentGateway.errors.error'),
          text: error.response?.data?.message || this.$t('tenant_dashboard.paymentGateway.errors.failedToSetDefault'),
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 4000
        })
      }
    }
  }
}
</script>

<style scoped>
.payment-gateways {
  padding: 2rem;
}

.header {
  margin-bottom: 2rem;
}

.header h2 {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1a202c;
  margin: 0 0 0.5rem 0;
}

.header p {
  color: #718096;
  margin: 0;
  font-size: 1.125rem;
}

.status-info {
  background: #f8f9fa;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 1.25rem;
  margin-bottom: 2rem;
}

.status-info-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.status-info-header i {
  color: #3b82f6;
  font-size: 1.25rem;
}

.status-info-header h4 {
  margin: 0;
  font-size: 1.125rem;
  font-weight: 600;
  color: #1a202c;
}

.status-info-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1rem;
}

.status-item {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  padding: 0.75rem;
  background: white;
  border-radius: 6px;
  border: 1px solid #e5e7eb;
}

.status-item .status-badge-demo {
  flex-shrink: 0;
  margin-top: 0.125rem;
}

.status-item i {
  flex-shrink: 0;
  margin-top: 0.125rem;
  font-size: 1.125rem;
}

.status-item p {
  margin: 0;
  font-size: 0.875rem;
  color: #4b5563;
  line-height: 1.5;
}

.status-item p strong {
  color: #1a202c;
  font-weight: 600;
}

.gateways-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}

.gateway-card {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 1.5rem;
  background: white;
  transition: all 0.2s ease;
}

.gateway-card:hover {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.gateway-card.configured {
  border-left: 4px solid #10b981;
}

.gateway-card.active {
  border-top: 2px solid #3b82f6;
}

.gateway-card.default {
  border: 2px solid #f59e0b;
}

.gateway-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.gateway-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1a202c;
}

.status-badges {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.badge {
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 500;
  white-space: nowrap;
}

.badge-primary { background: #3b82f6; color: white; }
.badge-success { background: #10b981; color: white; }
.badge-secondary { background: #6b7280; color: white; }
.badge-warning { background: #f59e0b; color: white; }

.gateway-description {
  color: #4b5563;
  margin: 0 0 1rem 0;
  line-height: 1.5;
}

.gateway-features {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  margin: 1rem 0;
}

.feature-tag {
  background: #f3f4f6;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.75rem;
  color: #4b5563;
  border: 1px solid #e5e7eb;
}

.gateway-actions {
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #f3f4f6;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.btn {
  padding: 0.5rem 1rem;
  border: 1px solid transparent;
  border-radius: 4px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-primary {
  background: #3b82f6;
  color: white;
  border-color: #3b82f6;
}

.btn-primary:hover {
  background: #2563eb;
  border-color: #2563eb;
}

.btn-outline-primary {
  background: transparent;
  color: #3b82f6;
  border-color: #3b82f6;
}

.btn-outline-primary:hover {
  background: #3b82f6;
  color: white;
}

.btn-outline-secondary {
  background: transparent;
  color: #6b7280;
  border-color: #6b7280;
}

.btn-outline-secondary:hover {
  background: #6b7280;
  color: white;
}

.btn-outline-success {
  background: transparent;
  color: #10b981;
  border-color: #10b981;
}

.btn-outline-success:hover {
  background: #10b981;
  color: white;
}

.btn-outline-info {
  background: transparent;
  color: #06b6d4;
  border-color: #06b6d4;
}

.btn-outline-info:hover {
  background: #06b6d4;
  color: white;
}

.btn-outline-info:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-success {
  background: #10b981;
  color: white;
  border-color: #10b981;
}

.btn-success:hover {
  background: #059669;
  border-color: #059669;
}

.btn-warning {
  background: #f59e0b;
  color: white;
  border-color: #f59e0b;
}

.btn-warning:hover {
  background: #d97706;
  border-color: #d97706;
}

.btn-secondary {
  background: #6b7280;
  color: white;
  border-color: #6b7280;
}

.btn-secondary:hover {
  background: #4b5563;
  border-color: #4b5563;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

.modal-content {
  background: white;
  border-radius: 8px;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.modal-header h3 {
  margin: 0;
  color: #1a202c;
  font-size: 1.25rem;
  font-weight: 600;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6b7280;
  padding: 0;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
}

.close-btn:hover {
  background: #f3f4f6;
  color: #374151;
}

.modal-body {
  padding: 1.5rem;
}

.form-section {
  margin-bottom: 2rem;
}

.form-section h4 {
  margin: 0 0 1rem 0;
  color: #374151;
  font-size: 1.125rem;
  font-weight: 600;
}

.form-group {
  margin-bottom: 1.5rem;
  position: relative;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #374151;
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  font-size: 0.875rem;
  transition: all 0.2s ease;
}

.form-control:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control.is-invalid {
  border-color: #ef4444;
}

.password-toggle {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
}

.toggle-btn {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  font-size: 0.75rem;
  padding: 0.25rem 0.5rem;
  border-radius: 2px;
}

.toggle-btn:hover {
  background: #f3f4f6;
}

.error-message {
  color: #ef4444;
  font-size: 0.75rem;
  margin-top: 0.25rem;
}

.form-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
  flex-wrap: wrap;
}

.btn-loading {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-spinner {
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  width: 12px;
  height: 12px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 768px) {
  .gateways-grid {
    grid-template-columns: 1fr;
  }

  .gateway-header {
    flex-direction: column;
    gap: 0.5rem;
  }

  .action-buttons {
    flex-direction: column;
  }

  .btn {
    width: 100%;
  }

  .form-actions {
    flex-direction: column;
  }

  .modal-content {
    margin: 1rem;
    max-height: calc(100vh - 2rem);
  }

  .status-info-content {
    grid-template-columns: 1fr;
  }

  .status-item {
    flex-direction: column;
    gap: 0.5rem;
  }
}
</style>
