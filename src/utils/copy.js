const copy = function (source, keys = []) {
  if (!source) {
    return source
  }
  const d = Object.create(null)
  keys.forEach(k => {
    d[k] = source[k]
  })
  return d
}

export {
  copy
}
