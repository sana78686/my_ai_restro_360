<template>
  <div class="text-center py-5">
    <h2>Processing your subscription…</h2>
    <p>Please wait, this may take a moment.</p>
    <div class="spinner-border text-danger mt-3"></div>
  </div>
</template>

<script setup>
import { onMounted } from "vue"
import axios from "axios"
import { useRouter } from "vue-router"

const router = useRouter()

onMounted(async () => {
  const params = new URLSearchParams(window.location.search)
  const sessionId = params.get('session_id')

  if (!sessionId) {
    router.push('/dashboard/pricing?checkout=missing-session')
    return
  }

  try {
    const { data } = await axios.get(`/tenant/verifyCheckoutSession?session_id=${sessionId}`)

    if (data.success) {
      // success → redirect with success message
      router.push('/dashboard/pricing?checkout=success')

    } else {
      // payment pending or other
      router.push('/dashboard/pricing?checkout=pending')
    }

  } catch (e) {
    console.error(e)
    router.push('/dashboard/pricing?checkout=error')
  }
})
</script>
