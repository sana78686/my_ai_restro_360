// ... existing code ...
<template>
  <div class="contact-banner-section">
    <div class="contact-banner-bg">
      <div class="contact-banner-overlay"></div>
      <div class="contact-banner-content text-center">
        <h1 class="mt-2 mb-2" style="font-family: 'Playfair Display', serif;">{{ $t('stock_check.title') }}</h1>
        <div class="mb-3">{{ $t('stock_check.description') }}</div>
      </div>
    </div>
    <div class="container contact-card-container">
      <div class="contact-card mx-auto">
        <div class="row g-0 align-items-stretch">

          <div class="col-md-12 p-4 d-flex flex-column justify-content-center">
            <section class="reservation-section">
              <div class="container">
                <div class="reservation-card mx-auto">
                  <div class="text-center reservation-title mb-4">{{ $t('stock_check.reservation_title') }}</div>
                  <form @submit.prevent="handleReservation" autocomplete="off">
                    <div class="row g-3 mb-3">
                      <div class="col-md-4">
                        <div class="input-group">
                          <input type="text" class="form-control" v-model="reservation.full_name" required
                            :placeholder="$t('stock_check.full_name')" id="reservationFullName" :disabled="reservationLoading"
                            data-bs-toggle="tooltip" :title="$t('stock_check.full_name_tip')">
                          <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="input-group">
                          <input type="text" class="form-control" v-model="reservation.phone_number" required
                            :placeholder="$t('stock_check.phone_number')" id="reservationPhone" :disabled="reservationLoading"
                            data-bs-toggle="tooltip" :title="$t('stock_check.phone_number_tip')">
                          <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="input-group">
                          <input type="email" class="form-control" v-model="reservation.email" required
                            :placeholder="$t('stock_check.email')" id="reservationEmail" :disabled="reservationLoading"
                            data-bs-toggle="tooltip" :title="$t('stock_check.email_tip')">
                          <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="input-group">
                          <select class="form-select" v-model="reservation.product_id" required :disabled="reservationLoading"
                            data-bs-toggle="tooltip" :title="$t('stock_check.select_product_tip')">
                            <option value="" disabled>{{ $t('stock_check.select_product') }}</option>
                            <option v-for="product in products" :key="product.id" :value="product.id">
                              {{ product.name }}
                            </option>
                          </select>
                          <span class="input-group-text"><i class="fas fa-box"></i></span>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="input-group">
                          <input type="number" min="1" class="form-control" v-model="reservation.qty" required
                            :placeholder="$t('stock_check.quantity')" id="reservationQty" :disabled="reservationLoading"
                            data-bs-toggle="tooltip" :title="$t('stock_check.quantity_tip')">
                          <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                        </div>
                      </div>
                    </div>
                    <div class="d-grid mb-2">
                      <button type="submit" class="btn btn-danger" :disabled="reservationLoading">
                        <span v-if="reservationLoading"><span class="spinner-border spinner-border-sm me-2"></span>{{ $t('stock_check.processing') }}</span>
                        <span v-else>{{ $t('stock_check.check_stock') }}</span>
                      </button>
                    </div>
                  </form>
                  <!-- <div class="reservation-callout text-center mt-3">
                    <span>You can also call: <span class="text-danger fw-bold">(+100) 123 456 7890</span> to make a
                      reservation</span>
                  </div> -->
                </div>
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Swal from 'sweetalert2'
import axios from 'axios'
import { Tooltip } from 'bootstrap'
// Remove vue-select imports

const reservation = ref({
  full_name: '',
  phone_number: '',
  email: '',
  product_id: '',
  qty: 1
});
const reservationLoading = ref(false);
const products = ref([]);

const fetchProducts = async () => {
  try {
    const response = await axios.get('/tenant/products', { params: { status: 'active', per_page: 1000 } });
    if (response.data.success) {
      products.value = response.data.data.data;
    } else {
      products.value = [];
    }
  } catch (error) {
    products.value = [];
  }
};

const handleReservation = async () => {
  reservationLoading.value = true;
  try {
    const response = await axios.post('/tenant/stock-check', {
      full_name: reservation.value.full_name,
      phone_number: reservation.value.phone_number,
      email: reservation.value.email,
      product_id: reservation.value.product_id,
      qty: reservation.value.qty
    });
    if (response.data.success) {
      await Swal.fire({
        icon: 'success',
        title: 'Stock Check Submitted!',
        text: response.data.message,
        confirmButtonColor: '#c62828'
      });
      reservation.value = { full_name: '', phone_number: '', email: '', product_id: '', qty: 1 };
    } else {
      throw new Error(response.data.message || 'Submission failed.');
    }
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: error.response?.data?.message || 'Something went wrong! Please try again.',
      confirmButtonColor: '#c62828'
    });
  } finally {
    reservationLoading.value = false;
  }
};

