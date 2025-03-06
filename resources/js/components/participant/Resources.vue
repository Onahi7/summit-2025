<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Conference Resources</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Access conference schedules, documents, presentations, and guidelines.
        </p>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <!-- Resource Type Filter -->
        <div class="mt-4">
          <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
              <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <select v-model="selectedType"
                  class="mt-1 block w-full rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                  <option value="">All Resources</option>
                  <option value="schedule">Schedules</option>
                  <option value="document">Documents</option>
                  <option value="presentation">Presentations</option>
                  <option value="guideline">Guidelines</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Resource Grid -->
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <div v-for="resource in filteredResources" :key="resource.id"
            class="relative flex flex-col overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow transition hover:shadow-lg">
            <!-- Resource Icon -->
            <div class="p-6">
              <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                <component :is="getResourceIcon(resource.type)" class="h-6 w-6" />
              </div>
              <div class="mt-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ resource.title }}</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ resource.description }}</p>
              </div>
            </div>

            <!-- Resource Actions -->
            <div class="flex-1 flex flex-col justify-end">
              <div class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-6 py-3">
                <div class="flex items-center justify-between">
                  <span class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ formatDate(resource.created_at) }}
                  </span>
                  <a :href="resource.file_url" target="_blank"
                    class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-3 py-2 text-sm font-medium leading-4 text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <DocumentArrowDownIcon class="h-4 w-4 mr-1" />
                    Download
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredResources.length === 0" class="text-center mt-8">
          <DocumentIcon class="mx-auto h-12 w-12 text-gray-400" />
          <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No resources</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            No resources are available for the selected type.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import {
  DocumentIcon,
  DocumentArrowDownIcon,
  CalendarIcon,
  PresentationChartBarIcon,
  BookOpenIcon
} from '@heroicons/vue/24/outline'
import axios from 'axios'

export default {
  name: 'Resources',

  components: {
    DocumentIcon,
    DocumentArrowDownIcon,
    CalendarIcon,
    PresentationChartBarIcon,
    BookOpenIcon
  },

  setup() {
    const resources = ref([])
    const selectedType = ref('')
    const loading = ref(true)

    const fetchResources = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/resources', {
          params: { type: selectedType.value }
        })
        resources.value = response.data
      } catch (error) {
        console.error('Failed to fetch resources:', error)
      } finally {
        loading.value = false
      }
    }

    const filteredResources = computed(() => {
      if (!selectedType.value) return resources.value
      return resources.value.filter(resource => resource.type === selectedType.value)
    })

    const getResourceIcon = (type) => {
      return {
        schedule: CalendarIcon,
        document: DocumentIcon,
        presentation: PresentationChartBarIcon,
        guideline: BookOpenIcon
      }[type] || DocumentIcon
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-NG', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }

    onMounted(() => {
      fetchResources()
    })

    return {
      resources,
      selectedType,
      loading,
      filteredResources,
      getResourceIcon,
      formatDate
    }
  }
}
</script>
