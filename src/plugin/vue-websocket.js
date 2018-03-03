class WuanWebsocket {
  constructor (url) {
    this._url = url
    this._user = {}
    this._ws = new Websocket(url)
    this._mesQueue = {}
    this._channelMsgQueue = {}
    this.connection()
  }
  connection() {
    this._ws.addEventListener('open', function (event) {
      console.log('connection opened !')
    })
    this._ws.addEventListener('message', function (event) {
      processMsg(event)
    })
    this._ws.addEventListener('error', function (event) {
      processError(event)
    })
    this._ws.addEventListener('close', function (event) {
      console.log('connection closed !')
    })
  }
  /**
   * 监听某种type的消息
   * @param  {String} msgType 消息类型
   * @param  {Function} callback 回调函数
   */
  onByType (msgType, callback) {
    if(!this._mesQueue[msgType])
    this._mesQueue[msgType] = [];
    this._mesQueue[msgType].push(callback);
  }
  /**
   * 触发某种type的消息
   * @param  {String} msgType 消息类型
   * @param  {Array} arg 回调函数的参数数组
   */
  emitByType (msgType,...arg) {
    let len = this._mesQueue[msgType].length
    if (len != 0) {
    	this._mesQueue[msgType].forEach(function (item) {
    		item(...arg)
    	})
    }
  }
  /**
   * 监听某个channel的消息
   * @param  {String} channel 消息类型
   * @param  {Function} callback 回调函数
   */
  onByChannel (channel, callback) {
    if(!this._channelMsgQueue[channel])
    this._channelMsgQueue[channel] = [];
    this._channelMsgQueue[channel].push(callback);
  }
  /**
   * 触发某个channel的消息
   * @param  {String} channel 消息类型
   * @param  {Array} arg 回调函数的参数数组
   */
  emitByChannel (channel,...arg) {
    let len = this._channelMsgQueue[channel].length
    if (len != 0) {
      this._channelMsgQueue[channel].forEach(function (item) {
        item(...arg)
      })
    }
  }
  /**
   * @param  {Object} message 需要send的message对象，自动添加uuid和source等
   */
  send (message) {
    this._ws.send(JSON.stringify(message))
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
  /**
   * 处理服务器响应的消息
   */
  processMsg (e) {
    let data = JSON.parse(e.data)
    switch (data.type){
    	case 'chat-subscribe-ok':
    	//do something here ...
    		break;
    	case 'chat-send-ok':
      //do something here ...
        break;
      case 'chat-receive':
      //do something here ...
        break;
    	default:
    		break;
    }
  }
  /**
   * 处理错误消息
   */
  processError (e) {
    let data = JSON.parse(e.data)
    //do something here ...
  }
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
