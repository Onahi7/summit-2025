<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="text-center text-3xl font-extrabold text-gray-900">
        Sign in to your account
      </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <form class="space-y-6" @submit.prevent="handleLogin">
          <!-- Error Alert -->
          <div v-if="error" class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ error }}</span>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
            <div class="mt-1">
              <input v-model="form.email" id="email" type="email" required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <div class="mt-1">
              <input v-model="form.password" id="password" type="password" required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
            </div>
          </div>

          <div>
            <button type="submit" :disabled="loading"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
              {{ loading ? 'Signing in...' : 'Sign in' }}
            </button>
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white text-gray-500">
                Don't have an account?
                <router-link to="/register" class="font-medium text-blue-600 hover:text-blue-500">
                  Register here
                </router-link>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'
import axios from 'axios'

export default {
  setup() {
    const router = useRouter()
    const store = useStore()
    const form = ref({
      email: '',
      password: ''
    })
    const loading = ref(false)
    const error = ref(null)

    const getCsrfToken = async () => {
      try {
        await axios.get('/sanctum/csrf-cookie')
      } catch (err) {
        console.error('Failed to get CSRF token:', err)
      }
    }

    const handleLogin = async () => {
      try {
        loading.value = true
        error.value = null
        
        // Get CSRF token before login
        await getCsrfToken()
        
        await store.dispatch('auth/login', form.value)
        
        const user = store.getters['auth/getUser']
        if (user) {
          switch (user.role) {
            case 'admin':
              router.push('/admin')
              break
            case 'validator':
              router.push('/validator')
              break
            default:
              router.push('/dashboard')
          }
        }
      } catch (err) {
        error.value = err.response?.data?.message || 'Login failed. Please try again.'
        console.error('Login error:', err)
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      // Get CSRF token when component mounts
      getCsrfToken()
    })

    return {
      form,
      loading,
      error,
      handleLogin
    }
  }
}
</script>
