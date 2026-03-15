<template>
  <div class="settings-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">{{ $t('tenant_dashboard.email_settings.title') }}</h4>
    </div>

    <div class="card">
      <div class="">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">{{ $t('dashboard_common.loading') }}</span>
          </div>
        </div>
        <form v-else @submit.prevent="saveSettings" class="settings-form">
          <!-- Basic Information -->
          <div class="section mb-4">
            <h5 class="section-title">{{ $t('tenant_dashboard.email_settings.businessEmailSettings') }}</h5>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">{{ $t('tenant_dashboard.email_settings.mailDriver') }}
                    <span class="ms-1" data-bs-toggle="tooltip" :title="$t('tenant_dashboard.email_settings.mailDriverTooltip')">
                      <i class="fas fa-info-circle text-secondary"></i>
                    </span>
                  </label>
                  <input v-model="form.driver" type="text" class="form-control" :placeholder="$t('tenant_dashboard.email_settings.mailDriverPlaceholder')">
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ $t('tenant_dashboard.email_settings.mailHost') }}
                    <span class="ms-1" data-bs-toggle="tooltip" :title="$t('tenant_dashboard.email_settings.mailHostTooltip')">
                      <i class="fas fa-info-circle text-secondary"></i>
                    </span>
                  </label>
                  <input v-model="form.host" type="text" class="form-control" :placeholder="$t('tenant_dashboard.email_settings.mailHostPlaceholder')">
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ $t('tenant_dashboard.email_settings.mailPort') }}
                    <span class="ms-1" data-bs-toggle="tooltip" :title="$t('tenant_dashboard.email_settings.mailPortTooltip')">
                      <i class="fas fa-info-circle text-secondary"></i>
                    </span>
                  </label>
                  <input v-model="form.port" type="text" class="form-control" :placeholder="$t('tenant_dashboard.email_settings.mailPortPlaceholder')">
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ $t('tenant_dashboard.email_settings.mailUsername') }}
                    <span class="ms-1" data-bs-toggle="tooltip" :title="$t('tenant_dashboard.email_settings.mailUsernameTooltip')">
                      <i class="fas fa-info-circle text-secondary"></i>
                    </span>
                  </label>
                  <input v-model="form.username" type="text" class="form-control" :placeholder="$t('tenant_dashboard.email_settings.mailUsernamePlaceholder')">
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ $t('tenant_dashboard.email_settings.mailPassword') }}
                    <span class="ms-1" data-bs-toggle="tooltip" :title="$t('tenant_dashboard.email_settings.mailPasswordTooltip')">
                      <i class="fas fa-info-circle text-secondary"></i>
                    </span>
                  </label>
                  <input v-model="form.password" type="text" class="form-control" :placeholder="$t('tenant_dashboard.email_settings.mailPasswordPlaceholder')">
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ $t('tenant_dashboard.email_settings.mailEncryption') }}
                    <span class="ms-1" data-bs-toggle="tooltip" :title="$t('tenant_dashboard.email_settings.mailEncryptionTooltip')">
                      <i class="fas fa-info-circle text-secondary"></i>
                    </span>
                  </label>
                  <input v-model="form.encryption" type="text" class="form-control" :placeholder="$t('tenant_dashboard.email_settings.mailEncryptionPlaceholder')">
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ $t('tenant_dashboard.email_settings.mailFromName') }}
                    <span class="ms-1" data-bs-toggle="tooltip" :title="$t('tenant_dashboard.email_settings.mailFromNameTooltip')">
                      <i class="fas fa-info-circle text-secondary"></i>
                    </span>
                  </label>
                  <input v-model="form.from_name" type="text" class="form-control" :placeholder="$t('tenant_dashboard.email_settings.mailFromNamePlaceholder')">
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ $t('tenant_dashboard.email_settings.businessEmail') }}
                    <span class="ms-1" data-bs-toggle="tooltip" :title="$t('tenant_dashboard.email_settings.businessEmailTooltip')">
                      <i class="fas fa-info-circle text-secondary"></i>
                    </span>
                  </label>
                  <input v-model="form.email" type="email" class="form-control" :placeholder="$t('tenant_dashboard.email_settings.businessEmailPlaceholder')">
                </div>
              </div>
          </div>


          <div class="text-end">
            <button type="submit" class="btn btn-primary" :disabled="saving">
              <span v-if="saving" class="spinner-border spinner-border-sm me-2" role="status"></span>
              {{ $t('tenant_dashboard.email_settings.saveSettings') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { Tooltip } from 'bootstrap'

export default {
  name: 'Settings',
  setup() {
    const loading = ref(true)
    const saving = ref(false)
    let tooltips = []
    const form = ref({
        driver: '',
        host: '',
        port: '',
        username: '',
        password: '',
        encryption: '',
        email: '',
        from_name: ''
    })


    // Initialize tooltips
    const initTooltips = () => {
      // Dispose existing tooltips
      tooltips.forEach(tooltip => tooltip.dispose())
      tooltips = []

      // Initialize new tooltips
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        tooltips.push(new Tooltip(el))
      })
    }
    const fetchSettings = async () => {
        try {
          const response = await axios.get('/tenant/email-setting')
          if (response.data.success && response.data.data) {
            const settings = response.data.data
            form.value = {
              driver: settings.driver || '',
              port: settings.port || '',
              host: settings.host || '',
              username: settings.username || '',
              password: settings.password || '',
              email: settings.email || '',
              encryption: settings.encryption || '',
              from_name: settings.from_name || ''
            }
          }
        } catch (error) {
          console.error('Error fetching Email Setting:', error)
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to fetch Email Setting'
          })
        } finally {
          loading.value = false
        }
      }

      const saveSettings = async () => {
        saving.value = true
        try {
          const response = await axios.put('/tenant/email-setting', form.value)
          if (response.data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: 'Email Setup updated successfully',
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000
            })
          } else {
            throw new Error(response.data.message || 'Failed to save Email Setting')
          }
        } catch (error) {
          console.error('Error saving Email Setting:', error)
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response?.data?.message || 'Failed to save Email Setting'
          })
        } finally {
          saving.value = false
        }
      }

    onMounted(() => {
      fetchSettings()
      initTooltips()
      // document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
      //   new Tooltip(el)
      // })
    })

    return {
      form,
      loading,
      saving,
      saveSettings
    }
  }
}
</script>

<style scoped>
.settings-page {
  padding: 20px;
  font-family: 'Inter', 'Roboto', 'Segoe UI', Arial, sans-serif;
}

.section {
  background: #fff;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.section-title {
  color: #2c3e50;
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 1.5rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #f0f0f0;
}

.form-label {
  font-weight: 500;
  color: #4a5568;
  margin-bottom: 0.5rem;
}

.form-control, .form-select {
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 0.5rem 0.75rem;
  font-size: 0.95rem;
}

.form-control:focus, .form-select:focus {
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.btn-primary {
  background: #4299e1;
  border: none;
  padding: 0.6rem 1.5rem;
  font-weight: 500;
  border-radius: 6px;
  transition: all 0.2s;
}

.btn-primary:hover {
  background: #3182ce;
  transform: translateY(-1px);
}

.btn-primary:disabled {
  background: #a0aec0;
  cursor: not-allowed;
  transform: none;
}

.form-check-input:checked {
  background-color: #4299e1;
  border-color: #4299e1;
}

.logo-preview {
  width: 100px;
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8fafc;
  border-radius: 6px;
  overflow: hidden;
}

.logo-preview img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}
</style>
