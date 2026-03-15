<template>
  <div class="contact-page">
    <div class="container">
      <div class="row">
        <div class="col-lg-6">
          <h1 class="mb-4">Contact Us</h1>
          <p class="mb-5">Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>

          <form @submit.prevent="handleSubmit" class="contact-form">
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input
                type="text"
                class="form-control"
                id="name"
                v-model="form.name"
                required
              >
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                type="email"
                class="form-control"
                id="email"
                v-model="form.email"
                required
              >
            </div>

            <div class="mb-3">
              <label for="subject" class="form-label">Subject</label>
              <input
                type="text"
                class="form-control"
                id="subject"
                v-model="form.subject"
                required
              >
            </div>

            <div class="mb-4">
              <label for="message" class="form-label">Message</label>
              <textarea
                class="form-control"
                id="message"
                rows="5"
                v-model="form.message"
                required
              ></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Send Message</button>
          </form>
        </div>

        <div class="col-lg-6">
          <div class="contact-info mt-5 mt-lg-0">
            <div class="info-item">
              <i class="fas fa-map-marker-alt"></i>
              <div>
                <h4>Location</h4>
                <p>{{ contactAddress }}</p>
              </div>
            </div>

            <div class="info-item">
              <i class="fas fa-phone"></i>
              <div>
                <h4>Phone</h4>
                <p>{{ contactPhone }}</p>
              </div>
            </div>

            <div class="info-item">
              <i class="fas fa-envelope"></i>
              <div>
                <h4>Email</h4>
                <p>{{ contactEmail }}</p>
              </div>
            </div>

            <div class="info-item">
              <i class="fas fa-clock"></i>
              <div>
                <h4>Working Hours</h4>
                <p>{{ contactHours }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import Swal from 'sweetalert2'
import { contactEmail, contactPhone, contactAddress, contactHours } from '../../utils/contact.js'

const form = reactive({
  name: '',
  email: '',
  subject: '',
  message: ''
})

const handleSubmit = async () => {
  try {
    // Here you would typically make an API call to send the message
    // For now, we'll just show a success message
    await Swal.fire({
      icon: 'success',
      title: 'Message Sent!',
      text: 'Thank you for contacting us. We will get back to you soon.',
      confirmButtonColor: '#d4af37'
    })

    // Reset form
    form.name = ''
    form.email = ''
    form.subject = ''
    form.message = ''
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'Something went wrong! Please try again.',
      confirmButtonColor: '#d4af37'
    })
  }
}
</script>

<style scoped>
.contact-page {
  padding: 4rem 0;
  background-color: #f8f9fa;
}

.contact-form {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
}

.contact-info {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
}

.info-item {
  display: flex;
  align-items: flex-start;
  margin-bottom: 2rem;
}

.info-item:last-child {
  margin-bottom: 0;
}

.info-item i {
  font-size: 24px;
  color: #d4af37;
  margin-right: 1rem;
  margin-top: 0.25rem;
}

.info-item h4 {
  margin: 0 0 0.5rem;
  font-size: 1.1rem;
  color: #333;
}

.info-item p {
  margin: 0;
  color: #666;
}

.btn-primary {
  background-color: #d4af37;
  border-color: #d4af37;
  padding: 0.75rem 2rem;
}

.btn-primary:hover {
  background-color: #c4a030;
  border-color: #c4a030;
}

.form-control:focus {
  border-color: #d4af37;
  box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
}
</style>
