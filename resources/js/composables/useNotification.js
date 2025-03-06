import { ref } from 'vue'

const notifications = ref([])

export function useNotification() {
    const addNotification = ({ type, title, message, duration = 5000 }) => {
        const id = Date.now()
        notifications.value.push({
            id,
            type,
            title,
            message,
            duration
        })

        if (duration > 0) {
            setTimeout(() => {
                removeNotification(id)
            }, duration)
        }

        return id
    }

    const removeNotification = (id) => {
        const index = notifications.value.findIndex(n => n.id === id)
        if (index > -1) {
            notifications.value.splice(index, 1)
        }
    }

    const showSuccess = (title, message, duration) => {
        return addNotification({ type: 'success', title, message, duration })
    }

    const showError = (title, message, duration) => {
        return addNotification({ type: 'error', title, message, duration })
    }

    return {
        notifications,
        showSuccess,
        showError,
        removeNotification
    }
}
