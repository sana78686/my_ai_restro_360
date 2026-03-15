<template>
  <VOnboardingWrapper ref="onboarding" :steps="steps" v-if="showTour" @finish="onFinish" @exit="onFinish" />
</template>

<script setup>
import { ref, watch, defineExpose } from 'vue'
import { VOnboardingWrapper } from 'v-onboarding'
import 'v-onboarding/dist/style.css'

const props = defineProps({
  steps: { type: Array, required: true },
  trigger: { type: Boolean, default: false }
})

const showTour = ref(false)
const onboarding = ref(null)

watch(() => props.trigger, (val) => {
  if (val) start()
})

function start() {
  showTour.value = true
  setTimeout(() => {
    onboarding.value?.start()
  }, 50)
}

function onFinish() {
  showTour.value = false
}

defineExpose({ start })
</script>

<style scoped>
/* Custom styles for a professional look */
.v-onboarding-step {
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(44, 62, 80, 0.18);
  font-family: 'Poppins', Arial, sans-serif;
}
.v-onboarding-step__content {
  font-size: 1.08rem;
  color: #333;
}
.v-onboarding-step__title {
  font-weight: 600;
  color: #c62828;
  font-size: 1.2rem;
}
</style> 