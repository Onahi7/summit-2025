import { auth } from '../supabase'

export default {
    namespaced: true,
    state: {
        user: null,
        loading: false,
        error: null
    },
    mutations: {
        SET_USER(state, user) {
            state.user = user
        },
        SET_LOADING(state, loading) {
            state.loading = loading
        },
        SET_ERROR(state, error) {
            state.error = error
        }
    },
    actions: {
        async register({ commit }, { email, password, name }) {
            try {
                commit('SET_LOADING', true)
                commit('SET_ERROR', null)
                
                const { user, error } = await auth.signUp({
                    email,
                    password,
                    name
                })

                if (error) throw error
                
                commit('SET_USER', user)
                return { user }
            } catch (error) {
                commit('SET_ERROR', error.message)
                return { error }
            } finally {
                commit('SET_LOADING', false)
            }
        },

        async login({ commit }, { email, password }) {
            try {
                commit('SET_LOADING', true)
                commit('SET_ERROR', null)

                const { user, error } = await auth.signIn({
                    email,
                    password
                })

                if (error) throw error

                commit('SET_USER', user)
                return { user }
            } catch (error) {
                commit('SET_ERROR', error.message)
                return { error }
            } finally {
                commit('SET_LOADING', false)
            }
        },

        async logout({ commit }) {
            try {
                commit('SET_LOADING', true)
                commit('SET_ERROR', null)

                const { error } = await auth.signOut()
                if (error) throw error

                commit('SET_USER', null)
                return { success: true }
            } catch (error) {
                commit('SET_ERROR', error.message)
                return { error }
            } finally {
                commit('SET_LOADING', false)
            }
        },

        async checkAuth({ commit }) {
            try {
                const { user, error } = await auth.getUser()
                if (error) throw error
                
                commit('SET_USER', user)
                return { user }
            } catch (error) {
                commit('SET_USER', null)
                return { error }
            }
        },

        async resetPassword({ commit }, email) {
            try {
                commit('SET_LOADING', true)
                commit('SET_ERROR', null)

                const { error } = await auth.resetPassword(email)
                if (error) throw error

                return { success: true }
            } catch (error) {
                commit('SET_ERROR', error.message)
                return { error }
            } finally {
                commit('SET_LOADING', false)
            }
        }
    },
    getters: {
        isAuthenticated: state => !!state.user,
        isAdmin: state => state.user?.user_metadata?.role === 'admin',
        isValidator: state => state.user?.user_metadata?.role === 'validator',
        currentUser: state => state.user,
        loading: state => state.loading,
        error: state => state.error
    }
}
