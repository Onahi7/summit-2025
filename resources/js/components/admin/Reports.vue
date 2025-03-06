<template>
  <div class="min-h-screen bg-gray-100">
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Reports</h1>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
          <!-- Registration Report -->
          <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900">Registration Report</h3>
            <p class="mt-1 text-sm text-gray-500">Download detailed registration data including payment status and participant details.</p>
            
            <div class="mt-6 space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Start Date</label>
                  <input 
                    type="date" 
                    v-model="registrationFilters.startDate"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">End Date</label>
                  <input 
                    type="date" 
                    v-model="registrationFilters.endDate"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  >
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">Payment Status</label>
                <select 
                  v-model="registrationFilters.paymentStatus"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                >
                  <option value="">All</option>
                  <option value="paid">Paid</option>
                  <option value="pending">Pending</option>
                </select>
              </div>

              <button 
                @click="downloadRegistrationReport"
                :disabled="isLoading"
                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Download Registration Report
              </button>
            </div>
          </div>

          <!-- Meal Report -->
          <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900">Meal Validation Report</h3>
            <p class="mt-1 text-sm text-gray-500">Download meal validation data including morning and evening meal counts.</p>
            
            <div class="mt-6 space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Start Date</label>
                  <input 
                    type="date" 
                    v-model="mealFilters.startDate"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">End Date</label>
                  <input 
                    type="date" 
                    v-model="mealFilters.endDate"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  >
                </div>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700">Meal Type</label>
                <select 
                  v-model="mealFilters.mealType"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                >
                  <option value="">All</option>
                  <option value="morning">Morning</option>
                  <option value="evening">Evening</option>
                </select>
              </div>

              <button 
                @click="downloadMealReport"
                :disabled="isLoading"
                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Download Meal Report
              </button>
            </div>
          </div>

          <!-- Financial Report -->
          <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900">Financial Report</h3>
            <p class="mt-1 text-sm text-gray-500">Download financial data including revenue, payment methods, and transaction details.</p>
            
            <div class="mt-6 space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Start Date</label>
                  <input 
                    type="date" 
                    v-model="financialFilters.startDate"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">End Date</label>
                  <input 
                    type="date" 
                    v-model="financialFilters.endDate"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                  >
                </div>
              </div>

              <button 
                @click="downloadFinancialReport"
                :disabled="isLoading"
                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Download Financial Report
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'
import axios from 'axios'
import { useNotification } from '@/composables/useNotification'

export default {
  name: 'AdminReports',

  setup() {
    const isLoading = ref(false)
    const { showSuccess, showError } = useNotification()

    const registrationFilters = ref({
      startDate: '',
      endDate: '',
      paymentStatus: ''
    })

    const mealFilters = ref({
      startDate: '',
      endDate: '',
      mealType: ''
    })

    const financialFilters = ref({
      startDate: '',
      endDate: ''
    })

    const downloadReport = async (endpoint, filters, filename) => {
      if (isLoading.value) return

      isLoading.value = true
      try {
        const response = await axios.get(endpoint, {
          params: filters,
          responseType: 'blob'
        })

        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', filename)
        document.body.appendChild(link)
        link.click()
        link.remove()

        showSuccess('Success', 'Report downloaded successfully')
      } catch (error) {
        showError('Error', 'Failed to download report')
      } finally {
        isLoading.value = false
      }
    }

    const downloadRegistrationReport = () => {
      downloadReport(
        '/api/admin/reports/registrations',
        registrationFilters.value,
        'registration-report.csv'
      )
    }

    const downloadMealReport = () => {
      downloadReport(
        '/api/admin/reports/meals',
        mealFilters.value,
        'meal-validation-report.csv'
      )
    }

    const downloadFinancialReport = () => {
      downloadReport(
        '/api/admin/reports/financial',
        financialFilters.value,
        'financial-report.csv'
      )
    }

    return {
      isLoading,
      registrationFilters,
      mealFilters,
      financialFilters,
      downloadRegistrationReport,
      downloadMealReport,
      downloadFinancialReport
    }
  }
}
</script>
