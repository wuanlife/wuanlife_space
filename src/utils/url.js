export function parseQueryParams(url) {
  var obj = {};
  var keyvalue = [];
  var key = "",
      value = "";
  var paraString = url.substring(url.indexOf("?") + 1, url.length).split("&");
  for (var i in paraString) {
      keyvalue = paraString[i].split("=");
      key = keyvalue[0];
      value = keyvalue[1];
      obj[key] = value;
  }
  return obj;   
}
