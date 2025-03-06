const state = {
    notifications: []
}

const getters = {
    getNotifications: state => state.notifications
}

const actions = {
    addNotification({ commit }, notification) {
        commit('ADD_NOTIFICATION', notification)
        setTimeout(() => {
            commit('REMOVE_NOTIFICATION', notification.id)
        }, notification.timeout || 5000)
    }
}

const mutations = {
    ADD_NOTIFICATION(state, notification) {
        state.notifications.push({
            ...notification,
            id: Date.now()
        })
    },
    REMOVE_NOTIFICATION(state, id) {
        state.notifications = state.notifications.filter(notification => notification.id !== id)
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
