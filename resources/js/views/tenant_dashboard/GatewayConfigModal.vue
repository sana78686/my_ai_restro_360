<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Configure {{ gatewaySchema.display_name }}</h3>
        <button @click="$emit('close')" class="close-btn">&times;</button>
      </div>

      <div class="modal-body">
        <form @submit.prevent="saveConfiguration">
          <!-- Credentials Fields -->
          <div class="form-section">
            <h4>Credentials</h4>
            <div 
              v-for="(schema, field) in gatewaySchema.credentials_schema" 
              :key="field"
              class="form-group"
            >
              <label :for="field">{{ schema.label }}</label>
              
              <input
                v-if="schema.type === 'password'"
                :type="showPasswords[field] ? 'text' : 'password'"
                :id="field"
                v-model="formData[field]"
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
                :placeholder="schema.placeholder || schema.label"
                :required="schema.required"
                class="form-control"
                :class="{ 'is-invalid': errors[field] }"
              >
              
              <select
                v-else-if="schema.type === 'select'"
                :id="field"
                v-model="formData[field]"
                class="form-control"
                :class="{ 'is-invalid': errors[field] }"
              >
                <option value="">Select {{ schema.label }}</option>
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
                  {{ showPasswords[field] ? 'Hide' : 'Show' }}
                </button>
              </div>

              <div v-if="errors[field]" class="error-message">
                {{ errors[field] }}
              </div>
            </div>
          </div>

          <!-- Configuration Fields -->
          <div v-if="Object.keys(gatewaySchema.config_schema).length" class="form-section">
            <h4>Configuration</h4>
            <div 
              v-for="(schema, field) in gatewaySchema.config_schema" 
              :key="field"
              class="form-group"
            >
              <label :for="field">{{ schema.label }}</label>
              
              <select
                v-if="schema.type === 'select'"
                :id="field"
                v-model="formData[field]"
                class="form-control"
              >
                <option value="">Select {{ schema.label }}</option>
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
                :placeholder="schema.placeholder || schema.label"
                class="form-control"
              >
            </div>
          </div>

          <div class="form-actions">
            <button 
              type="button" 
              @click="closeModal"
              class="btn btn-secondary"
              :disabled="loading"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              :disabled="loading"
              class="btn btn-primary"
            >
              <span v-if="loading" class="btn-loading">
                <div class="btn-spinner"></div>
                Saving...
              </span>
              <span v-else>Save Configuration</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'GatewayConfigModal',
  props: {
    gatewayName: String
  },
  data() {
    return {
      gatewaySchema: {
        display_name: '',
        credentials_schema: {},
        config_schema: {},
      },
      formData: {},
      showPasswords: {},
      loading: false,
      errors: {}
    }
  },
  async mounted() {
    await this.loadGatewaySchema()
  },
  methods: {
    async loadGatewaySchema() {
      try {
        console.log('🔄 Loading gateway schema for:', this.gatewayName)
        const response = await axios.get(`/tenant/payment-gateways/${this.gatewayName}/schema`)
        this.gatewaySchema = response.data.data

        this.initializeFormData()
      } catch (error) {
        console.error('❌ Failed to load gateway schema:', error)
        this.$toast.error('Failed to load gateway configuration')
        this.closeModal()
      }
    },

    initializeFormData() {
      this.formData = {}
      this.errors = {}

      Object.keys(this.gatewaySchema.credentials_schema).forEach(field => {
        this.formData[field] = ''
      })
      Object.keys(this.gatewaySchema.config_schema).forEach(field => {
        this.formData[field] = ''
      })

      if (this.gatewaySchema.current_config) {
        const { credentials, config } = this.gatewaySchema.current_config

        Object.keys(credentials).forEach(field => {
          const schema = this.gatewaySchema.credentials_schema[field]
          if (schema?.type === 'password' && credentials[field] === '••••••••') {
            this.formData[field] = ''
          } else {
            this.formData[field] = credentials[field] || ''
          }
        })

        Object.keys(config).forEach(field => {
          this.formData[field] = config[field] || ''
        })
      }

      Object.keys(this.gatewaySchema.credentials_schema).forEach(field => {
        if (this.gatewaySchema.credentials_schema[field].type === 'password') {
          this.showPasswords[field] = false
        }
      })
    },

    togglePassword(field) {
      this.showPasswords[field] = !this.showPasswords[field]
    },

    async saveConfiguration() {
      this.loading = true
      this.errors = {}

      try {
        const response = await axios.post(
          `/tenant/payment-gateways/${this.gatewayName}/configure`,
          this.formData
        )

        console.log('✅ Save successful:', response.data)
        this.$toast.success(response.data.message)

        // Notify parent that save succeeded
        this.$emit('saved', response.data)

        // Close the modal after a short delay
        setTimeout(() => {
          this.closeModal()
        }, 1000)
      } catch (error) {
        console.error('❌ Save failed:', error)
        if (error.response?.status === 422) {
          this.errors = error.response.data.errors || {}
          this.$toast.error('Please fix the form errors')
        } else {
          const errorMessage = error.response?.data?.message || 'Failed to save configuration'
          this.$toast.error(errorMessage)
        }
      } finally {
        this.loading = false
      }
    },

    closeModal() {
      console.log('🚪 Closing modal...')
      this.$emit('close')
    }
  }
}
</script>


<style scoped>
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
}

.modal-content {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e2e8f0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6b7280;
}

.close-btn:hover {
  color: #374151;
}

.modal-body {
  padding: 1.5rem;
}

.form-section {
  margin-bottom: 2rem;
}

.form-section h4 {
  margin-bottom: 1rem;
  color: #374151;
}

.form-group {
  margin-bottom: 1rem;
  position: relative;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-control {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  font-size: 0.875rem;
  transition: border-color 0.2s ease;
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
}

.toggle-btn:hover {
  color: #374151;
}

.error-message {
  color: #ef4444;
  font-size: 0.75rem;
  margin-top: 0.25rem;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: 1px solid transparent;
  border-radius: 4px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 120px;
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
  border-color: #2563eb;
}

.btn-secondary {
  background: #6b7280;
  color: white;
  border-color: #6b7280;
}

.btn-secondary:hover:not(:disabled) {
  background: #4b5563;
  border-color: #4b5563;
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
</style>