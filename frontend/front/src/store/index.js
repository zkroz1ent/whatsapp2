import { createStore } from 'vuex'

export default createStore({
  state: {
    isDark: false,
    userId:"",
    tokken:""
  },
  getters: {
  },
  mutations: {
    SET_DARK_MODE(state, payload) {
      state.isDark = payload;
    },
    SET_userId(state, userId) {
      state.userId = userId;
    },
  
  },
  actions: {
  },
  modules: {
  }
})

