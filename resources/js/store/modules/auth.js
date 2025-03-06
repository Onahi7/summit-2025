import axios from 'axios'

// Parse user data from localStorage if available
const savedUser = localStorage.getItem('user');
const initialUser = savedUser ? JSON.parse(savedUser) : null;

const state = {
    token: localStorage.getItem('token') || null,
    user: initialUser,
    loading: false,
    error: null
}

const getters = {
    isAuthenticated: state => !!state.token,
    isAdmin: state => state.user?.role === 'admin',
    isValidator: state => state.user?.role === 'validator',
    isParticipant: state => state.user?.role === 'participant',
    getUser: state => state.user,
    getError: state => state.error,
    isLoading: state => state.loading
}

const actions = {
    async login({ commit }, credentials) {
        try {
            commit('SET_LOADING', true);
            commit('SET_ERROR', null);
            const response = await axios.post('/auth/login', credentials);
            const { token, user } = response.data;
            localStorage.setItem('token', token);
            localStorage.setItem('user', JSON.stringify(user));
            commit('SET_TOKEN', token);
            commit('SET_USER', user);
            return response;
        } catch (error) {
            const message = error.response?.data?.message || 'Login failed';
            commit('SET_ERROR', message);
            throw error;
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async register({ commit }, userData) {
        try {
            commit('SET_LOADING', true);
            commit('SET_ERROR', null);
            const response = await axios.post('/auth/register', userData);
            const { token, user } = response.data;
            localStorage.setItem('token', token);
            localStorage.setItem('user', JSON.stringify(user));
            commit('SET_TOKEN', token);
            commit('SET_USER', user);
            return response;
        } catch (error) {
            const message = error.response?.data?.message || 'Registration failed';
            commit('SET_ERROR', message);
            throw error;
        } finally {
            commit('SET_LOADING', false);
        }
    },

    async logout({ commit, state }) {
        try {
            commit('SET_LOADING', true);
            if (state.token) {
                await axios.post('/auth/logout');
            }
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            commit('SET_TOKEN', null);
            commit('SET_USER', null);
            commit('SET_LOADING', false);
        }
    },

    async fetchUser({ commit }) {
        try {
            commit('SET_LOADING', true);
            const response = await axios.get('/user');
            const user = response.data.user;
            localStorage.setItem('user', JSON.stringify(user));
            commit('SET_USER', user);
            return response;
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to fetch user';
            commit('SET_ERROR', message);
            throw error;
        } finally {
            commit('SET_LOADING', false);
        }
    }
}

const mutations = {
    SET_TOKEN(state, token) {
        state.token = token;
    },
    SET_USER(state, user) {
        state.user = user;
    },
    SET_LOADING(state, loading) {
        state.loading = loading;
    },
    SET_ERROR(state, error) {
        state.error = error;
    }
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
