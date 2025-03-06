<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
      </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <!-- Overview Stats -->
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Participants</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.totalParticipants }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Validated Participants</dt>
                  <dd class="text-lg font-medium text-gray-900">{{ stats.validatedParticipants }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="mt-8">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <button @click="showCreateNotification = true" 
            class="relative block w-full rounded-lg p-6 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 bg-white shadow">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="mt-2 block text-sm font-semibold text-gray-900">Create Notification</span>
          </button>

          <button @click="showUploadResource = true"
            class="relative block w-full rounded-lg p-6 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 bg-white shadow">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            <span class="mt-2 block text-sm font-semibold text-gray-900">Upload Resource</span>
          </button>

          <button @click="showCreateValidator = true"
            class="relative block w-full rounded-lg p-6 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 bg-white shadow">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            <span class="mt-2 block text-sm font-semibold text-gray-900">Create Validator</span>
          </button>
        </div>
      </div>

      <!-- Participant List -->
      <div class="mt-8">
        <div class="sm:flex sm:items-center">
          <div class="sm:flex-auto">
            <h2 class="text-xl font-semibold text-gray-900">Participants</h2>
            <p class="mt-2 text-sm text-gray-700">A list of all participants including their name, email and status.</p>
          </div>
        </div>
        <div class="mt-8 flex flex-col">
          <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
              <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                  <thead class="bg-gray-50">
                    <tr>
                      <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
                      <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                      <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                      <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Actions</span>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="participant in participants" :key="participant.id">
                      <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                        {{ participant.name }}
                      </td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ participant.email }}</td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        <span :class="[
                          participant.validated ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800',
                          'inline-flex rounded-full px-2 text-xs font-semibold leading-5'
                        ]">
                          {{ participant.validated ? 'Validated' : 'Pending' }}
                        </span>
                      </td>
                      <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <button @click="viewParticipant(participant)" class="text-indigo-600 hover:text-indigo-900">
                          View<span class="sr-only">, {{ participant.name }}</span>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <!-- Modals -->
    <CreateNotificationModal v-if="showCreateNotification" @close="showCreateNotification = false" />
    <UploadResourceModal v-if="showUploadResource" @close="showUploadResource = false" />
    <CreateValidatorModal v-if="showCreateValidator" @close="showCreateValidator = false" />
    <ParticipantModal v-if="selectedParticipant" :participant="selectedParticipant" @close="selectedParticipant = null" />
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import CreateNotificationModal from './modals/CreateNotificationModal.vue'
import UploadResourceModal from './modals/UploadResourceModal.vue'
import CreateValidatorModal from './modals/CreateValidatorModal.vue'
import ParticipantModal from './modals/ParticipantModal.vue'

export default {
  name: 'AdminDashboard',

  components: {
    CreateNotificationModal,
    UploadResourceModal,
    CreateValidatorModal,
    ParticipantModal
  },

  setup() {
    const showCreateNotification = ref(false)
    const showUploadResource = ref(false)
    const showCreateValidator = ref(false)
    const selectedParticipant = ref(null)
    
    const stats = ref({
      totalParticipants: 0,
      validatedParticipants: 0
    })

    const participants = ref([])

    const fetchStats = async () => {
      try {
        const response = await fetch('/api/admin/stats')
        const data = await response.json()
        stats.value = data
      } catch (error) {
        console.error('Error fetching stats:', error)
      }
    }

    const fetchParticipants = async () => {
      try {
        const response = await fetch('/api/admin/participants')
        const data = await response.json()
        participants.value = data
      } catch (error) {
        console.error('Error fetching participants:', error)
      }
    }

    const viewParticipant = (participant) => {
      selectedParticipant.value = participant
    }

    onMounted(() => {
      fetchStats()
      fetchParticipants()
    })

    return {
      showCreateNotification,
      showUploadResource,
      showCreateValidator,
      selectedParticipant,
      stats,
      participants,
      viewParticipant
    }
  }
}
</script>
