<template>
  <div aria-live="assertive" class="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6 sm:items-start z-50">
    <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
      <Notification
        v-for="notification in notifications"
        :key="notification.id"
        v-bind="notification"
        @close="removeNotification(notification.id)"
      />
    </div>
  </div>
</template>

<script>
import { useNotification } from '@/composables/useNotification'
import Notification from './Notification.vue'

export default {
  components: {
    Notification
  },

  setup() {
    const { notifications, removeNotification } = useNotification()

    return {
      notifications,
      removeNotification
    }
  }
}
</script>
