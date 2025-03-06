<template>
  <div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Meal Tracking</h1>
      <p class="mt-2 text-gray-600 dark:text-gray-300">
        Track your meal validations for the conference.
      </p>
    </div>

    <!-- Today's Status -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
      <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Today's Status</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Morning Meal -->
        <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <div class="p-3 rounded-full" :class="morningMealColor">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Morning Meal</h3>
            <p class="mt-1 text-sm" :class="morningMealTextColor">{{ morningMealStatus }}</p>
          </div>
        </div>

        <!-- Evening Meal -->
        <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <div class="p-3 rounded-full" :class="eveningMealColor">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Evening Meal</h3>
            <p class="mt-1 text-sm" :class="eveningMealTextColor">{{ eveningMealStatus }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Meal History -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
      <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Meal History</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Date
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Morning Meal
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Evening Meal
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Validator
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="record in mealHistory" :key="record.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                {{ formatDate(record.date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(record.morning_status)">
                  {{ record.morning_status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(record.evening_status)">
                  {{ record.evening_status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ record.validator_name }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useNotification } from '@/composables/useNotification'
import axios from 'axios'

export default {
  name: 'ParticipantMeals',

  setup() {
    const mealHistory = ref([])
    const todayMeals = ref({
      morning: null,
      evening: null
    })
    const { showError } = useNotification()

    const morningMealStatus = computed(() => {
      if (!todayMeals.value.morning) return 'Not Validated'
      return 'Validated'
    })

    const eveningMealStatus = computed(() => {
      if (!todayMeals.value.evening) return 'Not Validated'
      return 'Validated'
    })

    const morningMealColor = computed(() => {
      return todayMeals.value.morning ? 'bg-green-500' : 'bg-yellow-500'
    })

    const eveningMealColor = computed(() => {
      return todayMeals.value.evening ? 'bg-green-500' : 'bg-yellow-500'
    })

    const morningMealTextColor = computed(() => {
      return todayMeals.value.morning ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400'
    })

    const eveningMealTextColor = computed(() => {
      return todayMeals.value.evening ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400'
    })

    const getStatusClass = (status) => {
      return status === 'Validated'
        ? 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
        : 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100'
    }

    const fetchMealHistory = async () => {
      try {
        const response = await axios.get('/api/participant/meals/history')
        mealHistory.value = response.data
      } catch (error) {
        showError('Error', 'Failed to fetch meal history')
      }
    }

    const fetchTodayMeals = async () => {
      try {
        const response = await axios.get('/api/participant/meals/today')
        todayMeals.value = response.data
      } catch (error) {
        showError('Error', 'Failed to fetch today\'s meal status')
      }
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-NG', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    onMounted(() => {
      fetchMealHistory()
      fetchTodayMeals()
    })

    return {
      mealHistory,
      morningMealStatus,
      eveningMealStatus,
      morningMealColor,
      eveningMealColor,
      morningMealTextColor,
      eveningMealTextColor,
      getStatusClass,
      formatDate
    }
  }
}
</script>
