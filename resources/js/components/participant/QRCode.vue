<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
    <div class="max-w-lg mx-auto text-center">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Your Conference QR Code</h2>
      <p class="text-gray-600 dark:text-gray-300 mb-6">
        Present this QR code during registration and meal times for validation.
      </p>

      <div v-if="loading" class="flex justify-center items-center py-8">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-napps-primary"></div>
      </div>

      <div v-else-if="qrCode" class="bg-white p-4 rounded-lg shadow-inner mb-6">
        <img :src="qrCode" alt="QR Code" class="mx-auto" />
        <p class="mt-2 text-sm text-gray-500">Participant ID: {{ user.id }}</p>
      </div>

      <div v-else class="text-red-500 dark:text-red-400 mb-6">
        Failed to load QR code. Please try again.
      </div>

      <div class="space-y-4">
        <button @click="downloadQRCode" 
          class="w-full bg-napps-primary text-white px-4 py-2 rounded-md hover:bg-blue-700 disabled:opacity-50"
          :disabled="!qrCode">
          Download QR Code
        </button>

        <button @click="refreshQRCode"
          class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200">
          Refresh QR Code
        </button>
      </div>

      <!-- Validation History -->
      <div class="mt-8">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Validation History</h3>
        <div class="space-y-4">
          <div v-if="validations.length === 0" class="text-gray-500 dark:text-gray-400">
            No validations yet
          </div>
          <div v-for="validation in validations" :key="validation.id"
            class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <div class="flex justify-between items-start">
              <div>
                <span class="font-medium text-gray-900 dark:text-white">{{ validation.type }}</span>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  Validated by: {{ validation.validator_name }}
                </p>
              </div>
              <span class="text-sm text-gray-400 dark:text-gray-500">
                {{ formatDate(validation.created_at) }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import { useNotification } from '@/composables/useNotification'
import axios from 'axios'

export default {
  name: 'QRCode',

  setup() {
    const store = useStore()
    const { showError } = useNotification()
    const loading = ref(false)
    const qrCode = ref(null)
    const validations = ref([])

    const user = computed(() => store.state.auth.user)

    const fetchQRCode = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/qr-code')
        qrCode.value = response.data.qr_code
      } catch (error) {
        showError('Error', 'Failed to load QR code')
      } finally {
        loading.value = false
      }
    }

    const fetchValidations = async () => {
      try {
        const response = await axios.get('/api/validations')
        validations.value = response.data
      } catch (error) {
        console.error('Failed to fetch validations:', error)
      }
    }

    const downloadQRCode = () => {
      const link = document.createElement('a')
      link.href = qrCode.value
      link.download = `napps-qr-${user.value.id}.png`
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
    }

    const refreshQRCode = () => {
      fetchQRCode()
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-NG', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    onMounted(() => {
      fetchQRCode()
      fetchValidations()
    })

    return {
      user,
      loading,
      qrCode,
      validations,
      downloadQRCode,
      refreshQRCode,
      formatDate
    }
  }
}
</script>
