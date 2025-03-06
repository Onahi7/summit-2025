<template>
  <div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
          <div class="max-w-3xl mx-auto">
            <h2 class="text-2xl font-bold mb-6">QR Code Validation</h2>

            <div class="space-y-8">
              <!-- QR Code Scanner -->
              <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                <div class="text-center">
                  <qrcode-stream v-if="showScanner" @decode="onDecode" @init="onInit">
                    <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 text-white">
                      <loading-spinner message="Starting camera..." />
                    </div>
                  </qrcode-stream>

                  <div v-else class="space-y-4">
                    <button @click="startScanner" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                      <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      Start Scanner
                    </button>

                    <div class="relative">
                      <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                      </div>
                      <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Or</span>
                      </div>
                    </div>

                    <div class="flex space-x-2">
                      <input 
                        type="text" 
                        v-model="qrCode" 
                        placeholder="Enter QR code manually"
                        class="flex-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                      />
                      <button 
                        @click="validateQrCode"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                      >
                        Validate
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Participant Details -->
              <div v-if="participant" class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Participant Details</h3>
                <div class="grid grid-cols-2 gap-4">
                  <div class="text-sm text-gray-500">Name:</div>
                  <div class="text-sm font-medium">{{ participant.user.name }}</div>
                  
                  <div class="text-sm text-gray-500">Email:</div>
                  <div class="text-sm font-medium">{{ participant.user.email }}</div>
                  
                  <div class="text-sm text-gray-500">Phone:</div>
                  <div class="text-sm font-medium">{{ participant.phone_number }}</div>
                  
                  <div class="text-sm text-gray-500">State:</div>
                  <div class="text-sm font-medium">{{ participant.state }}</div>
                  
                  <div class="text-sm text-gray-500">Chapter:</div>
                  <div class="text-sm font-medium">{{ participant.napps_chapter }}</div>
                  
                  <div class="text-sm text-gray-500">Ticket Type:</div>
                  <div class="text-sm font-medium">{{ participant.ticket_type }}</div>
                </div>

                <!-- Meal Validation -->
                <div class="mt-6">
                  <h4 class="text-md font-medium text-gray-900 mb-3">Validate Meal</h4>
                  <div class="flex space-x-4">
                    <button 
                      @click="validateMeal('morning')"
                      class="flex-1 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                    >
                      Morning Meal
                    </button>
                    <button 
                      @click="validateMeal('evening')"
                      class="flex-1 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                    >
                      Evening Meal
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import { QrcodeStream } from 'vue-qrcode-reader'
import { useNotification } from '@/composables/useNotification'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import axios from 'axios'

export default {
  name: 'QrValidator',
  
  components: {
    QrcodeStream,
    LoadingSpinner
  },

  setup() {
    const qrCode = ref('')
    const participant = ref(null)
    const showScanner = ref(false)
    const loading = ref(false)
    const { showSuccess, showError } = useNotification()

    const startScanner = () => {
      showScanner.value = true
    }

    const onInit = async promise => {
      loading.value = true
      try {
        await promise
      } catch (error) {
        showError('Camera Error', error.message || 'Failed to start camera')
      } finally {
        loading.value = false
      }
    }

    const onDecode = async result => {
      qrCode.value = result
      await validateQrCode()
    }

    const validateQrCode = async () => {
      if (!qrCode.value) return

      try {
        const response = await axios.post('/api/validate-qr', {
          qr_code: qrCode.value
        })

        participant.value = response.data.participant
        showSuccess('Success', 'QR code validated successfully')
        showScanner.value = false
        qrCode.value = ''
      } catch (error) {
        showError('Validation Error', error.response?.data?.message || 'Invalid QR code')
        participant.value = null
      }
    }

    const validateMeal = async (mealType) => {
      if (!participant.value) return

      try {
        await axios.post('/api/validate-meal', {
          qr_code: participant.value.qr_code,
          meal_type: mealType
        })

        showSuccess('Success', `${mealType.charAt(0).toUpperCase() + mealType.slice(1)} meal validated successfully`)
      } catch (error) {
        showError('Validation Error', error.response?.data?.message || 'Meal validation failed')
      }
    }

    return {
      qrCode,
      participant,
      showScanner,
      loading,
      startScanner,
      onInit,
      onDecode,
      validateQrCode,
      validateMeal
    }
  }
}
</script>
