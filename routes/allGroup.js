var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */
router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']),
	pn = req.query.page || 1;
	var page = agent.Mobile ? 'allGroupM' : 'allGroup';
	request(config.server +"group/lists", function(error, response, body) {
		if (!error) {
			var result = JSON.parse(body);
			//console.log(result);
			if (result.ret == 200) {
				res.render(page, {
					'data': result.data,
					'title':'全部星球',
					'user':req.session.user
				});
			} else{
				res.render('error', {
					'message': result.msg,
					error: {
						'status': result.ret,
						'stack': ''
					}
				});
			}
		} else {
			console.error('allGroup failed:', error);
			next(error);
		}
	});
});
router.get('/more',function(req,res,next){
	var pn=req.query.pagecount;
	request(config.server + 'group/lists?pn=' + pn,function(error, response, body){
		if(!error){
			var result = JSON.parse(body);
			if(result.ret==200){
				res.send(result);
			}else{
				res.render('error', {
					'message': result.msg,
					error: {
						'status': result.ret,
						'stack': ''
					}
				});
			}
		}else{
			console.error('allGroup failed:', error);
			next(error);
		}
	})
});
module.exports = router;