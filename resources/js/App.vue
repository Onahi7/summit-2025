<template>
  <div>
    <!-- Temporarily comment out nav bar to focus on dashboard rendering
    <nav v-if="isAuthenticated" class="bg-gray-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <span class="text-white font-bold">NAPPS Conference</span>
            </div>
            <div class="hidden md:block">
              <div class="ml-10 flex items-baseline space-x-4">
                <router-link to="/profile"
                  class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                  Profile
                </router-link>
                <router-link to="/qr-code"
                  class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                  QR Code
                </router-link>
                <router-link v-if="isValidator" to="/validator"
                  class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
                  Validate QR
                </router-link>
              </div>
            </div>
          </div>
          <div>
            <button @click="logout"
              class="text-gray-300 hover:bg-gray-700 hover:text-white px-3 py-2 rounded-md text-sm font-medium">
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>
    -->

    <main>
      <router-view></router-view>
    </main>

    <NotificationContainer />
  </div>
</template>

<script>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'
import NotificationContainer from './components/common/NotificationContainer.vue'

export default {
  components: {
    NotificationContainer
  },

  setup() {
    const router = useRouter()
    const store = useStore()

    const isAuthenticated = computed(() => store.state.auth.token)
    const isValidator = computed(() => store.state.auth.user?.role === 'validator')

    const logout = async () => {
      try {
        await store.dispatch('auth/logout')
        router.push('/login')
      } catch (error) {
        console.error('Logout failed:', error)
      }
    }

    return {
      isAuthenticated,
      isValidator,
      logout
    }
  }
}
</script>
