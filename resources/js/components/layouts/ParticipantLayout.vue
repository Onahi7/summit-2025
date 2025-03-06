<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900" :class="{ 'dark': isDarkMode }">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-30 w-64 transform bg-white dark:bg-gray-800 lg:translate-x-0 lg:static lg:inset-0"
      :class="{ '-translate-x-full': !isSidebarOpen }">
      <div class="flex items-center justify-between flex-shrink-0 p-4">
        <span class="p-2 text-xl font-semibold leading-8 tracking-wider text-gray-900 dark:text-white">
          NAPPS Conference
        </span>
        <button @click="toggleSidebar" class="p-2 lg:hidden">
          <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <nav class="flex-1 overflow-hidden hover:overflow-y-auto">
        <ul class="p-2 overflow-hidden">
          <li>
            <router-link to="/dashboard/participant" class="flex items-center p-2 space-x-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
              :class="{ 'bg-gray-100 dark:bg-gray-700': $route.path === '/dashboard/participant' }">
              <span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
              </span>
              <span>Dashboard</span>
            </router-link>
          </li>

          <li>
            <router-link to="/dashboard/participant/profile" class="flex items-center p-2 space-x-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
              :class="{ 'bg-gray-100 dark:bg-gray-700': $route.path === '/dashboard/participant/profile' }">
              <span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </span>
              <span>Profile</span>
            </router-link>
          </li>

          <li>
            <router-link to="/dashboard/participant/qr-code" class="flex items-center p-2 space-x-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
              :class="{ 'bg-gray-100 dark:bg-gray-700': $route.path === '/dashboard/participant/qr-code' }">
              <span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
              </span>
              <span>QR Code</span>
            </router-link>
          </li>

          <li>
            <router-link to="/dashboard/participant/resources" class="flex items-center p-2 space-x-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
              :class="{ 'bg-gray-100 dark:bg-gray-700': $route.path === '/dashboard/participant/resources' }">
              <span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
              </span>
              <span>Resources</span>
            </router-link>
          </li>

          <li>
            <router-link to="/dashboard/participant/meals" class="flex items-center p-2 space-x-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700"
              :class="{ 'bg-gray-100 dark:bg-gray-700': $route.path === '/dashboard/participant/meals' }">
              <span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </span>
              <span>Meals</span>
            </router-link>
          </li>
        </ul>
      </nav>

      <div class="flex-shrink-0 p-4 border-t border-gray-200 dark:border-gray-700">
        <button @click="toggleDarkMode" class="flex items-center justify-center w-full px-4 py-2 space-x-2 text-sm text-gray-600 transition-colors duration-200 rounded-md dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
          <svg v-if="isDarkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
          </svg>
          <span>{{ isDarkMode ? 'Light Mode' : 'Dark Mode' }}</span>
        </button>

        <button @click="logout" class="flex items-center justify-center w-full px-4 py-2 mt-4 space-x-2 text-sm text-gray-600 transition-colors duration-200 rounded-md dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          <span>Logout</span>
        </button>
      </div>
    </div>

    <!-- Main content -->
    <div class="flex-1">
      <!-- Navbar -->
      <header class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 shadow-md">
        <button @click="toggleSidebar" class="p-1 lg:hidden">
          <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>

        <div class="flex items-center space-x-4">
          <div class="relative">
            <button @click="toggleNotifications" class="p-1 relative">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <span v-if="unreadNotifications.length" class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- Notifications dropdown -->
            <div v-if="showNotifications" class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50">
              <div class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Notifications
              </div>
              <div v-if="notifications.length === 0" class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                No new notifications
              </div>
              <template v-else>
                <div v-for="notification in notifications" :key="notification.id" class="px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                  <div class="text-sm font-medium text-gray-900 dark:text-white">{{ notification.title }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ notification.message }}</div>
                  <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ formatDate(notification.created_at) }}</div>
                </div>
              </template>
            </div>
          </div>
        </div>
      </header>

      <!-- Page content -->
      <main class="p-6">
        <router-view></router-view>
      </main>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'

export default {
  name: 'ParticipantLayout',

  setup() {
    const router = useRouter()
    const store = useStore()
    const isSidebarOpen = ref(false)
    const isDarkMode = ref(localStorage.getItem('darkMode') === 'true')
    const showNotifications = ref(false)
    const notifications = ref([])
    const unreadNotifications = ref([])

    const toggleSidebar = () => {
      isSidebarOpen.value = !isSidebarOpen.value
    }

    const toggleDarkMode = () => {
      isDarkMode.value = !isDarkMode.value
      localStorage.setItem('darkMode', isDarkMode.value)
    }

    const toggleNotifications = () => {
      showNotifications.value = !showNotifications.value
      if (showNotifications.value) {
        markNotificationsAsRead()
      }
    }

    const fetchNotifications = async () => {
      try {
        const response = await axios.get('/api/notifications')
        notifications.value = response.data
        unreadNotifications.value = notifications.value.filter(n => !n.read_at)
      } catch (error) {
        console.error('Failed to fetch notifications:', error)
      }
    }

    const markNotificationsAsRead = async () => {
      if (unreadNotifications.value.length === 0) return

      try {
        await axios.post('/api/notifications/mark-as-read')
        unreadNotifications.value = []
      } catch (error) {
        console.error('Failed to mark notifications as read:', error)
      }
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

    const logout = async () => {
      try {
        await store.dispatch('auth/logout')
        router.push('/login')
      } catch (error) {
        console.error('Logout failed:', error)
      }
    }

    // Close notifications dropdown when clicking outside
    const handleClickOutside = (event) => {
      if (showNotifications.value && !event.target.closest('.notifications-dropdown')) {
        showNotifications.value = false
      }
    }

    onMounted(() => {
      fetchNotifications()
      document.addEventListener('click', handleClickOutside)
    })

    watch(isDarkMode, (newValue) => {
      if (newValue) {
        document.documentElement.classList.add('dark')
      } else {
        document.documentElement.classList.remove('dark')
      }
    }, { immediate: true })

    return {
      isSidebarOpen,
      isDarkMode,
      showNotifications,
      notifications,
      unreadNotifications,
      toggleSidebar,
      toggleDarkMode,
      toggleNotifications,
      formatDate,
      logout
    }
  }
}
</script>

<style>
.dark {
  color-scheme: dark;
}
</style>
