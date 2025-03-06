<template>
  <div>
    <div class="bg-white shadow sm:rounded-lg mb-6">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Welcome to NAPPS Conference Registration</h3>
        <div class="mt-2 max-w-xl text-sm text-gray-500">
          <p>Complete the following steps to register for the conference.</p>
        </div>
      </div>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <div class="space-y-6">
          <!-- Profile Step -->
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div :class="[
                'h-8 w-8 rounded-full flex items-center justify-center',
                profile ? 'bg-green-100' : 'bg-gray-100'
              ]">
                <svg
                  :class="[
                    'h-5 w-5',
                    profile ? 'text-green-600' : 'text-gray-400'
                  ]"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    v-if="profile"
                    fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                  <path
                    v-else
                    fill-rule="evenodd"
                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <h4 class="text-sm font-medium text-gray-900">Complete Your Profile</h4>
              <p class="mt-1 text-sm text-gray-500">
                Add your contact information and upload your passport photograph.
              </p>
            </div>
            <div class="ml-auto">
              <router-link
                to="/profile"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200"
              >
                {{ profile ? 'Update Profile' : 'Complete Profile' }}
              </router-link>
            </div>
          </div>

          <!-- Payment Step -->
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div :class="[
                'h-8 w-8 rounded-full flex items-center justify-center',
                payment ? 'bg-green-100' : 'bg-gray-100'
              ]">
                <svg
                  :class="[
                    'h-5 w-5',
                    payment ? 'text-green-600' : 'text-gray-400'
                  ]"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    v-if="payment"
                    fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                  <path
                    v-else
                    fill-rule="evenodd"
                    d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <h4 class="text-sm font-medium text-gray-900">Make Payment</h4>
              <p class="mt-1 text-sm text-gray-500">
                Choose your ticket type and complete the payment process.
              </p>
            </div>
            <div class="ml-auto">
              <router-link
                to="/payment"
                :class="[
                  'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md',
                  profile
                    ? 'text-indigo-700 bg-indigo-100 hover:bg-indigo-200'
                    : 'text-gray-400 bg-gray-100 cursor-not-allowed'
                ]"
                :disabled="!profile"
              >
                {{ payment ? 'View Receipt' : 'Make Payment' }}
              </router-link>
            </div>
          </div>

          <!-- QR Code Step -->
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div :class="[
                'h-8 w-8 rounded-full flex items-center justify-center',
                qrCode ? 'bg-green-100' : 'bg-gray-100'
              ]">
                <svg
                  :class="[
                    'h-5 w-5',
                    qrCode ? 'text-green-600' : 'text-gray-400'
                  ]"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    v-if="qrCode"
                    fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd"
                  />
                  <path
                    v-else
                    fill-rule="evenodd"
                    d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>
            <div class="ml-4">
              <h4 class="text-sm font-medium text-gray-900">Get Your QR Code</h4>
              <p class="mt-1 text-sm text-gray-500">
                Download your QR code for conference accreditation and meal validation.
              </p>
            </div>
            <div class="ml-auto">
              <router-link
                to="/qr-code"
                :class="[
                  'inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md',
                  payment
                    ? 'text-indigo-700 bg-indigo-100 hover:bg-indigo-200'
                    : 'text-gray-400 bg-gray-100 cursor-not-allowed'
                ]"
                :disabled="!payment"
              >
                {{ qrCode ? 'View QR Code' : 'Generate QR Code' }}
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useStore } from 'vuex';

export default {
  name: 'Dashboard',
  setup() {
    const store = useStore();
    const profile = ref(false);
    const payment = ref(false);
    const qrCode = ref(false);

    const checkStatus = async () => {
      try {
        const participant = await store.dispatch('participant/fetchProfile');
        profile.value = !!participant;
        
        if (profile.value) {
          const receipt = await store.dispatch('payment/fetchReceipt');
          payment.value = !!receipt;

          if (payment.value) {
            const qrCodeData = await store.dispatch('participant/fetchQrCode');
            qrCode.value = !!qrCodeData;
          }
        }
      } catch (error) {
        console.error('Error checking status:', error);
      }
    };

    onMounted(() => {
      checkStatus();
    });

    return {
      profile,
      payment,
      qrCode,
    };
  },
};
</script>
