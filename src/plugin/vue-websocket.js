class WuanWebsocket {
  constructor (url) {
    this._url = url
    this._user = {}
    this._msgTypeQueue = {}
    this._channelQueue = {}
    this._ws = new Websocket(url)
    this._ws.addEventListener('open', function (event) {
      console.log('ws link')
    })
  }
  /**
   * 监听某种type的消息
   * @param  {String} msgType 消息类型
   * @param  {Function} callback 回调函数
   */
  onByType (msgType, callback) {
    if (!this._msgTypeQueue[msgType])
    this._msgTypeQueue[msgType] = []
    this._msgTypeQueue[msgType].push(callback)
  }

  /**
   * 监听某个channel的消息
   * @param  {String} channel 消息类型
   * @param  {Function} callback 回调函数
   */
  onByChannel (channel, callback) {
    if (!this._channelQueue[msgType])
    this._channelQueue[msgType] = []
    this._channelQueue[msgType].push(callback)
  }

  /**
   * @param  {Object} message 需要send的message对象，自动添加uuid和source等
   */
  send (message) {
    message = {
      ...message,
      uuid: genUuid(),
      source: wuanlife
    }
    this._ws.send(JOSN.stringify(message))
  }
  /**
   * 重置websocket,当登录状态变化时调用reset并传入新user
   * @param  {} newUser
   */
  reset (newUser) {
    this._user = newUser
  }

  /**
   * 断开websocket连接
   */
  terminate () {
    this._ws.close()
  }
}

function genUuid () {
	return new Date().getTime()+""+Math.floor(Math.random()*899+100)
}

const DEFAULT_OPTIONS = {
  url: 'ws://localhost:3001'
}

export default {
  install (Vue, options) {
    options = {
      ...DEFAULT_OPTIONS,
      options
    }
    const ws = new WuanWebsocket(`${options.url}`)

    Vue.prototype.$ws = ws

    Vue.mixin({
      beforeCreate: () => {},

      beforeDestroy: () => {}
    })
  }

}
