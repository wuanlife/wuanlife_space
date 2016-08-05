var express = require('express');
var router = express.Router();
var request = require('request');
var config = require('../config/config');
var ua = require('mobile-agent');

/* 重置密码：发送邮件 */
router.get('/', function(req, res, next) {
    var agent = ua(req.headers['user-agent']);
    var page = agent.Mobile ? 'resetPasswordMobile' : 'resetPassword';
    res.render(page, {'title':'重设密码','user':req.session.user});
});
router.post('/', function(req, res, next) {
    request.post({
        url: config.server + '?service=User.SendMail',
        formData: {
            Email: req.body.email
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
        //session保存用于找回密码邮箱
        if (JSON.parse(body).data.code == 1) {
            req.session.resetEmail = req.body.email;
        }
        console.log('Email send successful!  Server responded with:', body);
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        res.send(JSON.parse(body));
    });
});

// 重置密码：更改密码
router.post('/set', function(req, res, next) {
    request.post({
        url: config.server + '?service=User.RePsw',
        formData: {
            Email: req.session.resetEmail,
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
        console.log('Reset Password successful!  Server responded with:', body);
        res.header('Content-type', 'application/json');
        res.header('Charset', 'utf8');
        res.send(JSON.parse(body));
    });
});
module.exports = router;