import axios from 'axios';

export default {
  namespaced: true,

  state: {
    paymentDetails: null,
    receipt: null,
  },

  mutations: {
    SET_PAYMENT_DETAILS(state, details) {
      state.paymentDetails = details;
    },
    SET_RECEIPT(state, receipt) {
      state.receipt = receipt;
    },
  },

  actions: {
    async initiatePayment({ commit }, { ticket_type }) {
      try {
        const { data } = await axios.post('/api/initiate-payment', { ticket_type });
        commit('SET_PAYMENT_DETAILS', data);
        return data;
      } catch (error) {
        throw error.response.data;
      }
    },

    async verifyPayment({ commit }, reference) {
      try {
        const { data } = await axios.post('/api/verify-payment', { reference });
        return data;
      } catch (error) {
        throw error.response.data;
      }
    },

    async fetchReceipt({ commit }) {
      try {
        const { data } = await axios.get('/api/payment-receipt');
        commit('SET_RECEIPT', data.receipt);
        return data;
      } catch (error) {
        throw error.response.data;
      }
    },
  },
};
