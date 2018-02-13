const websocket = {
  state: [],
  mutations: {
    RECEIVE_MESSAGE: (state, message) => {
      state.unshift(message)
    },
    CLEAR_MESSAGE: state => {
      state = []
    }
  },
  actions: {
  }
}

export default websocket
