<template>
  <Modal :show="true" @close="$emit('close')">
    <div class="sm:flex sm:items-start">
      <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-napps-primary sm:mx-0 sm:h-10 sm:w-10">
        <DocumentIcon class="h-6 w-6 text-white" aria-hidden="true" />
      </div>
      <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Upload Resource</h3>
        <div class="mt-2">
          <form @submit.prevent="uploadResource" class="space-y-4">
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
              <label for="type" class="block text-sm font-medium text-gray-700">Resource Type</label>
              <select id="type" v-model="form.type"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-napps-primary focus:ring-napps-primary sm:text-sm">
                <option value="schedule">Conference Schedule</option>
                <option value="presentation">Presentation Material</option>
                <option value="document">Important Document</option>
                <option value="guide">Guidelines</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">File</label>
              <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
                @dragover.prevent="dragover = true" @dragleave.prevent="dragover = false"
                @drop.prevent="handleFileDrop"
                :class="{ 'border-napps-primary bg-blue-50': dragover }">
                <div class="space-y-1 text-center">
                  <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path
                      d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <div class="flex text-sm text-gray-600">
                    <label for="file-upload"
                      class="relative cursor-pointer rounded-md font-medium text-napps-primary hover:text-napps-accent focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-napps-primary">
                      <span>Upload a file</span>
                      <input id="file-upload" type="file" class="sr-only" @change="handleFileSelect" />
                    </label>
                    <p class="pl-1">or drag and drop</p>
                  </div>
                  <p class="text-xs text-gray-500">
                    PDF, DOC, DOCX, PPT, PPTX up to 10MB
                  </p>
                  <p v-if="form.file" class="text-sm text-napps-primary">
                    Selected: {{ form.file.name }}
                  </p>
                </div>
              </div>
            </div>

            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
              <button type="submit"
                class="inline-flex w-full justify-center rounded-md border border-transparent bg-napps-primary px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-napps-primary focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm"
                :disabled="loading || !form.file">
                {{ loading ? 'Uploading...' : 'Upload Resource' }}
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
import { DocumentIcon } from '@heroicons/vue/24/outline'
import Modal from '@/components/shared/Modal.vue'
import { useNotification } from '@/composables/useNotification'

export default {
  name: 'UploadResourceModal',

  components: {
    Modal,
    DocumentIcon
  },

  emits: ['close'],

  setup(props, { emit }) {
    const { showSuccess, showError } = useNotification()
    const loading = ref(false)
    const dragover = ref(false)
    const form = ref({
      title: '',
      description: '',
      type: 'document',
      file: null
    })

    const handleFileSelect = (event) => {
      const file = event.target.files[0]
      validateAndSetFile(file)
    }

    const handleFileDrop = (event) => {
      dragover.value = false
      const file = event.dataTransfer.files[0]
      validateAndSetFile(file)
    }

    const validateAndSetFile = (file) => {
      const allowedTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation'
      ]

      if (!allowedTypes.includes(file.type)) {
        showError('Error', 'Invalid file type. Please upload PDF, DOC, DOCX, PPT, or PPTX files.')
        return
      }

      if (file.size > 10 * 1024 * 1024) {
        showError('Error', 'File size should not exceed 10MB')
        return
      }

      form.value.file = file
    }

    const uploadResource = async () => {
      try {
        loading.value = true
        const formData = new FormData()
        formData.append('title', form.value.title)
        formData.append('description', form.value.description)
        formData.append('type', form.value.type)
        formData.append('file', form.value.file)

        await axios.post('/api/admin/resources', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

        showSuccess('Success', 'Resource uploaded successfully')
        emit('close')
      } catch (error) {
        showError('Error', 'Failed to upload resource')
      } finally {
        loading.value = false
      }
    }

    return {
      form,
      loading,
      dragover,
      handleFileSelect,
      handleFileDrop,
      uploadResource
    }
  }
}
</script>
