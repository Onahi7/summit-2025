<template>
  <div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900">Your QR Code</h3>
      
      <div v-if="loading" class="mt-5 text-center">
        <div class="spinner">Loading...</div>
      </div>

      <div v-else-if="error" class="mt-5">
        <div class="bg-red-50 border border-red-400 rounded-md p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-red-800">Error</h3>
              <div class="mt-2 text-sm text-red-700">
                <p>{{ error }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-else-if="qrCode" class="mt-5">
        <div class="text-center">
          <img
            :src="'data:image/png;base64,' + qrCode.qr_image"
            alt="QR Code"
            class="mx-auto h-64 w-64"
          />
          <p class="mt-2 text-sm text-gray-500">QR Code ID: {{ qrCode.qr_code }}</p>
          <div class="mt-4">
            <button
              @click="downloadQrCode"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Download QR Code
            </button>
          </div>
          <div class="mt-4 text-sm text-gray-500">
            <p>Present this QR code at the conference for:</p>
            <ul class="mt-2 list-disc list-inside">
              <li>Accreditation at the venue</li>
              <li>Morning meal validation</li>
              <li>Evening meal validation</li>
            </ul>
          </div>
        </div>
      </div>

      <div v-else class="mt-5">
        <div class="text-center">
          <p class="text-sm text-gray-500">
            Complete your payment to generate your QR code.
          </p>
          <div class="mt-4">
            <router-link
              to="/payment"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Go to Payment
            </router-link>
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
  name: 'QrCode',
  setup() {
    const store = useStore();
    const loading = ref(false);
    const error = ref(null);
    const qrCode = ref(null);

    const loadQrCode = async () => {
      try {
        loading.value = true;
        error.value = null;
        const data = await store.dispatch('participant/fetchQrCode');
        qrCode.value = data;
      } catch (error) {
        console.error('Error loading QR code:', error);
        error.value = error.message || 'Failed to load QR code';
      } finally {
        loading.value = false;
      }
    };

    const downloadQrCode = () => {
      if (!qrCode.value) return;

      const link = document.createElement('a');
      link.href = 'data:image/png;base64,' + qrCode.value.qr_image;
      link.download = 'napps-conference-qr.png';
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    };

    onMounted(() => {
      loadQrCode();
    });

    return {
      loading,
      error,
      qrCode,
      downloadQrCode,
    };
  },
};
</script>

<style scoped>
.spinner {
  border: 4px solid rgba(0, 0, 0, 0.1);
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border-left-color: #4f46e5;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
