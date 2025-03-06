<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <div class="py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Profile Settings</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Update your personal information and conference preferences.
        </p>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <form @submit.prevent="updateProfile" class="mt-6 space-y-8">
          <!-- Profile Photo -->
          <div class="space-y-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
              Profile Photo
            </label>
            <div class="flex items-center space-x-6">
              <div class="h-24 w-24 overflow-hidden rounded-full bg-gray-100">
                <img v-if="photoPreview" :src="photoPreview" class="h-full w-full object-cover" />
                <img v-else-if="user.photo_url" :src="user.photo_url" class="h-full w-full object-cover" />
                <UserCircleIcon v-else class="h-24 w-24 text-gray-300" />
              </div>
              <div>
                <input type="file" ref="photoInput" @change="updatePhotoPreview" accept="image/*" class="hidden" />
                <button type="button" @click="selectNewPhoto"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                  Change Photo
                </button>
                <button v-if="photoPreview" type="button" @click="cancelPhotoSelection"
                  class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                  Cancel
                </button>
              </div>
            </div>
          </div>

          <!-- Personal Information -->
          <div class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Personal Information</h2>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Full Name
                </label>
                <input type="text" v-model="form.name"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                <p v-if="errors.name" class="mt-2 text-sm text-red-600">{{ errors.name[0] }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Email Address
                </label>
                <input type="email" v-model="form.email"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                <p v-if="errors.email" class="mt-2 text-sm text-red-600">{{ errors.email[0] }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Phone Number
                </label>
                <input type="tel" v-model="form.phone"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                <p v-if="errors.phone" class="mt-2 text-sm text-red-600">{{ errors.phone[0] }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Gender
                </label>
                <select v-model="form.gender"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                  <option value="">Select Gender</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                </select>
                <p v-if="errors.gender" class="mt-2 text-sm text-red-600">{{ errors.gender[0] }}</p>
              </div>
            </div>
          </div>

          <!-- School Information -->
          <div class="space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white">School Information</h2>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  School Name
                </label>
                <input type="text" v-model="form.school_name"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                <p v-if="errors.school_name" class="mt-2 text-sm text-red-600">{{ errors.school_name[0] }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  School Address
                </label>
                <input type="text" v-model="form.school_address"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                <p v-if="errors.school_address" class="mt-2 text-sm text-red-600">{{ errors.school_address[0] }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Position/Role
                </label>
                <input type="text" v-model="form.position"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                <p v-if="errors.position" class="mt-2 text-sm text-red-600">{{ errors.position[0] }}</p>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end">
            <button type="submit" :disabled="loading"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
              <span v-if="loading">Updating...</span>
              <span v-else>Update Profile</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { UserCircleIcon } from '@heroicons/vue/24/solid'
import axios from 'axios'
import { useNotification } from '@/composables/useNotification'

export default {
  name: 'Profile',

  components: {
    UserCircleIcon
  },

  setup() {
    const { showSuccess, showError } = useNotification()
    const photoInput = ref(null)
    const photoPreview = ref(null)
    const loading = ref(false)
    const errors = ref({})
    const user = ref({})

    const form = reactive({
      name: '',
      email: '',
      phone: '',
      gender: '',
      school_name: '',
      school_address: '',
      position: '',
      photo: null
    })

    const fetchUserProfile = async () => {
      try {
        const response = await axios.get('/api/profile')
        user.value = response.data
        Object.assign(form, response.data)
      } catch (error) {
        console.error('Failed to fetch profile:', error)
        showError('Error', 'Failed to load profile data')
      }
    }

    const selectNewPhoto = () => {
      photoInput.value.click()
    }

    const updatePhotoPreview = (event) => {
      const file = event.target.files[0]
      if (!file) return

      if (!file.type.startsWith('image/')) {
        showError('Error', 'Please select an image file')
        return
      }

      form.photo = file
      const reader = new FileReader()
      reader.onload = (e) => {
        photoPreview.value = e.target.result
      }
      reader.readAsDataURL(file)
    }

    const cancelPhotoSelection = () => {
      form.photo = null
      photoPreview.value = null
      photoInput.value.value = ''
    }

    const updateProfile = async () => {
      try {
        loading.value = true
        errors.value = {}

        const formData = new FormData()
        Object.keys(form).forEach(key => {
          if (form[key] !== null) {
            formData.append(key, form[key])
          }
        })

        await axios.post('/api/profile', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        showSuccess('Success', 'Profile updated successfully')
        await fetchUserProfile()
      } catch (error) {
        if (error.response?.data?.errors) {
          errors.value = error.response.data.errors
        }
        showError('Error', 'Failed to update profile')
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      fetchUserProfile()
    })

    return {
      form,
      user,
      loading,
      errors,
      photoInput,
      photoPreview,
      selectNewPhoto,
      updatePhotoPreview,
      cancelPhotoSelection,
      updateProfile
    }
  }
}
</script>
