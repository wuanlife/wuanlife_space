var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
var xss = require('xss');

/* GET users listing. */
router.get('/:groupid', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	var page = agent.Mobile ? 'postMobile' : 'post';
	res.render(page, {
		'path':'../',
		'result': req.param("groupid"),
		'title':'发表帖子'
	});
});

router.post('/:groupid', function(req, res, next) {
	request.post({
		url: config.server + 'demo/?service=Group.Posts',
		formData: {
			user_id: req.param('user_id'),
			group_base_id: req.param('groupid'),
			title: xss(req.param('title')),
			text: xss(req.param('text'))
		}
	}, function optionalCallback(err, httpResponse, body) {
		if (err) {
			console.error('Login failed:', err);
			next(err);
		}
		var result = JSON.parse(body);
		console.log('issue success:');
		console.log(JSON.parse(body));
		res.header('Content-type', 'application/json');
		res.header('Charset', 'utf8');
		res.send(JSON.parse(body));
		// if (result.ret == 200 && result.msg == "") {
		// 	res.render('/postDetail/1' );
		// 	//res.render('/postDetail/' + result.data.info.post_base_id);
		// }else{
		// 	next();
		// }
	});

});

module.exports = router;