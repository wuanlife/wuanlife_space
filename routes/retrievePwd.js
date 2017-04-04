var express = require('express');
var router = express.Router();
var request = require('request');
var ua = require('mobile-agent');
var config = require('../config/config');


router.get('/', function(req, res, next) {
	console.log('start bug');
    var agent = ua(req.headers['user-agent']);
    var page = agent.Mobile ? 'retrievePwdMobile' :'retrievePwd';
    try{
        res.render(page, {
            'title':'重置密码',
            'user' : req.session.user,
        });
    } catch(e){
        next(e);
    }     

});

//发送邮箱验证，用于更改密码
router.post('/',function(req, res, next) {
    console.log(config.server + "user/send_mail?user_email=" + req.body.email);
    request(config.server + "user/send_mail?user_email=" + req.body.email,
        function(error, httpResponse, body) {
            if (!error && httpResponse.statusCode == 200) {
                console.log('sendemail success!  Server responded with:', body);
                return res.send(JSON.parse(body));
            } else {
                console.log('sendemail error!  Server responded with:', body);
                try {
                    console.error('sendemail failed:', error.toString());
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                } catch(error) {
                    console.error('get sendemail exception:', error.toString());
                    res.send({
                        ret: 500,
                        msg:'服务器异常'
                    });
                }
            }
        }
    )

});


//更改密码
router.post('/reset', function(req, res, next) {

});

module.exports = router;