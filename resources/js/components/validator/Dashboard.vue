<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Validator Dashboard</h1>
      </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Scanner Section -->
      <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">QR Code Scanner</h3>
          <div class="mt-2 max-w-xl text-sm text-gray-500">
            <p>Scan participant QR codes to validate their attendance.</p>
          </div>
          <div class="mt-5">
            <button @click="startScanning" 
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Start Scanner
            </button>
          </div>
          <div v-if="isScanning" class="mt-4">
            <div class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg overflow-hidden">
              <!-- QR Scanner component will be mounted here -->
              <div ref="scanner" class="w-full h-full"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Scans -->
      <div class="mt-8">
        <div class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Validations</h3>
            <div class="mt-5">
              <div class="flow-root">
                <ul role="list" class="-my-5 divide-y divide-gray-200">
                  <li v-for="scan in recentScans" :key="scan.id" class="py-4">
                    <div class="flex items-center space-x-4">
                      <div class="flex-shrink-0">
                        <span class="inline-flex items-center justify-center h-12 w-12 rounded-full" 
                          :class="scan.success ? 'bg-green-100' : 'bg-red-100'">
                          <svg v-if="scan.success" class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                          </svg>
                          <svg v-else class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                          </svg>
                        </span>
                      </div>
                      <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                          {{ scan.participant_name }}
                        </p>
                        <p class="text-sm text-gray-500">
                          {{ scan.success ? 'Successfully validated' : 'Validation failed' }}
                        </p>
                      </div>
                      <div>
                        <p class="text-sm text-gray-500">
                          {{ formatTime(scan.timestamp) }}
                        </p>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div class="mt-8">
        <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
          <div class="bg-white shadow rounded-lg px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Today's Validations</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.todayValidations }}</dd>
          </div>

          <div class="bg-white shadow rounded-lg px-4 py-5 sm:p-6">
            <dt class="text-sm font-medium text-gray-500 truncate">Total Validations</dt>
            <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.totalValidations }}</dd>
          </div>
        </dl>
      </div>
    </main>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'
import { Html5QrcodeScanner } from 'html5-qrcode'

export default {
  name: 'ValidatorDashboard',

  setup() {
    const scanner = ref(null)
    const isScanning = ref(false)
    const recentScans = ref([])
    const stats = ref({
      todayValidations: 0,
      totalValidations: 0
    })

    let html5QrcodeScanner = null

    const startScanning = () => {
      isScanning.value = true
      html5QrcodeScanner = new Html5QrcodeScanner(
        "scanner",
        { fps: 10, qrbox: { width: 250, height: 250 } },
        false
      )
      
      html5QrcodeScanner.render(onScanSuccess, onScanFailure)
    }

    const onScanSuccess = async (decodedText) => {
      try {
        const response = await fetch('/api/validator/validate', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ qr_code: decodedText })
        })
        
        const data = await response.json()
        
        if (data.success) {
          recentScans.value.unshift({
            id: Date.now(),
            participant_name: data.participant_name,
            success: true,
            timestamp: new Date()
          })
          await fetchStats()
        }
      } catch (error) {
        console.error('Validation error:', error)
        recentScans.value.unshift({
          id: Date.now(),
          participant_name: 'Unknown',
          success: false,
          timestamp: new Date()
        })
      }
    }

    const onScanFailure = (error) => {
      console.warn(`QR scan error: ${error}`)
    }

    const fetchStats = async () => {
      try {
        const response = await fetch('/api/validator/stats')
        const data = await response.json()
        stats.value = data
      } catch (error) {
        console.error('Error fetching stats:', error)
      }
    }

    const formatTime = (timestamp) => {
      return new Date(timestamp).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    onMounted(() => {
      fetchStats()
    })

    onUnmounted(() => {
      if (html5QrcodeScanner) {
        html5QrcodeScanner.clear()
      }
    })

    return {
      isScanning,
      recentScans,
      stats,
      startScanning,
      formatTime
    }
  }
}
</script>
