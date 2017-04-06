var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* GET users listing. */
router.get('/', function(req, res, next) {
    req.session.user={
            "user_id": "174",
            "user_name": "午安网",
            "user_email": "wuanwang@163.com"
        };
    if (req.session.user) {
        var agent = ua(req.headers['user-agent']);
        var userid = req.session.user.user_id;
        var page = agent.Mobile ? 'personalInforMobile' : 'personalInfor';
        request(config.server + "user/get_user_info?user_id="+userid,
            function(error, response, body) {
                if (!error && response.statusCode == 200) {
                    var result = JSON.parse(body);
                    //console.log(result);
                    if (result.ret == 200 && result.data) {
                        res.render(page, {
                            'result': result.data,
                            'title': '个人中心',
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
//提交修改个人信息的数据
router.get('/sub', function(req, res, next) {
    req.session.user={
            "user_id": "174",
            "user_name": "三名哈哈呵呵",
            "user_email": "2660591904@qq.com"
        };
    if (req.session.user) {
        var userid = req.session.user.user_id;
        var username=encodeURIComponent(req.query.user_name);
        var profile_picture=req.query.profile_picture;
        var sex=req.query.sex;
        var year=req.query.year;
        var month=req.query.month;
        var day=req.query.day;
        request(config.server + "user/alter_user_info?user_id=" + userid +'&user_name=' + username + '&profile_picture=' +profile_picture + '&sex=' + sex + '&year=' + year + '&month=' + month + '&day=' + day,
            function(error, response, body) {
                if (!error && response.statusCode == 200) {
                    var result = JSON.parse(body);
                    if (result.ret == 200) {
                        res.send(result);
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

//发送验证邮箱
router.get('/verify', function(req, res, next) {
    req.session.user={
            "user_id": "174",
            "user_name": "三名哈哈呵呵",
            "user_email": "2660591904@qq.com"
        };
    if (req.session.user) {
        var usermail = req.session.user.user_email;
        request(config.server + "user/check_mail_1?user_email=" + usermail,
            function(error, response, body) {
                if (!error && response.statusCode == 200) {
                    var result = JSON.parse(body);
                    //console.log(result);
                    if (result.ret == 200 && result.data) {
                        res.send(result);
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
//验证邮箱页面
router.get('/verifyusermail',function(req,res,next){
    var agent = ua(req.headers['user-agent']);
    var page = agent.Mobile ? 'verifymailMobile' : 'verifymailMobile';
    res.render(page, {
                'result': 'result',
                'title': '个人中心'
            })
    /*request(config.server + "user/check_mail_2?user_id",
        function(error,response,body){
            if (!error && response.statusCode == 200) {
                    var result = JSON.parse(body);
                    //console.log(result);
                    if (result.ret == 200) {
                        res.render(page, {
                            'result': result,
                            'title': '个人中心',
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
            });*/
});
//确认是否验证邮箱
router.get('/verifycomfirm', function(req, res, next) {
    req.session.user={
            "user_id": "174",
            "user_name": "三名哈哈呵呵",
            "user_email": "2660591904@qq.com"
        };
    if (req.session.user) {
        var userid = req.session.user.user_id;
        request(config.server + "user/get_mail_checked?user_id=" + userid,
            function(error, response, body) {
                if (!error && response.statusCode == 200) {
                    var result = JSON.parse(body);
                    //console.log(result);
                    if (result.ret == 200 && result.data) {
                        res.send(result);
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
