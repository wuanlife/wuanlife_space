var request = require('request');
request('http://104.194.79.57/?service=Post.GetIndexPost', function (error, response, body) {
  if (!error && response.statusCode == 200) {
    console.log(body) // Show the HTML for the Google homepage. 
  }
});

request.post({url:'http://104.194.79.57/?service=User.Login', formData: {Email:'123@qq.com',password:131331}}, function optionalCallback(err, httpResponse, body) {
  if (err) {
    return console.error('Login failed:', err);
  }
  console.log('Login successful!  Server responded with:', body);
});
