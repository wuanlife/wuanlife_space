var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../config/config');

/* GET home page. */
/*
router.get('/', function(req, res, next) {
	var agent = ua(req.headers['user-agent']),
		pn = req.query.page || 1,
		userID = (req.session.user) ? req.session.user.userID : null;
	request(config.server + '?service=Post.GetIndexPost&pn=' + pn + '&user_id=' + userID, function(error, response, body) {
		if (!error && response.statusCode == 200) {
			//console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
			try{
				var data = JSON.parse(body);
				if (data.ret == 200) {
					var page = agent.Mobile ? 'indexMobile' : 'index';
					res.render(page, {
						'result': data.data,
						'user':req.session.user
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
			} catch(e){
				next(e);
			}
		} else {
			next(error);
		}
	})

});
*/

router.get('/', function(req, res, next) {
	console.log("session: ",req.session.user);
	var agent = ua(req.headers['user-agent']);
	var userID = (req.session.user) ? req.session.user.user_id : null;
	try{
		var page = agent.Mobile ? 'indexMobile' : 'index';
		res.render(page, {
			'result': null,
			'user': null
		});
	} catch(e){
		next(e);
	}
	

});

router.post('/', function(req, res, next) {
    request.get({
        url: config.server + 'post/get_index_post',
        formData: {
            user_id: req.session.user.user_id,
            pn: req.body.pn,
        }
    }, function optionalCallback(err, httpResponse, body) {
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        if (!err && httpResponse.statusCode == 200) {
            //console.log('CreatePlanet successful!  Server responded with:', body);
            return res.send(JSON.parse(body));
        } else {
            try {
                console.error('get index posts failed:', err.toString());
                res.send({
                    ret: 500,
                    msg:'服务器异常'
                });
            } catch(err) {
                console.error('get index posts exception:', err.toString());
            }

        }

    });
});
module.exports = router;