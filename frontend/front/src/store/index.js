import { createStore } from 'vuex'

export default createStore({
  state: {
    isDark: false
  },
  getters: {
  },
  mutations: {
    SET_DARK_MODE(state, payload) {
      state.isDark = payload;
    }
  },
  actions: {
  },
  modules: {
  }
})

