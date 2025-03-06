<template>
  <div class="min-h-screen bg-gray-100 py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
          <h2 class="text-2xl font-bold mb-6">Update Profile</h2>
          
          <form @submit.prevent="updateProfile" class="space-y-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <div>
                <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" v-model="form.phone_number" required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">State</label>
                <select v-model="form.state" required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                  <option value="">Select State</option>
                  <option value="Nasarawa">Nasarawa</option>
                  <!-- Add other states -->
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">NAPPS Chapter</label>
                <input type="text" v-model="form.napps_chapter" required
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Passport Photograph</label>
                <input type="file" @change="handleFileUpload" accept="image/*"
                  class="mt-1 block w-full shadow-sm sm:text-sm" />
              </div>
            </div>

            <div class="flex justify-end">
              <button type="submit"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Update Profile
              </button>
            </div>
          </form>

          <div v-if="profileComplete" class="mt-8">
            <h3 class="text-lg font-medium text-gray-900">Select Ticket Type</h3>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
              <div v-for="ticket in tickets" :key="ticket.type"
                class="relative rounded-lg border border-gray-300 bg-white px-6 py-4 shadow-sm hover:border-gray-400 cursor-pointer"
                :class="{ 'border-blue-500': selectedTicket === ticket.type }"
                @click="selectTicket(ticket.type)">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ ticket.name }}</p>
                    <p class="text-sm text-gray-500">₦{{ ticket.price.toLocaleString() }}</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-6 flex justify-end">
              <button @click="initiatePayment"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Proceed to Payment
              </button>
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
import axios from 'axios'

export default {
  setup() {
    const router = useRouter()
    const form = ref({
      phone_number: '',
      state: '',
      napps_chapter: '',
      passport_photo: null
    })

    const profileComplete = ref(false)
    const selectedTicket = ref(null)

    const tickets = [
      { type: 'executive', name: 'Executive Ticket', price: 50000 },
      { type: 'regular', name: 'Regular Ticket', price: 30000 },
      { type: 'observer', name: 'Observer Ticket', price: 20000 }
    ]

    const handleFileUpload = (event) => {
      form.value.passport_photo = event.target.files[0]
    }

    const updateProfile = async () => {
      try {
        const formData = new FormData()
        Object.keys(form.value).forEach(key => {
          formData.append(key, form.value[key])
        })

        await axios.post('/api/profile', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        })

        profileComplete.value = true
      } catch (error) {
        console.error('Profile update failed:', error)
      }
    }

    const selectTicket = (type) => {
      selectedTicket.value = type
    }

    const initiatePayment = async () => {
      if (!selectedTicket.value) return

      try {
        const response = await axios.post('/api/initiate-payment', {
          ticket_type: selectedTicket.value
        }, {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        })

        // Redirect to Paystack payment page
        window.location.href = response.data.data.authorization_url
      } catch (error) {
        console.error('Payment initiation failed:', error)
      }
    }

    onMounted(async () => {
      try {
        const response = await axios.get('/api/profile', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        })
        
        if (response.data) {
          form.value = response.data
          profileComplete.value = true
        }
      } catch (error) {
        console.error('Failed to fetch profile:', error)
      }
    })

    return {
      form,
      profileComplete,
      selectedTicket,
      tickets,
      handleFileUpload,
      updateProfile,
      selectTicket,
      initiatePayment
    }
  }
}
</script>
