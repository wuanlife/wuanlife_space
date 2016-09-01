var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* 重置密码：发送邮件 */
router.get('/', function(req, res, next) {
    var agent = ua(req.headers['user-agent']),
        email = req.flash('email').toString(),
        error = req.flash('error').toString(),
        verify = req.flash('verify').toString(),
        page;
    if (verify) {
        page = agent.Mobile ? 'resetPasswordMobile' : 'resetPassword';
    } else{
        page = agent.Mobile ? 'forgetMobile' : 'forget';
    }
    res.render(page, {'title':'重设密码','user':req.session.user,'email':email,'error':error});
});
router.post('/', function(req, res, next) {
    var email = req.body.email;
    request.post({
        url: config.server + '?service=User.SendMail',
        formData: {
            Email: req.body.email
        }
    }, function optionalCallback(err, httpResponse, body) {
        if (!err && httpResponse.statusCode == 200) {
            var result = JSON.parse(body);
            //console.log(result);
            if (result.ret == 200) {
                if (result.data.code == 1) {
                    req.flash('verify',true);
                } else{
                    req.flash('error',result.data.msg);
                }
                req.flash('email',email);
                res.redirect('back');
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
            next(err);
        }
    });
});

// 重置密码：更改密码
router.post('/set', function(req, res, next) {
    request.post({
        url: config.server + '?service=User.RePsw',
        formData: {
            Email: req.body.email,
            code:req.body.code,
            password:req.body.password,
            psw:req.body.psw
        }
    }, function optionalCallback(err, httpResponse, body) {
        if (err) {
            console.error('email send failed:', err);
            res.header('Content-type', 'application/json');
            res.header('Charset', 'utf8');
            res.send({
                err: err
            });
        }
       // console.log('Reset Password successful!  Server responded with:', body);
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        res.send(JSON.parse(body));
    });
});
module.exports = router;