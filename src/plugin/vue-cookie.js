var Cookie = require('tiny-cookie')

export default {
  install: function (Vue) {
    Vue.prototype.$cookie = this
    Vue.cookie = this
  },
  set: function (name, value, daysOrOptions) {
    let opts = daysOrOptions
    if (Number.isInteger(daysOrOptions)) {
      opts = { expires: daysOrOptions }
    }
    return Cookie.set(name, value, opts)
  },
  get: function (name) {
    return Cookie.get(name)
  },
  delete: function (name, options) {
    var opts = {expires: -1}
    if (options !== undefined) {
      opts = {
        ...options,
        ...opts
      }
    }
    this.set(name, '', opts)
  }
}
