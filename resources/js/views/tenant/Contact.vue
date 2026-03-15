// ... existing code ...
<template>
  <div class="contact-banner-section">
    <div class="contact-banner-bg">
      <div class="contact-banner-overlay"></div>
      <div class="contact-banner-content text-center">
        <h1 class="mt-2 mb-2" style="font-family: 'Playfair Display', serif; ">{{ $t('contact') }}</h1>
        <div class=" mb-3">{{ $t('contactSubtitle') || 'GOT A QUESTION? WE\'LL GIVE YOU STRAIGHT ANSWER' }}</div>
      </div>
    </div>
    <div class="container contact-card-container">
      <div class="contact-card mx-auto">
        <div class="row g-0 align-items-stretch">

          <div class="col-md- p-4 d-flex flex-column justify-content-center">
            <h3 class="text-center mb-3" style="color: #c62828; font-family: 'Playfair Display', serif;">{{ $t('getInTouch') || 'Get In Touch' }}</h3>
            <form @submit.prevent="handleSubmit" class="contact-form">
              <div class="mb-3">
                <label for="name" class="form-label">{{ $t('name') }}
                  <span class="ms-1" data-bs-toggle="tooltip" :title="$t('nameTip') || 'Enter your full name.'"><i class="fas fa-info-circle text-secondary"></i></span>
                </label>
                <input type="text" class="form-control" id="name" v-model="form.name" required :placeholder="$t('yourName') || 'Your Name'">
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">{{ $t('auth.login.email') }}
                  <span class="ms-1" data-bs-toggle="tooltip" :title="$t('emailTip') || 'Enter a valid email address.'"><i class="fas fa-info-circle text-secondary"></i></span>
                </label>
                <input type="email" class="form-control" id="email" v-model="form.email" required :placeholder="$t('yourEmail') || 'you@email.com'">
              </div>
              <div class="mb-3">
                <label for="phone" class="form-label">{{ $t('tenants.list.phone') || 'Phone' }}
                  <span class="ms-1" data-bs-toggle="tooltip" :title="$t('phoneTip') || 'Enter your phone number.'"><i class="fas fa-info-circle text-secondary"></i></span>
                </label>
                <input type="text" class="form-control" id="phone" v-model="form.phone" required :placeholder="$t('yourPhoneNumber') || 'Your Phone Number'">
              </div>
              <div class="mb-3">
                <label for="subject" class="form-label">{{ $t('subject') || 'Subject' }}
                  <span class="ms-1" data-bs-toggle="tooltip" :title="$t('subjectTip') || 'What is your message about?'"><i class="fas fa-info-circle text-secondary"></i></span>
                </label>
                <input type="text" class="form-control" id="subject" v-model="form.subject" required :placeholder="$t('subject') || 'Subject'">
              </div>
              <div class="mb-3">
                <label for="message" class="form-label">{{ $t('message') || 'Message' }}
                  <span class="ms-1" data-bs-toggle="tooltip" :title="$t('messageTip') || 'Type your message here.'"><i class="fas fa-info-circle text-secondary"></i></span>
                </label>
                <textarea class="form-control" id="message" rows="4" v-model="form.message" required :placeholder="$t('yourMessage') || 'Your message...'"></textarea>
              </div>
              <!-- <vue-recaptcha-v3 :siteKey="recaptchaSiteKey" @verify="onVerify" /> -->
              <button type="submit" class="btn btn-danger w-100 mt-2">{{ $t('common.submit') }}</button>
            </form>
          </div>
          <!-- <div class="col-md-6 d-flex align-items-center justify-content-center p-4">
            <img src="/Mox_files/contact-restaurant.jpg" alt="Contact Restaurant" class="img-fluid rounded shadow" style="object-fit: cover; min-height: 320px; max-height: 340px; width: 100%;">
          </div> -->
          <!-- <div class="col-12 px-4 pt-4 pb-2">
            <div class="row text-center mb-4">
              <div class="col-md-4 mb-3 mb-md-0">
                <i class="fas fa-home fa-2x text-danger mb-2"></i>
                <div class="fw-bold">ADDRESS :</div>
                <div style="font-size: 0.98rem;">2903 Avenue Z, Brooklyn, New York City</div>
              </div>
              <div class="col-md-4 mb-3 mb-md-0">
                <i class="fas fa-phone fa-2x text-danger mb-2"></i>
                <div class="fw-bold">PHONES :</div>
                <div style="font-size: 0.98rem;">(212) 457-2308<br/>(212) 457-2309</div>
              </div>
              <div class="col-md-4">
                <i class="fas fa-envelope fa-2x text-danger mb-2"></i>
                <div class="fw-bold">E-MAIL :</div>
                <div style="font-size: 0.98rem;">contact@mox.com<br/>info@mox.com</div>
              </div>
            </div>
          </div> -->
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import Swal from 'sweetalert2'
import axios from 'axios'
import { Tooltip } from 'bootstrap'
// import { useReCaptcha, VueReCaptcha } from 'vue-recaptcha-v3'

// const recaptchaSiteKey = 'YOUR_RECAPTCHA_SITE_KEY' // <-- Replace with your real site key
// const { executeRecaptcha } = useReCaptcha()

const form = reactive({
  name: '',
  email: '',
  phone: '',
  subject: '',
  message: ''
})
// let recaptchaToken = ''

const onVerify = (token) => {
  // recaptchaToken = token
}

const handleSubmit = async () => {
  try {
    const response = await axios.post('/tenant/contact-info', { ...form })
    if (response.data.success) {
      await Swal.fire({
        icon: 'success',
        title: 'Message Sent!',
        text: response.data.message,
        confirmButtonColor: '#c62828'
      })
      form.name = ''
      form.email = ''
      form.phone = ''
      form.subject = ''
      form.message = ''
    } else {
      throw new Error(response.data.message || 'Submission failed.')
    }
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: error.response?.data?.message || 'Something went wrong! Please try again.',
      confirmButtonColor: '#c62828'
    })
  }
}

onMounted(() => {
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new Tooltip(el)
  })
})
</script>

<style scoped>
.contact-banner-section {
  /* min-height: 100vh; */
  background: #f8f9fa;
  position: relative;
}
.contact-banner-bg {
  background: url('/Mox_files/contact-banner.jpg') center center/cover no-repeat;
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
  box-shadow: 0 6px 32px rgba(0,0,0,0.18);
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
  box-shadow: 0 0 0 0.15rem rgba(198,40,40,0.12);
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
  background: rgba(255,255,255,0.08);
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
    box-shadow: 0 2px 8px rgba(0,0,0,0.10);
  }
  .contact-card-container {
    top: -40px;
    margin-bottom: 0;
  }
  .contact-form {
    padding: 0.5rem;
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

  input, textarea {
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
  .contact-form {
    padding: 1rem 0.75rem;
  }
  .btn-danger {
    padding: 10px 20px;
    font-size: 0.95rem;
  }
}
</style>
