var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

router.get('/', function(req, res, next) {
	if (req.session.user) {
        var agent = ua(req.headers['user-agent']);
        var userid = req.session.user.userID;
        pn = req.query.page || 1;
        var page = agent.Mobile ? 'myCollectionsMobile' : 'myCollections';
        request(config.server + "?service=Post.GetCollectPost&user_id=" + userid+'&pn='+pn,
            function(error, response, body) {
            	if (!error && response.statusCode == 200) {
                    var result = JSON.parse(body);
                    //console.log(result);
                    if (result.ret == 200 && result.data) {
                        res.render(page, {
                            'result': result.data,
                            'title': '我的收藏',
                            'user': req.session.user
                        });
                    } else {
                        res.render('error', {
                            'message': result.msg,
                            error: {
                                'status': result.ret,
                                'stack': ''
                            }
                        });
                    }
                } else {
                    console.error('user failed:', error);
                    next(error);
                }
        });
	} else {
        res.redirect('/login');
    }
});

module.exports = router;