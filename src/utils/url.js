export function parseQueryParams (url) {
  const obj = {}
  let keyvalue = []
  let key = ''
  let value = ''
  const paraString = url.substring(url.indexOf('?') + 1, url.length).split('&');
  for (const i in paraString) {
    keyvalue = paraString[i].split('=')
    key = keyvalue[0]
    value = keyvalue[1]
    obj[key] = value
  }
  return obj
}