onMounted(() => {
  fetchProducts();
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new Tooltip(el)
  })
})
</script>

<style scoped>
.contact-banner-section {
  min-height: 100vh;
  background: #f8f9fa;
  position: relative;
}

.contact-banner-bg {
  background: url('/Mox_files/reservation.jpg') center center/cover no-repeat;
  min-height: 420px;
  position: relative;
  width: 100vw;
  max-width: 100%;
}

.contact-banner-overlay {
  position: absolute;
  inset: 0;
  background: #000000b3;
  z-index: 1;
}

.contact-banner-content {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 2;
  color: #fff;
  width: 100%;
}

.contact-card-container {
  position: relative;
  top: -120px;
  z-index: 3;
  margin-bottom: -80px;
}

.contact-card {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 6px 32px rgba(0, 0, 0, 0.18);
  padding: 0;
  max-width: 900px;
  margin: 0 auto;
  border: 2px solid #c62828;
}

.contact-form label {
  font-weight: 500;
  color: #c62828;
}

.contact-form .form-control:focus {
  border-color: #c62828;
  box-shadow: 0 0 0 0.15rem rgba(198, 40, 40, 0.12);
}

.btn-danger {
  background: #c62828;
  border: none;
  font-weight: bold;
  letter-spacing: 1px;
}

.btn-danger:hover {
  background: #b71c1c;
}

.footer-social a {
  color: #c62828;
  font-size: 1.3rem;
  background: rgba(255, 255, 255, 0.08);
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s, color 0.2s;
}

.footer-social a:hover {
  background: #c62828;
  color: #fff;
}

@media (max-width: 991.98px) {
  .contact-card {
    max-width: 98vw;
  }

  .contact-card-container {
    top: -80px;
    margin-bottom: -40px;
  }
}

@media (max-width: 767.98px) {
  .contact-banner-bg {
    min-height: 300px;
  }
  .contact-banner-content h1 {
    font-size: 2.2rem;
  }
  .contact-card {
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.10);
  }
  .contact-card-container {
    top: -40px;
    margin-bottom: 0;
  }
  .reservation-title {
    font-size: 1rem;
  }
  .reservation-section .row.g-3 > div {
    margin-bottom: 1rem;
  }
}

/* Android-specific fixes */
@media screen and (-webkit-min-device-pixel-ratio: 0) {
  .contact-banner-section,
  .contact-card {
    -webkit-transform: translateZ(0);
    transform: translateZ(0);
    -webkit-font-smoothing: antialiased;
  }
  
  input, textarea, select {
    -webkit-appearance: none;
    -webkit-border-radius: 4px;
    border-radius: 4px;
    font-size: 16px; /* Prevents zoom on Android */
  }
  
  button, .btn {
    -webkit-tap-highlight-color: rgba(198, 40, 40, 0.2);
    touch-action: manipulation;
  }
}

@media (max-width: 575.98px) {
  .contact-banner-bg {
    min-height: 250px;
  }
  .contact-banner-content h1 {
    font-size: 1.75rem;
  }
  .contact-banner-content {
    padding: 0 1rem;
  }
  .contact-card {
    border-radius: 8px;
    padding: 0 !important;
  }
  .contact-card-container {
    top: -30px;
    padding: 0 0.5rem;
  }
  .reservation-section {
    padding: 1rem 0.5rem;
  }
  .reservation-title {
    font-size: 0.95rem;
    margin-bottom: 1rem;
  }
  .btn-danger {
    padding: 10px 20px;
    font-size: 0.95rem;
  }
  .input-group-text {
    padding: 0.5rem;
  }
  .form-select {
    font-size: 0.9rem;
  }
}

.reservation-title {
  font-family: 'Playfair Display', serif;
  /* font-weight: bold; */
  color: #222;
  font-size: 1.08rem;
  margin-bottom: 0.5rem;
  border-bottom: 3px solid #c62828;
  /* display: inline-block; */
  padding-bottom: 0.2rem;
}
</style>
