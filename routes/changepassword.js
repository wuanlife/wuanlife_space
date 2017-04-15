var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/*修改用户密码*/
router.get('/', function(req, res, next) {
    req.session.user={
            "user_id": "3",
            "user_name": "午安网",
            "user_email": "wuanwang@163.com"
        };
    if (req.session.user) {
        var agent = ua(req.headers['user-agent']);
        var userid = req.session.user.user_id;
        var page = agent.Mobile ? 'changepasswordM' : 'changepasswordM';
        request("http://104.194.79.57:800/" + "user/get_user_info?user_id="+userid,
            function(error, response, body) {
                if (!error && response.statusCode == 200) {
                    var result = JSON.parse(body);
                    //console.log(result);
                    if (result.ret == 200 && result.data) {
                        res.render(page, {
                            'result': result.data,
                            'title': '修改密码',
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

router.post('/', function(req, res, next) {
    request.post({
        url: "http://104.194.79.57:800/" + 'user/change_pwd',
        formData: {
            user_id: req.session.user.user_id,
            password: req.body.password,
            psw:req.body.psw,
            check_psw:req.body.check_psw
        }
    }, function optionalCallback(err, httpResponse, body) {
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        /*console.log(err);
        console.log(httpResponse);
        console.log(body);*/
        if (err) {
            console.error('alter info failed:', err);
             return res.send({
                ret: 500,
                msg:'服务器异常'
            });
        }
        res.send(JSON.parse(body));
    });
});
module.exports = router;