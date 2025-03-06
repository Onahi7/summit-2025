<template>
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
    <div class="max-w-lg mx-auto">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Meal Status</h2>
      <p class="text-gray-600 dark:text-gray-300 mb-6">
        Track your meal validations for the conference.
      </p>

      <!-- Today's Status -->
      <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Today's Status</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Morning Meal -->
          <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <div class="flex items-center justify-between">
              <div>
                <h4 class="font-medium text-gray-900 dark:text-white">Morning Meal</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatTime(morningMealTime) }}</p>
              </div>
              <div :class="{
                'bg-green-100 text-green-800': todayMeals.morning,
                'bg-gray-100 text-gray-800': !todayMeals.morning
              }" class="px-2 py-1 rounded-full text-sm font-medium">
                {{ todayMeals.morning ? 'Validated' : 'Not Validated' }}
              </div>
            </div>
          </div>

          <!-- Evening Meal -->
          <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <div class="flex items-center justify-between">
              <div>
                <h4 class="font-medium text-gray-900 dark:text-white">Evening Meal</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ formatTime(eveningMealTime) }}</p>
              </div>
              <div :class="{
                'bg-green-100 text-green-800': todayMeals.evening,
                'bg-gray-100 text-gray-800': !todayMeals.evening
              }" class="px-2 py-1 rounded-full text-sm font-medium">
                {{ todayMeals.evening ? 'Validated' : 'Not Validated' }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Validation History -->
      <div>
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Validation History</h3>
        <div class="space-y-4">
          <div v-if="mealHistory.length === 0" class="text-gray-500 dark:text-gray-400">
            No meal validations yet
          </div>
          <div v-for="meal in mealHistory" :key="meal.id"
            class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
            <div class="flex justify-between items-start">
              <div>
                <span class="font-medium text-gray-900 dark:text-white">{{ meal.type }}</span>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                  Validated by: {{ meal.validator_name }}
                </p>
              </div>
              <span class="text-sm text-gray-400 dark:text-gray-500">
                {{ formatDate(meal.created_at) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Load More Button -->
        <div v-if="hasMoreHistory" class="mt-4 text-center">
          <button @click="loadMoreHistory"
            class="text-napps-primary hover:text-blue-700 font-medium"
            :disabled="loadingMore">
            {{ loadingMore ? 'Loading...' : 'Load More' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'MealStatus',

  setup() {
    const todayMeals = ref({
      morning: false,
      evening: false
    })
    const mealHistory = ref([])
    const hasMoreHistory = ref(false)
    const loadingMore = ref(false)
    const currentPage = ref(1)
    const morningMealTime = ref('07:00 - 09:00')
    const eveningMealTime = ref('18:00 - 20:00')

    const fetchTodayStatus = async () => {
      try {
        const response = await axios.get('/api/meals/today')
        todayMeals.value = response.data
      } catch (error) {
        console.error('Failed to fetch today\'s meal status:', error)
      }
    }

    const fetchMealHistory = async (page = 1) => {
      try {
        loadingMore.value = true
        const response = await axios.get(`/api/meals/history?page=${page}`)
        if (page === 1) {
          mealHistory.value = response.data.data
        } else {
          mealHistory.value = [...mealHistory.value, ...response.data.data]
        }
        hasMoreHistory.value = response.data.next_page_url !== null
        currentPage.value = page
      } catch (error) {
        console.error('Failed to fetch meal history:', error)
      } finally {
        loadingMore.value = false
      }
    }

    const loadMoreHistory = () => {
      fetchMealHistory(currentPage.value + 1)
    }

    const formatTime = (time) => {
      return time
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-NG', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    onMounted(() => {
      fetchTodayStatus()
      fetchMealHistory()
    })

    return {
      todayMeals,
      mealHistory,
      hasMoreHistory,
      loadingMore,
      morningMealTime,
      eveningMealTime,
      loadMoreHistory,
      formatTime,
      formatDate
    }
  }
}
</script>
