<template>
  <Modal :show="true" @close="$emit('close')">
    <div class="sm:flex sm:items-start">
      <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-napps-primary sm:mx-0 sm:h-10 sm:w-10">
        <UserIcon class="h-6 w-6 text-white" aria-hidden="true" />
      </div>
      <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
        <h3 class="text-lg font-medium leading-6 text-gray-900">
          {{ participant.isEditing ? 'Edit Participant' : 'Participant Details' }}
        </h3>
        <div class="mt-2">
          <form v-if="participant.isEditing" @submit.prevent="updateParticipant" class="space-y-4">
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
              <label for="school" class="block text-sm font-medium text-gray-700">School Name</label>
              <input type="text" id="school" v-model="form.school" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-napps-primary focus:ring-napps-primary sm:text-sm" />
            </div>

            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
              <button type="submit"
                class="inline-flex w-full justify-center rounded-md border border-transparent bg-napps-primary px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-napps-primary focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                :disabled="loading">
                {{ loading ? 'Saving...' : 'Save Changes' }}
              </button>
              <button type="button" @click="$emit('close')"
                class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-napps-primary focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">
                Cancel
              </button>
            </div>
          </form>

          <div v-else class="space-y-4">
            <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
              <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
                  <dt class="text-sm font-medium text-gray-500">Full name</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ participant.name }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
                  <dt class="text-sm font-medium text-gray-500">Email address</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ participant.email }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
                  <dt class="text-sm font-medium text-gray-500">Phone number</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ participant.phone }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
                  <dt class="text-sm font-medium text-gray-500">State</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ participant.state }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
                  <dt class="text-sm font-medium text-gray-500">School</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ participant.school }}</dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
                  <dt class="text-sm font-medium text-gray-500">Payment status</dt>
                  <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                    <span :class="{
                      'bg-green-100 text-green-800': participant.paymentStatus === 'paid',
                      'bg-yellow-100 text-yellow-800': participant.paymentStatus === 'pending',
                      'bg-red-100 text-red-800': participant.paymentStatus === 'failed'
                    }" class="inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                      {{ participant.paymentStatus }}
                    </span>
                  </dd>
                </div>
                <div class="py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:py-5">
                  <dt class="text-sm font-medium text-gray-500">QR status</dt>
                  <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                    <span :class="{
                      'bg-green-100 text-green-800': participant.qrVerified,
                      'bg-gray-100 text-gray-800': !participant.qrVerified
                    }" class="inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                      {{ participant.qrVerified ? 'Verified' : 'Not Verified' }}
                    </span>
                  </dd>
                </div>
              </dl>
            </div>

            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
              <button type="button" @click="participant.isEditing = true"
                class="inline-flex w-full justify-center rounded-md border border-transparent bg-napps-primary px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-napps-primary focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                Edit Details
              </button>
              <button type="button" @click="$emit('close')"
                class="mt-3 inline-flex w-full justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-napps-primary focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Modal>
</template>

<script>
import { ref } from 'vue'
import { UserIcon } from '@heroicons/vue/24/outline'
import Modal from '@/components/shared/Modal.vue'
import { useNotification } from '@/composables/useNotification'

export default {
  name: 'ParticipantModal',

  components: {
    Modal,
    UserIcon
  },

  props: {
    participant: {
      type: Object,
      required: true
    }
  },

  emits: ['close'],

  setup(props, { emit }) {
    const { showSuccess, showError } = useNotification()
    const loading = ref(false)
    const form = ref({
      name: props.participant.name,
      email: props.participant.email,
      phone: props.participant.phone,
      state: props.participant.state,
      school: props.participant.school
    })

    const states = [
      'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa', 'Benue',
      'Borno', 'Cross River', 'Delta', 'Ebonyi', 'Edo', 'Ekiti', 'Enugu', 'FCT',
      'Gombe', 'Imo', 'Jigawa', 'Kaduna', 'Kano', 'Katsina', 'Kebbi', 'Kogi',
      'Kwara', 'Lagos', 'Nasarawa', 'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo',
      'Plateau', 'Rivers', 'Sokoto', 'Taraba', 'Yobe', 'Zamfara'
    ]

    const updateParticipant = async () => {
      try {
        loading.value = true
        await axios.put(`/api/admin/participants/${props.participant.id}`, form.value)
        showSuccess('Success', 'Participant details updated successfully')
        emit('close')
      } catch (error) {
        if (error.response?.data?.errors) {
          const errors = Object.values(error.response.data.errors).flat()
          showError('Error', errors[0])
        } else {
          showError('Error', 'Failed to update participant details')
        }
      } finally {
        loading.value = false
      }
    }

    return {
      form,
      loading,
      states,
      updateParticipant
    }
  }
}
</script>
