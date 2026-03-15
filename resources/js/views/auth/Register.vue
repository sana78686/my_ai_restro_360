<template>
  <div class="register-page">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
              <h3 class="mb-0">Restaurant Registration</h3>
            </div>
            <div class="card-body">
              <form @submit.prevent="handleSubmit" class="needs-validation" enctype="multipart/form-data">
                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label for="restaurantName" class="form-label">Restaurant Name*</label>
                    <input
                      type="text"
                      class="form-control"
                      id="restaurantName"
                      ref="googlePlacesInput"
                      v-model="form.restaurant_name"
                      required
                    >
                  </div>

                  <div class="col-md-12 mb-3">
                    <label for="email" class="form-label">Email Address*</label>
                    <input
                      type="email"
                      class="form-control"
                      id="email"
                      v-model="form.email"
                      required
                    >
                  </div>

                  <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password*</label>
                    <input
                      type="password"
                      class="form-control"
                      id="password"
                      v-model="form.password"
                      required
                    >
                  </div>

                  <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password*</label>
                    <input
                      type="password"
                      class="form-control"
                      id="password_confirmation"
                      v-model="form.password_confirmation"
                      required
                    >
                  </div>

                  <div class="col-md-12 mb-3">
                    <label for="logo" class="form-label">Restaurant Logo</label>
                    <input
                      type="file"
                      class="form-control"
                      id="logo"
                      @change="handleLogoUpload"
                      accept="image/*"
                    >
                  </div>

                  <div class="col-md-12 mb-3">
                    <label class="form-label">Address Details</label>
                    <div class="card bg-light">
                      <div class="card-body">
                        <p class="mb-1"><strong>Street:</strong> {{ form.street }}</p>
                        <p class="mb-1"><strong>City:</strong> {{ form.city }}</p>
                        <p class="mb-1"><strong>State:</strong> {{ form.state }}</p>
                        <p class="mb-1"><strong>Country:</strong> {{ form.country }}</p>
                        <p class="mb-0"><strong>Postal Code:</strong> {{ form.postal_code }}</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="d-grid">
                  <button type="submit" class="btn btn-primary" :disabled="isLoading">
                    {{ isLoading ? 'Registering...' : 'Register Restaurant' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
  name: 'Register',
  setup() {
    const router = useRouter()
    const googlePlacesInput = ref(null)
    const isLoading = ref(false)
    const form = ref({
      restaurant_name: '',
      email: '',
      password: '',
      password_confirmation: '',
      logo: null,
      street: '',
      city: '',
      state: '',
      country: '',
      postal_code: '',
      place_id: ''
    })

    onMounted(() => {
      // Initialize Google Places Autocomplete
      if (window.google && window.google.maps) {
        const autocomplete = new google.maps.places.Autocomplete(googlePlacesInput.value, {
          types: ['establishment'],
          componentRestrictions: { country: ['US'] } // Restrict to US restaurants, remove or modify as needed
        })

        autocomplete.addListener('place_changed', () => {
          const place = autocomplete.getPlace()
          form.value.restaurant_name = place.name
          form.value.place_id = place.place_id

          // Extract address components
          place.address_components.forEach(component => {
            const types = component.types

            if (types.includes('street_number') || types.includes('route')) {
              form.value.street = form.value.street 
                ? `${form.value.street} ${component.long_name}`
                : component.long_name
            }
            if (types.includes('locality')) form.value.city = component.long_name
            if (types.includes('administrative_area_level_1')) form.value.state = component.long_name
            if (types.includes('country')) form.value.country = component.long_name
            if (types.includes('postal_code')) form.value.postal_code = component.long_name
          })
        })
      }
    })

    const handleLogoUpload = (event) => {
      const file = event.target.files[0]
      if (file) {
        form.value.logo = file
      }
    }

    const handleSubmit = async () => {
      try {
        isLoading.value = true
        const formData = new FormData()
        
        // Append all form fields to FormData
        Object.keys(form.value).forEach(key => {
          formData.append(key, form.value[key])
        })

        const response = await axios.post('/api/register', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        if (response.data.success) {
          // Store token and role
          localStorage.setItem('token', response.data.token)
          localStorage.setItem('userRole', 'restaurant_owner')

          // Show success message
          await Swal.fire({
            icon: 'success',
            title: 'Registration Successful!',
            text: 'Your restaurant has been registered successfully.',
            confirmButtonColor: '#4CAF50'
          })

          // Redirect to dashboard
          const subdomain = form.value.restaurant_name.toLowerCase().replace(/[^a-z0-9]/g, '')
          window.location.href = `http://${subdomain}.localhost:5000/dashboard`
        }
      } catch (error) {
        console.error('Registration error:', error)
        Swal.fire({
          icon: 'error',
          title: 'Registration Failed',
          text: error.response?.data?.message || 'An error occurred during registration.',
          confirmButtonColor: '#dc3545'
        })
      } finally {
        isLoading.value = false
      }
    }

    return {
      form,
      isLoading,
      googlePlacesInput,
      handleLogoUpload,
      handleSubmit
    }
  }
}
</script>

<style scoped>
.register-page {
  padding: 60px 0;
  min-height: 100vh;
  background-color: var(--light-bg);
}

.card {
  border: none;
  border-radius: 15px;
  overflow: hidden;
}

.card-header {
  border-bottom: none;
  padding: 20px;
}

.card-body {
  padding: 30px;
}

.form-label {
  font-weight: 600;
  color: var(--secondary-color);
}

.btn-primary {
  padding: 12px;
  font-weight: 600;
}
</style> 