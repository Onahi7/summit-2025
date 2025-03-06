import { ref, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import Echo from 'laravel-echo'

export function useNotifications() {
    const notifications = ref([])
    const unreadCount = ref(0)
    const echo = window.Echo

    const fetchNotifications = async () => {
        try {
            const response = await axios.get('/api/notifications')
            notifications.value = response.data.data
            await fetchUnreadCount()
        } catch (error) {
            console.error('Failed to fetch notifications:', error)
        }
    }

    const fetchUnreadCount = async () => {
        try {
            const response = await axios.get('/api/notifications/unread')
            unreadCount.value = response.data.count
        } catch (error) {
            console.error('Failed to fetch unread count:', error)
        }
    }

    const markAsRead = async (notificationId = null) => {
        try {
            const payload = notificationId ? { notification_id: notificationId } : {}
            await axios.post('/api/notifications/mark-as-read', payload)
            await fetchUnreadCount()
            
            if (notificationId) {
                const notification = notifications.value.find(n => n.id === notificationId)
                if (notification) {
                    notification.read_at = new Date().toISOString()
                }
            } else {
                notifications.value.forEach(notification => {
                    if (!notification.read_at) {
                        notification.read_at = new Date().toISOString()
                    }
                })
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error)
        }
    }

    const deleteNotification = async (notificationId) => {
        try {
            await axios.delete(`/api/notifications/${notificationId}`)
            notifications.value = notifications.value.filter(n => n.id !== notificationId)
            await fetchUnreadCount()
        } catch (error) {
            console.error('Failed to delete notification:', error)
        }
    }

    const listenToNotifications = () => {
        // Listen to private channel for user-specific notifications
        echo.private(`App.Models.User.${window.userId}`)
            .listen('.notification.created', (e) => {
                notifications.value.unshift(e.notification)
                unreadCount.value++
            })

        // Listen to private channel for role-specific notifications
        echo.private(`role.${window.userRole}`)
            .listen('.notification.created', (e) => {
                notifications.value.unshift(e.notification)
                unreadCount.value++
            })

        // Listen to public channel for all notifications
        echo.channel('notifications')
            .listen('.notification.created', (e) => {
                notifications.value.unshift(e.notification)
                unreadCount.value++
            })
    }

    onMounted(() => {
        fetchNotifications()
        listenToNotifications()
    })

    onUnmounted(() => {
        echo.leave(`App.Models.User.${window.userId}`)
        echo.leave(`role.${window.userRole}`)
        echo.leave('notifications')
    })

    return {
        notifications,
        unreadCount,
        fetchNotifications,
        markAsRead,
        deleteNotification
    }
}
