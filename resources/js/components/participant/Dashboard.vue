
<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Participant Dashboard</h1>
      </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Profile Overview -->
      <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900">Profile Information</h3>
          <p class="mt-1 max-w-2xl text-sm text-gray-500">Personal details and conference status.</p>
        </div>
        <div class="border-t border-gray-200">
          <dl>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Full name</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ profile.name }}</dd>
            </div>
            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Email address</dt>
              <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ profile.email }}</dd>
            </div>
            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
              <dt class="text-sm font-medium text-gray-500">Registration status</dt>
              <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                <span :class="[
                  profile.validated ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800',
                  'inline-flex rounded-full px-2 text-xs font-semibold leading-5'
                ]">
                  {{ profile.validated ? 'Validated' : 'Pending Validation' }}
                </span>
              </dd>
            </div>
          </dl>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="mt-8">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <button @click="showQRCode = true"
            class="relative block w-full rounded-lg p-6 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 bg-white shadow">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
            <span class="mt-2 block text-sm font-semibold text-gray-900">View QR Code</span>
          </button>

          <button @click="showMealStatus = true"
            class="relative block w-full rounded-lg p-6 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 bg-white shadow">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="mt-2 block text-sm font-semibold text-gray-900">Meal Status</span>
          </button>

          <button @click="showResources = true"
            class="relative block w-full rounded-lg p-6 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 bg-white shadow">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <span class="mt-2 block text-sm font-semibold text-gray-900">View Resources</span>
          </button>
        </div>
      </div>

      <!-- Notifications -->
      <div class="mt-8">
        <div class="bg-white shadow sm:rounded-lg">
          <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Notifications</h3>
            <div class="mt-5">
              <div class="flow-root">
                <ul role="list" class="-mb-8">
                  <li v-for="notification in notifications" :key="notification.id">
                    <div class="relative pb-8">
                      <div class="relative flex space-x-3">
                        <div>
                          <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                          </span>
                        </div>
                        <div class="min-w-0 flex-1">
                          <div>
                            <p class="text-sm text-gray-500">{{ notification.message }}</p>
                            <div class="mt-2 text-sm text-gray-500">
                              <time :datetime="notification.created_at">{{ formatDate(notification.created_at) }}</time>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Modals -->
    <QRCodeModal v-if="showQRCode" @close="showQRCode = false" :code="profile.qr_code" />
    <MealStatusModal v-if="showMealStatus" @close="showMealStatus = false" :status="profile.meal_status" />
    <ResourcesModal v-if="showResources" @close="showResources = false" :resources="resources" />
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import QRCodeModal from './modals/QRCodeModal.vue'
import MealStatusModal from './modals/MealStatusModal.vue'
import ResourcesModal from './modals/ResourcesModal.vue'

export default {
  name: 'ParticipantDashboard',

  components: {
    QRCodeModal,
    MealStatusModal,
    ResourcesModal
  },

  setup() {
    const showQRCode = ref(false)
    const showMealStatus = ref(false)
    const showResources = ref(false)
    
    const profile = ref({
      name: '',
      email: '',
      validated: false,
      qr_code: '',
      meal_status: {}
    })

    const notifications = ref([])
    const resources = ref([])

    const fetchProfile = async () => {
      try {
        const response = await fetch('/api/participant/profile')
        const data = await response.json()
        profile.value = data
      } catch (error) {
        console.error('Error fetching profile:', error)
      }
    }

    const fetchNotifications = async () => {
      try {
        const response = await fetch('/api/participant/notifications')
        const data = await response.json()
        notifications.value = data
      } catch (error) {
        console.error('Error fetching notifications:', error)
      }
    }

    const fetchResources = async () => {
      try {
        const response = await fetch('/api/participant/resources')
        const data = await response.json()
        resources.value = data
      } catch (error) {
        console.error('Error fetching resources:', error)
      }
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric'
      })
    }

    onMounted(() => {
      fetchProfile()
      fetchNotifications()
      fetchResources()
    })

    return {
      showQRCode,
      showMealStatus,
      showResources,
      profile,
      notifications,
      resources,
      formatDate
    }
  }
}
</script>
