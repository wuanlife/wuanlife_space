const DEFAULT_OPTIONS = {
  url: 'ws://localhost:3001'
}

export default {
  install (Vue, options) {
    options = {
      ...DEFAULT_OPTIONS,
      options
    }
    const ws = new WebSocket(`${options.url}`)

    Vue.prototype.$ws = ws

    // TODO: 自己在Websocket上封装一层，自动添加uuid和source等
    const addListeners = function () {
    }

    const removeListeners = function () {
    }

    Vue.mixin({
      beforeCreate: () => {},

      beforeDestroy: () => {}
    })
  }

}
