import axios from 'axios';

export default {
  namespaced: true,

  state: {
    profile: null,
    qrCode: null,
  },

  mutations: {
    SET_PROFILE(state, profile) {
      state.profile = profile;
    },
    SET_QR_CODE(state, qrCode) {
      state.qrCode = qrCode;
    },
  },

  actions: {
    async fetchProfile({ commit }) {
      try {
        const { data } = await axios.get('/api/profile');
        commit('SET_PROFILE', data);
        return data;
      } catch (error) {
        throw error.response.data;
      }
    },

    async updateProfile({ commit }, profileData) {
      try {
        const formData = new FormData();
        Object.keys(profileData).forEach(key => {
          formData.append(key, profileData[key]);
        });

        const { data } = await axios.post('/api/profile', formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });
        commit('SET_PROFILE', data.participant);
        return data;
      } catch (error) {
        throw error.response.data;
      }
    },

    async fetchQrCode({ commit }) {
      try {
        const { data } = await axios.get('/api/qr-code');
        commit('SET_QR_CODE', data);
        return data;
      } catch (error) {
        throw error.response.data;
      }
    },
  },
};
