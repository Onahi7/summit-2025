<template>
  <div class="bg-white shadow sm:rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900">Update Profile</h3>
      <div class="mt-2 max-w-xl text-sm text-gray-500">
        <p>Please complete your profile information.</p>
      </div>
      <form @submit.prevent="handleSubmit" class="mt-5 space-y-6">
        <div class="grid grid-cols-6 gap-6">
          <div class="col-span-6 sm:col-span-3">
            <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
            <input
              type="text"
              v-model="form.phone_number"
              id="phone_number"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              required
            />
          </div>

          <div class="col-span-6 sm:col-span-3">
            <label for="state" class="block text-sm font-medium text-gray-700">State</label>
            <select
              id="state"
              v-model="form.state"
              class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              required
            >
              <option value="">Select a state</option>
              <option v-for="state in states" :key="state" :value="state">{{ state }}</option>
            </select>
          </div>

          <div class="col-span-6">
            <label for="napps_chapter" class="block text-sm font-medium text-gray-700">NAPPS Chapter</label>
            <input
              type="text"
              v-model="form.napps_chapter"
              id="napps_chapter"
              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
              required
            />
          </div>

          <div class="col-span-6">
            <label class="block text-sm font-medium text-gray-700">Passport Photograph</label>
            <div class="mt-1 flex items-center">
              <img
                v-if="previewImage || currentPassport"
                :src="previewImage || currentPassport"
                class="h-32 w-32 object-cover rounded-md"
              />
              <div
                v-else
                class="h-32 w-32 rounded-md border-2 border-dashed border-gray-300 flex items-center justify-center"
              >
                <span class="text-gray-400">No photo</span>
              </div>
              <input
                type="file"
                ref="fileInput"
                @change="handleFileChange"
                accept="image/*"
                class="ml-5"
              />
            </div>
          </div>
        </div>

        <div class="flex justify-end">
          <button
            type="submit"
            :disabled="loading"
            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            {{ loading ? 'Saving...' : 'Save' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

export default {
  name: 'Profile',
  setup() {
    const store = useStore();
    const router = useRouter();
    const loading = ref(false);
    const previewImage = ref(null);
    const currentPassport = ref(null);
    const fileInput = ref(null);

    const states = [
      'Nasarawa',
      'Abuja',
      'Plateau',
      'Benue',
      // Add more states as needed
    ];

    const form = ref({
      phone_number: '',
      state: '',
      napps_chapter: '',
      passport_photo: null,
    });

    const handleFileChange = (event) => {
      const file = event.target.files[0];
      if (file) {
        form.value.passport_photo = file;
        previewImage.value = URL.createObjectURL(file);
      }
    };

    const loadProfile = async () => {
      try {
        const profile = await store.dispatch('participant/fetchProfile');
        if (profile) {
          form.value.phone_number = profile.phone_number || '';
          form.value.state = profile.state || '';
          form.value.napps_chapter = profile.napps_chapter || '';
          currentPassport.value = profile.passport_photo || null;
        }
      } catch (error) {
        console.error('Error loading profile:', error);
      }
    };

    const handleSubmit = async () => {
      try {
        loading.value = true;
        await store.dispatch('participant/updateProfile', form.value);
        router.push('/payment');
      } catch (error) {
        console.error('Error updating profile:', error);
        // Handle error (show notification, etc.)
      } finally {
        loading.value = false;
      }
    };

    onMounted(() => {
      loadProfile();
    });

    return {
      form,
      states,
      loading,
      previewImage,
      currentPassport,
      fileInput,
      handleFileChange,
      handleSubmit,
    };
  },
};
</script>
