<template>
  <Modal :show="true" @close="$emit('close')">
    <div class="sm:flex sm:items-start">
      <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-napps-primary sm:mx-0 sm:h-10 sm:w-10">
        <UserPlusIcon class="h-6 w-6 text-white" aria-hidden="true" />
      </div>
      <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Create Validator Account</h3>
        <div class="mt-2">
          <form @submit.prevent="createValidator" class="space-y-4">
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
              <input type="text" id="name" v-model="form.name" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-napps-primary focus:ring-napps-primary sm:text-sm" />
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
              <input type="email" id="email" v-model="form.email" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-napps-primary focus:ring-napps-primary sm:text-sm" />
            </div>

            <div>
              <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
              <input type="tel" id="phone" v-model="form.phone" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-napps-primary focus:ring-napps-primary sm:text-sm" />
            </div>

            <div>
              <label for="state" class="block text-sm font-medium text-gray-700">State</label>
              <select id="state" v-model="form.state"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-napps-primary focus:ring-napps-primary sm:text-sm">
                <option value="">Select State</option>
                <option v-for="state in states" :key="state" :value="state">{{ state }}</option>
              </select>
            </div>

            <div>
              <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
              <input type="password" id="password" v-model="form.password" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-napps-primary focus:ring-napps-primary sm:text-sm" />
              <p class="mt-1 text-sm text-gray-500">Password must be at least 8 characters long</p>
            </div>

            <div>
              <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
              <input type="password" id="password_confirmation" v-model="form.password_confirmation" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-napps-primary focus:ring-napps-primary sm:text-sm" />
            </div>

            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
              <button type="submit"
                class="inline-flex w-full justify-center rounded-md border border-transparent bg-napps-primary px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-napps-primary focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                :disabled="loading">
                {{ loading ? 'Creating...' : 'Create Validator' }}
              </button>
              <button type="button" @click="$emit('close')"
                class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-napps-primary focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">
                Cancel
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </Modal>
</template>

<script>
import { ref } from 'vue'
import { UserPlusIcon } from '@heroicons/vue/24/outline'
import Modal from '@/components/shared/Modal.vue'
import { useNotification } from '@/composables/useNotification'

export default {
  name: 'CreateValidatorModal',

  components: {
    Modal,
    UserPlusIcon
  },

  emits: ['close'],

  setup(props, { emit }) {
    const { showSuccess, showError } = useNotification()
    const loading = ref(false)
    const form = ref({
      name: '',
      email: '',
      phone: '',
      state: '',
      password: '',
      password_confirmation: ''
    })

    const states = [
      'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue',
      'Borno', 'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT',
      'Gombe', 'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi',
      'Kwara', 'Lagos', 'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo',
      'Plateau', 'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara'
    ]

    const createValidator = async () => {
      if (form.value.password !== form.value.password_confirmation) {
        showError('Error', 'Passwords do not match')
        return
      }

      if (form.value.password.length < 8) {
        showError('Error', 'Password must be at least 8 characters long')
        return
      }

      try {
        loading.value = true
        await axios.post('/api/admin/validators', form.value)
        showSuccess('Success', 'Validator account created successfully')
        emit('close')
      } catch (error) {
        if (error.response?.data?.errors) {
          const errors = Object.values(error.response.data.errors).flat()
          showError('Error', errors[0])
        } else {
          showError('Error', 'Failed to create validator account')
        }
      } finally {
        loading.value = false
      }
    }

    return {
      form,
      loading,
      states,
      createValidator
    }
  }
}
</script>
