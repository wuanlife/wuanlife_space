var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');


router.get('/:groupID', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    var userid = (req.session.user) ? req.session.user.userID : null;
    if (agent.Mobile) {
        res.render('error', {
            'message': result.msg,
            error: {
                'status': 404,
                'stack': ''
            }
        });
    } else {
        request(config.server + '?service=Group.UserManage&group_id=' + req.params.groupID + '&user_id=' + userid,
            function(error, response, body) {
                if (!error && response.statusCode == 200) {
                    try {
                        var data = JSON.parse(body);
                        //console.log(data.data);
                        if (data.ret == 200) {
                            var page = 'userManage';
                            res.render(page, {
                                'result': data.data,
                                'groupid':req.params.groupID,
                                'title': '星球成员管理',
                                'user': req.session.user
                            });
                        } else {
                            throw new Error(data.msg);
                        }
                    } catch (e) {
                        next(e);
                    }
                } else {
                    next(error);
                }
            });
    }
});


module.exports = router;