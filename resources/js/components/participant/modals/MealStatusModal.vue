<template>
  <div class="fixed inset-0 z-10 overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
      </div>

      <div class="inline-block transform overflow-hidden rounded-lg bg-white px-4 pt-5 pb-4 text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm sm:p-6 sm:align-middle">
        <div>
          <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="mt-3 text-center sm:mt-5">
            <h3 class="text-lg font-medium leading-6 text-gray-900">Meal Status</h3>
            <div class="mt-2">
              <div class="space-y-4">
                <div v-for="(value, meal) in status" :key="meal" class="flex justify-between px-4">
                  <span class="text-sm font-medium text-gray-500 capitalize">{{ formatMealName(meal) }}</span>
                  <span :class="[
                    value ? 'text-green-600' : 'text-red-600',
                    'text-sm font-semibold'
                  ]">
                    {{ value ? 'Served' : 'Not Served' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-5 sm:mt-6">
          <button
            type="button"
            class="inline-flex w-full justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:text-sm"
            @click="$emit('close')"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'MealStatusModal',
  props: {
    status: {
      type: Object,
      required: true,
      default: () => ({
        breakfast: false,
        lunch: false,
        dinner: false
      })
    }
  },
  setup() {
    const formatMealName = (meal) => {
      return meal.charAt(0).toUpperCase() + meal.slice(1)
    }

    return {
      formatMealName
    }
  }
}
</script>
