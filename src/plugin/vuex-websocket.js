export default function createWebSocketPlugin () {
  // 保存当前用户内容，方便监听变化。
  let nowUser = null
  return store => {
    nowUser = store.getters.user
    console.log(nowUser)
    store.subscribe((mutation, state) => {

    })
    // socket.on('data', data => {
    //   store.commit('receiveData', data)
    // })
    // store.subscribe(mutation => {
    //   if (mutation.type === 'UPDATE_DATA') {
    //     socket.emit('update', mutation.payload)
    //   }
    // })
  }
}
