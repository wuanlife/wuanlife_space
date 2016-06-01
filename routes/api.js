var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');

/* GET users listing. */
router.get('/:method', function(req, res, next) {
	var data = {};
	switch (req.params.method) {
		case 'getindex':
			var pn = req.param('currentpage');
			request(config.server + '?service=Post.GetIndexPost&pn=' + pn, function(error, response, body) {
				if (!error && response.statusCode == 200) {
					console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
					res.header('Content-type', 'application/json');
					res.header('Charset', 'utf8');
					res.send(body);
				}
			})
			break;
		case 'getMyPlanet':
			var pn = req.param('currentpage');
			// var id = req.session.user.id;
			var id = req.param('userId');
			request(config.server + '?service=Post.GetMyGroupPost&id=' + id + '&pn=' + pn, function(error, response, body) {
				if (!error && response.statusCode == 200) {
					console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
					res.header('Content-type', 'application/json');
					res.header('Charset', 'utf8');
					res.send(body);
				}
			})
			break;
		case 'getPlanetAll':
			var pn = req.param('currentpage');
			request(config.server + '?service=Group.Lists&page=' + pn, function(error, response, body) {
				if (!error && response.statusCode == 200) {
					console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
					res.header('Content-type', 'application/json');
					res.header('Charset', 'utf8');
					res.send(body);
				}
			})
			break;
		case 'getPostReplys':
			var pn = req.param('currentpage');
			var post_id = req.param('post_id');
			request(config.server + 'demo/?service=Post.GetPostReply&post_id=' + post_id + '&pn=' + pn,
				function(error, response, body) {
					if (!error && response.statusCode == 200) {
						console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
						res.header('Content-type', 'application/json');
						res.header('Charset', 'utf8');
						res.send(body);
					}
				})
			break;
		case 'getPostEditRight':
			var post_id = req.param('post_id');
			var user_id = req.param('user_id');
			request(config.server + '?service=Post.GetPostBase&post_id=' + post_id + '&id=' + user_id,
				function(error, response, body) {
					if (!error && response.statusCode == 200) {
						console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
						res.header('Content-type', 'application/json');
						res.header('Charset', 'utf8');
						res.send(body);
					}
				})
			break;
		case 'getGroupPlanetShow':
			var pn = req.param('currentpage');
			var groupid = req.param('groupid');
			request(config.server + 'demo/?service=Post.GetGroupPost&group_id=' + groupid + '&pn=' + pn, function(error, response, body) {
				if (!error && response.statusCode == 200) {
					console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
					res.header('Content-type', 'application/json');
					res.header('Charset', 'utf8');
					res.send(body);
				}
			})
			break;
		case 'getPostForEdit':
			var postid = req.param('postid');
			var user_id = req.param('userid');
			request(config.server + '?service=Post.GetPostBase&post_id=' + postid+ '&id=' + user_id,
				function(error, response, body) {
					if (!error && response.statusCode == 200) {
						console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
						res.header('Content-type', 'application/json');
						res.header('Charset', 'utf8');
						res.send(body);
					}
			})
			break;
		case 'joinPlant':
			var groupid = req.param('groupid');
			var userid = req.param('userid');
			request(config.server + 'demo/?service=Group.Join&group_base_id=' + groupid + '&user_id=' + userid, function(error, response, body) {
				if (error) {
					console.log(error);
				}
				if (!error && response.statusCode == 200) {
					console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
					res.header('Content-type', 'application/json');
					res.header('Charset', 'utf8');
					res.send(body);
				}
			})
			break;
		case 'isJoinP':
			var groupid = req.param('groupid');
			var userid = req.param('userid');
			request(config.server + 'demo/?service=Group.GStatus&group_base_id=' + groupid + '&user_id=' + userid, function(error, response, body) {
				if (!error && response.statusCode == 200) {
					console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
					res.header('Content-type', 'application/json');
					res.header('Charset', 'utf8');
					res.send(body);
				}
			})
			break;
		case 'getJoindGroup':
			var user_id = req.param('user_id');
			request(config.server + '?service=Group.GetJoined&user_id=' + user_id, 
				function(error, response, body) {
				if (!error && response.statusCode == 200) {
					console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
					res.header('Content-type', 'application/json');
					res.header('Charset', 'utf8');
					res.send(body);
				}
			})
			break;
		case 'getCreateGroup':
			var user_id = req.param('user_id');
			request(config.server + '?service=Group.GetCreate&user_id=' + user_id, 
				function(error, response, body) {
				if (!error && response.statusCode == 200) {
					console.log(JSON.parse(body));
					console.log("lalala"); // Show the HTML for the Google homepage. 
					res.header('Content-type', 'application/json');
					res.header('Charset', 'utf8');
					res.send(body);
				}
			})
			break;
		case 'isLogin':
			if (req.session.user != null) {
				console.log('Session Login successful!  Server responded with:', req.session.user);
				res.header('Content-type', 'application/json');
				res.header('Charset', 'utf8');
				res.send(req.session.user);
			} else {
				request(config.server + 'demo/?service=Group.UStatus', function(error, response, body) {
					if (!error && response.statusCode == 200) {
						console.log(JSON.parse(body)) // Show the HTML for the Google homepage. 
						var result = JSON.parse(body);
						if (result.ret == 200 && result.data.code == '0') {
							router.post('/', function(req, res, next) {
								request.post({
									url: config.server + 'demo/?service=User.Login',
									formData: {
										Email: req.param('email'),
										password: req.param('password')
									}
								}, function optionalCallback(err, httpResponse, body) {
									if (err) {
										console.error('Login failed:', err);
										res.header('Content-type', 'application/json');
										res.header('Charset', 'utf8');
										res.send({
											err: err
										});
									}
									console.log('Login successful!  Server responded with:', body);
									res.header('Content-type', 'application/json');
									res.header('Charset', 'utf8');
									res.send(JSON.parse(body));
								});

							});
						}
					}

				});
			}
			break;
		default:
			res.send('respond with a resource');
			break;
	}

});

module.exports = router;