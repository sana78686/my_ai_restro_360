<template>
  <div class="registration-form">
    <form @submit.prevent="handleSubmit">
      <div class="mb-3">
        <label for="restaurantName" class="form-label">Restaurant Name</label>
        <input
          type="text"
          class="form-control"
          id="restaurantName"
          v-model="form.restaurantName"
          ref="restaurantInput"
          required
        />
      </div>

      <div class="mb-3">
        <label for="domain" class="form-label">Subdomain</label>
        <div class="input-group">
          <input
            type="text"
            class="form-control"
            id="domain"
            v-model="form.domain"
            required
            readonly
          />
          <span class="input-group-text">.localhost:5000</span>
        </div>
      </div>

      <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <input
          type="text"
          class="form-control"
          id="address"
          v-model="form.address"
          required
          readonly
        />
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="street" class="form-label">Street</label>
          <input
            type="text"
            class="form-control"
            id="street"
            v-model="form.street"
            required
            readonly
          />
        </div>
        <div class="col-md-6 mb-3">
          <label for="postalCode" class="form-label">Postal Code</label>
          <input
            type="text"
            class="form-control"
            id="postalCode"
            v-model="form.postalCode"
            required
            readonly
          />
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="city" class="form-label">City</label>
          <input
            type="text"
            class="form-control"
            id="city"
            v-model="form.city"
            required
            readonly
          />
        </div>
        <div class="col-md-6 mb-3">
          <label for="state" class="form-label">State</label>
          <input
            type="text"
            class="form-control"
            id="state"
            v-model="form.state"
            required
            readonly
          />
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="country" class="form-label">Country</label>
          <input
            type="text"
            class="form-control"
            id="country"
            v-model="form.country"
            required
            readonly
          />
        </div>
        <div class="col-md-6 mb-3">
          <label for="county" class="form-label">County</label>
          <input
            type="text"
            class="form-control"
            id="county"
            v-model="form.county"
            required
            readonly
          />
        </div>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input
          type="email"
          class="form-control"
          id="email"
          v-model="form.email"
          required
        />
      </div>

      <div class="mb-3">
        <label for="phone" class="form-label">Phone</label>
        <input
          type="tel"
          class="form-control"
          id="phone"
          v-model="form.phone"
          required
        />
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input
          type="password"
          class="form-control"
          id="password"
          v-model="form.password"
          required
        />
      </div>

      <div class="mb-3">
        <label for="password_confirmation" class="form-label">Confirm Password</label>
        <input
          type="password"
          class="form-control"
          id="password_confirmation"
          v-model="form.password_confirmation"
          required
        />
      </div>

      <div class="mb-3 form-check">
        <input
          type="checkbox"
          class="form-check-input"
          id="terms"
          v-model="form.terms"
          required
        />
        <label class="form-check-label" for="terms">
          I agree to the terms and conditions
        </label>
      </div>

      <button type="submit" class="btn btn-primary" :disabled="loading">
        {{ loading ? 'Registering...' : 'Register' }}
      </button>
    </form>
  </div>
</template>

<script>
export default {
  name: 'TenantRegistrationForm',
  data() {
    return {
      loading: false,
      form: {
        restaurantName: '',
        domain: '',
        address: '',
        street: '',
        postalCode: '',
        city: '',
        state: '',
        country: '',
        county: '',
        email: '',
        phone: '',
        password: '',
        password_confirmation: '',
        terms: false
      },
      autocomplete: null
    }
  },
  mounted() {
    this.initGooglePlaces();
  },
  methods: {
    initGooglePlaces() {
      const input = this.$refs.restaurantInput;
      this.autocomplete = new google.maps.places.Autocomplete(input, {
        types: ['establishment'],
        componentRestrictions: { country: ['us', 'uk', 'ca', 'au'] }
      });

      this.autocomplete.addListener('place_changed', () => {
        const place = this.autocomplete.getPlace();
        if (!place.geometry) {
          console.log("No details available for input: '" + place.name + "'");
          return;
        }

        // Set restaurant name
        this.form.restaurantName = place.name;

        // Generate subdomain
        this.generateSubdomain(place.name);

        // Parse address components
        this.parseAddressComponents(place.address_components);

        // Set formatted address
        this.form.address = place.formatted_address;
      });
    },

    generateSubdomain(name) {
      // Convert to lowercase and replace spaces with hyphens
      let subdomain = name.toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

      // Check if subdomain exists
      this.checkSubdomainExists(subdomain).then(exists => {
        if (exists) {
          // Add random number if subdomain exists
          const randomNum = Math.floor(Math.random() * 1000);
          this.form.domain = `${subdomain}-${randomNum}`;
        } else {
          this.form.domain = subdomain;
        }
      });
    },

    async checkSubdomainExists(subdomain) {
      try {
        const response = await axios.get(`/api/check-subdomain/${subdomain}`);
        return response.data.exists;
      } catch (error) {
        console.error('Error checking subdomain:', error);
        return false;
      }
    },

    parseAddressComponents(components) {
      const address = {
        street: '',
        postalCode: '',
        city: '',
        state: '',
        country: '',
        county: ''
      };

      components.forEach(component => {
        const type = component.types[0];
        switch (type) {
          case 'street_number':
          case 'route':
            address.street += component.long_name + ' ';
            break;
          case 'postal_code':
            address.postalCode = component.long_name;
            break;
          case 'locality':
            address.city = component.long_name;
            break;
          case 'administrative_area_level_1':
            address.state = component.long_name;
            break;
          case 'country':
            address.country = component.long_name;
            break;
          case 'administrative_area_level_2':
            address.county = component.long_name;
            break;
        }
      });

      // Update form with parsed address
      Object.assign(this.form, address);
    },

    async handleSubmit() {
      this.loading = true;
      try {
        const response = await axios.post('/api/register', this.form);
        if (response.data.success) {
          this.$swal({
            icon: 'success',
            title: 'Registration Successful',
            text: 'You have been registered successfully!'
          }).then(() => {
            window.location.href = response.data.redirect_url;
          });
        }
      } catch (error) {
        this.$swal({
          icon: 'error',
          title: 'Registration Failed',
          text: error.response?.data?.message || 'An error occurred during registration'
        });
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.registration-form {
  max-width: 800px;
  margin: 0 auto;
  padding: 2rem;
}

.input-group-text {
  background-color: #f8f9fa;
  color: #6c757d;
}
</style> 