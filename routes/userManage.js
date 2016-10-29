var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');


router.get('/:groupID', function(req, res, next) {
    var agent=ua(req.headers['user-agent']);
    var userid=(req.session.user)?req.session.user.userID:null;
    request(config.server+'?service=Group.UserManage&group_id='+req.param("groupID")+'&user_id='+userid,
        function(error, response, body) {
            if (!error && response.statusCode == 200) {
                //console.log(JSON.parse(body)); // Show the HTML for the Google homepage. 
                var data = JSON.parse(body);
                if (data.ret == 200 && data.msg == '') {
                    var page = agent.Mobile ? 'error' : 'userManage';
                    if(!agent.Mobile){
                      res.render(page, {
                        'path': '../',
                        'result': data,
                        'title': '星球成员管理',
                        'userid': (req.session.user)?req.session.user.userID:null,
                        'ownerName':data.data.user_name,
                        'user': req.session.user
                       });
                    }
                    else{
                    	res.render('error', {
                        'message': data.msg,
                        error: {
                            'status': data.ret,
                            'stack': ''
                        }
                    });
                    }
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
});


module.exports = router;