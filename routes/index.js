var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../config/config');

/* GET home page. */

router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']);
	var userID = (req.session.user) ? req.session.user.user_id : null;
	try{
		var page = agent.Mobile ? 'indexMobile' : 'index';
		res.render(page, {
			'result': null,
			'user': req.session.user
		});
	} catch(e){
		next(e);
	}
	

});

router.post('/', function(req, res, next) {
    request(req.session.user ? 
        config.server + "post/get_index_post?user_id=" + req.session.user.user_id + "&pn=" + req.body.pn : 
        config.server + "post/get_index_post?&pn=" + req.body.pn,
        function(error, httpResponse, body) {
            if (!error && httpResponse.statusCode == 200) {
                console.log('get_index_post success!');
                return res.send(JSON.parse(body));
            } else {
                console.log('get_index_post error!  Server responded with:', body);
                try {
                    console.error('get_index_post failed:', error.toString());
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                } catch(error) {
                    console.error('catch get_index_post exception:', error.toString());
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
            }
        }
    )

});
module.exports = router;