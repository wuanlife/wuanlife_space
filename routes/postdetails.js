var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');
var Promise = require('bluebird');
Promise.promisifyAll(request);

/* GET users listing. */

router.get('/:userid', function(req, res, next) {
	/*
	var agent = ua(req.headers['user-agent']),
        pn = req.query.page || 1;
	request(config.server + '?service=Post.GetMyGroupPost&id=' + req.param("userid") + '&pn=' + pn,
		function(error, response, body) {
			if (!error && response.statusCode == 200) {
				//console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
				var data = JSON.parse(body);
				if (data.ret == 200 && data.msg == '') {
					var page = agent.Mobile ? 'uGroupMobile' : 'uGroup';
					res.render(page, {
						'path': '../',
						'result': data.data,
						'title': data.data.user_name + '的星球',
						'userid': req.param("userid"),
                        'ownerName':data.data.user_name,
						'user': req.session.user
					});
				} else {
					res.render('error', {
						'message': data.msg,
						error: {
							'status': data.ret,
							'stack': ''
						}
					});
				}
			} else {
				next(error);
			}
		})

	*/
	var agent = ua(req.headers['user-agent']);
	try{
		var page = agent.Mobile ? 'postMobile' : 'post';
		res.render(page, {
			'result': null,
			'user': null
		});
	} catch(e){
		next(e);
	}
	
});
module.exports = router;