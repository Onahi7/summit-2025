<template>
  <div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900">Conference Registration Payment</h3>
      
      <div v-if="receipt" class="mt-5">
        <div class="bg-green-50 border border-green-400 rounded-md p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-green-800">Payment Successful</h3>
              <div class="mt-2 text-sm text-green-700">
                <p>Your payment has been processed successfully. You can now access your QR code.</p>
              </div>
              <div class="mt-4">
                <button
                  @click="printReceipt"
                  class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Print Receipt
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else class="mt-5">
        <div class="space-y-6">
          <div>
            <label class="text-base font-medium text-gray-900">Select Ticket Type</label>
            <p class="text-sm text-gray-500">Choose the type of ticket you want to purchase.</p>
            <div class="mt-4 space-y-4">
              <div class="flex items-center">
                <input
                  id="executive"
                  v-model="selectedTicket"
                  name="ticket-type"
                  type="radio"
                  value="executive"
                  class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                />
                <label for="executive" class="ml-3">
                  <span class="block text-sm font-medium text-gray-700">Executive Ticket</span>
                  <span class="block text-sm text-gray-500">₦50,000</span>
                </label>
              </div>
              <div class="flex items-center">
                <input
                  id="regular"
                  v-model="selectedTicket"
                  name="ticket-type"
                  type="radio"
                  value="regular"
                  class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                />
                <label for="regular" class="ml-3">
                  <span class="block text-sm font-medium text-gray-700">Regular Ticket</span>
                  <span class="block text-sm text-gray-500">₦30,000</span>
                </label>
              </div>
              <div class="flex items-center">
                <input
                  id="observer"
                  v-model="selectedTicket"
                  name="ticket-type"
                  type="radio"
                  value="observer"
                  class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
                />
                <label for="observer" class="ml-3">
                  <span class="block text-sm font-medium text-gray-700">Observer Ticket</span>
                  <span class="block text-sm text-gray-500">₦20,000</span>
                </label>
              </div>
            </div>
          </div>

          <div class="flex justify-end">
            <button
              @click="initiatePayment"
              :disabled="!selectedTicket || loading"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              {{ loading ? 'Processing...' : 'Proceed to Payment' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

export default {
  name: 'Payment',
  setup() {
    const store = useStore();
    const router = useRouter();
    const loading = ref(false);
    const selectedTicket = ref('');
    const receipt = ref(null);

    const loadReceipt = async () => {
      try {
        const data = await store.dispatch('payment/fetchReceipt');
        receipt.value = data.receipt;
      } catch (error) {
        console.error('Error loading receipt:', error);
      }
    };

    const initiatePayment = async () => {
      try {
        loading.value = true;
        const response = await store.dispatch('payment/initiatePayment', {
          ticket_type: selectedTicket.value,
        });

        // Initialize Paystack payment
        const handler = PaystackPop.setup({
          key: process.env.MIX_PAYSTACK_PUBLIC_KEY,
          email: store.state.auth.user.email,
          amount: response.data.amount,
          ref: response.data.reference,
          callback: async (response) => {
            try {
              await store.dispatch('payment/verifyPayment', response.reference);
              await loadReceipt();
              router.push('/qr-code');
            } catch (error) {
              console.error('Payment verification failed:', error);
            }
          },
          onClose: () => {
            console.log('Payment window closed');
          },
        });
        handler.openIframe();
      } catch (error) {
        console.error('Payment initiation failed:', error);
      } finally {
        loading.value = false;
      }
    };

    const printReceipt = () => {
      window.print();
    };

    onMounted(() => {
      loadReceipt();
    });

    return {
      selectedTicket,
      loading,
      receipt,
      initiatePayment,
      printReceipt,
    };
  },
};
</script>
