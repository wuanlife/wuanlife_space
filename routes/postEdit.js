var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */
router.get('/:postid', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	res.render('postEdit', {'path':'../','result':req.param("postid"),'ag': agent,'title':"编辑帖子"});
});

router.post('/:postid', function(req, res, next) {
	request.post({
		url: config.server + '?service=Post.editPost',
		formData: {
			user_id: req.param('user_id'),
			post_id: req.param('postid'),
			title: req.param('title'),
			text: req.param('text')
		}
	}, function optionalCallback(err, httpResponse, body) {
		if (err) {
			console.error('Edit failed:', err);
			next(err);
		}
		var result = JSON.parse(body);
		console.log('edit success:');
		console.log(JSON.parse(body)); 
		res.header('Content-type', 'application/json');
		res.header('Charset', 'utf8');
		res.send(JSON.parse(body));

	});

});

module.exports = router;


/* GET home page. */
// router.get('/', function (req, res, next) {
//     var agent = ua(req.headers['user-agent']);
// 	request('http://104.194.79.57/?service=Post.GetIndexPost', function(error, response, body) {
// 		if (!error && response.statusCode == 200) {
// 			console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
// 			var data = JSON.parse(body);
// 			if (data.ret == 200) {
//                 res.render('index', { 'result': data.data, 'ag': agent });
// 			}
// 		} else {
// //			res.status(err.status || 500);
// //			res.render('error', {
// //				message: response.statusCode + '/n服务器忙，请稍后再试！',
// //				error: {}
// //			});
// 			next(error);
// 		}
// 	})

// });

// module.exports = router;