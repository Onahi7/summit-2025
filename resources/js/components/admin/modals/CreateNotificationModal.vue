<template>
  <Modal :show="true" @close="$emit('close')">
    <div class="sm:flex sm:items-start">
      <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-napps-primary sm:mx-0 sm:h-10 sm:w-10">
        <BellIcon class="h-6 w-6 text-white" aria-hidden="true" />
      </div>
      <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Create Notification</h3>
        <div class="mt-2">
          <form @submit.prevent="createNotification" class="space-y-4">
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
              <input type="text" id="title" v-model="form.title" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-napps-primary focus:ring-napps-primary sm:text-sm" />
            </div>

            <div>
              <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
              <textarea id="description" v-model="form.description" rows="3" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-napps-primary focus:ring-napps-primary sm:text-sm"></textarea>
            </div>

            <div>
              <label for="urgency" class="block text-sm font-medium text-gray-700">Urgency Level</label>
              <select id="urgency" v-model="form.urgency"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-napps-primary focus:ring-napps-primary sm:text-sm">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
              </select>
            </div>

            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
              <button type="submit"
                class="inline-flex w-full justify-center rounded-md border border-transparent bg-napps-primary px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-napps-primary focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                :disabled="loading">
                {{ loading ? 'Creating...' : 'Create Notification' }}
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
import { BellIcon } from '@heroicons/vue/24/outline'
import Modal from '@/components/shared/Modal.vue'
import { useNotification } from '@/composables/useNotification'

export default {
  name: 'CreateNotificationModal',

  components: {
    Modal,
    BellIcon
  },

  emits: ['close'],

  setup(props, { emit }) {
    const { showSuccess, showError } = useNotification()
    const loading = ref(false)
    const form = ref({
      title: '',
      description: '',
      urgency: 'medium'
    })

    const createNotification = async () => {
      try {
        loading.value = true
        await axios.post('/api/admin/notifications', form.value)
        showSuccess('Success', 'Notification created successfully')
        emit('close')
      } catch (error) {
        showError('Error', 'Failed to create notification')
      } finally {
        loading.value = false
      }
    }

    return {
      form,
      loading,
      createNotification
    }
  }
}
</script>
